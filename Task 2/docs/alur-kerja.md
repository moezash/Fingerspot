# Alur Kerja API dan Webhook Fingerspot

Dokumen ini menjelaskan lifecycle yang benar-benar diterapkan oleh project `Task2`. Aplikasi memakai API untuk request keluar, webhook untuk callback/event masuk, dan `trans_id` untuk menghubungkan command asynchronous dengan callback-nya.

## Gambaran Umum

```mermaid
flowchart LR
    App[PHP Application] -->|API request| Cloud[Fingerspot API / Cloud]
    Cloud --> Device[Physical Device]
    Device --> Cloud
    Cloud -->|HTTPS webhook| Ngrok[ngrok]
    Ngrok --> Hook[app/webhook.php]
    Hook --> DB[(Database)]
    DB --> Dashboard[Dashboard]
```

## 1. Flow Get Attlog

`get_attlog` adalah command synchronous di project ini.

```mermaid
sequenceDiagram
    participant App as PHP Application
    participant API as Fingerspot API
    participant DB as Database

    App->>DB: Insert api_requests + command_logs (pending)
    App->>API: POST get_attlog + trans_id
    API-->>App: HTTP response
    alt response success dan data valid
        App->>DB: save_attlogs() dengan insert idempotent
        App->>DB: command_logs = success
    else transport/API gagal
        App->>DB: command_logs = failed
    end
```

Flow ini tidak menunggu webhook. Webhook `attlog` adalah event realtime terpisah dan tidak boleh memfinalisasi `get_attlog`.

`restart` juga synchronous: acknowledgement HTTP API yang sukses cukup untuk memfinalisasi command. Callback restart opsional hanya dapat memperbarui row yang masih `pending`.

## 2. Flow Async Command

Command async saat ini adalah `get_userinfo`, `get_allpin`, `set_userinfo`, `delete_userinfo`, `set_time`, dan `reg_online`.

```mermaid
sequenceDiagram
    participant App as PHP Application
    participant DB as Database
    participant API as Fingerspot API
    participant Device as Device
    participant Hook as webhook.php

    App->>DB: Simpan trans_id dan status pending
    App->>API: Kirim command + cloud_id + trans_id
    API->>Device: Teruskan command
    Device-->>API: Hasil proses
    API-->>Hook: Callback webhook
    Hook->>DB: Cari pending berdasarkan trans_id, type, cloud_id
    alt callback berhasil
        Hook->>DB: status = success
    else callback menyatakan gagal
        Hook->>DB: status = failed
    end
```

Callback `get_userid_list` dipetakan ke command `get_allpin`. Callback duplikat atau terlambat tidak menimpa row yang sudah final karena matcher mensyaratkan `status='pending'`.

## 3. Flow Realtime Attendance

```mermaid
flowchart LR
    Scan[Fingerprint scan] --> Device[Device]
    Device --> Cloud[Fingerspot Cloud]
    Cloud --> Hook[Webhook type attlog]
    Hook --> Save[save_attlogs]
    Save --> Logs[(attlogs)]
```

Event `attlog` memvalidasi data scan lalu menyimpannya ke `attlogs`. Ia tidak memanggil penyelesaian `command_logs` dan tidak berhubungan dengan status command pull `get_attlog`.

## 4. Flow Pending Timeout

Default `COMMAND_PENDING_TIMEOUT_MINUTES` adalah 15 menit.

```mermaid
flowchart TD
    P[command_logs pending] --> A{Command async?}
    A -->|Tidak| Keep[Biarkan lifecycle synchronous]
    A -->|Ya| Age{Lebih tua dari timeout?}
    Age -->|Tidak| Wait[Tetap pending]
    Age -->|Ya| Fail[status = failed]
    Fail --> Note[notes: Command timed out waiting for webhook]
    Late[Late webhook] --> Guard{Status masih pending?}
    Guard -->|Tidak| Ignore[Tidak mengubah final state]
```

`expire_pending_commands()` dijalankan ketika dashboard atau riwayat command dibuka. Hanya enam tipe async yang masih `pending` yang dapat expired. Status `success`, `failed`, `get_attlog`, dan `restart` tidak diubah.

## 5. Flow Webhook Authentication

```mermaid
flowchart TD
    Req[POST webhook request] --> Secret{Secret dikonfigurasi?}
    Secret -->|Ya| Compare[Bandingkan secret dengan hash_equals]
    Compare -->|Tidak cocok| Reject[HTTP 401, tanpa write database]
    Compare -->|Cocok| Validate[Validasi JSON dan type]
    Secret -->|Tidak, development| Validate
    Validate --> Process[Log dan proses webhook]
```

Secret dapat diterima dari query parameter webhook atau header yang didukung source. Di production, konfigurasi tanpa webhook secret dianggap tidak siap. Webhook tidak memakai CSRF karena merupakan endpoint eksternal, tetapi form aplikasi internal tetap memakai CSRF token.

## 6. Flow Duplicate Attendance Prevention

```mermaid
flowchart LR
    Pull[get_attlog pull] --> Save[save_attlogs]
    Realtime[realtime attlog] --> Save
    Repeat[repeated range/request] --> Save
    Save --> Unique{Unique identity exists?}
    Unique -->|Belum| Insert[Insert row]
    Unique -->|Sudah| Ignore[Idempotent no-op]
```

Identitas unik absensi adalah:

```text
cloud_id + pin + scan_time + verify + status_scan
```

Database menerapkan `uniq_attlog_scan`, sedangkan `save_attlogs()` memakai `INSERT IGNORE`. Duplikat dianggap no-op yang valid, bukan processing failure.

## Webhook Type dan Mapping

| Webhook Type | Efek | Command Mapping |
|---|---|---|
| `attlog` | Menyimpan absensi realtime | Tidak ada |
| `get_userinfo` | Upsert data user | `get_userinfo` |
| `get_allpin` | Refresh daftar PIN | `get_allpin` |
| `get_userid_list` | Refresh daftar PIN | `get_allpin` |
| `set_userinfo` | Finalisasi hasil command | `set_userinfo` |
| `delete_userinfo` | Finalisasi hasil command | `delete_userinfo` |
| `set_time` | Finalisasi hasil command | `set_time` |
| `reg_online` | Finalisasi hasil command | `reg_online` |
| `restart` | Pending-only callback handling | `restart` |

## Penyimpanan dan Integrity Rules

| Table | Fungsi |
|---|---|
| `api_requests` | Audit request dan HTTP response API |
| `command_logs` | Lifecycle command dan alasan timeout |
| `webhook_responses` | Audit callback/event webhook |
| `attlogs` | Data absensi |
| `userinfos` | Data user per device |
| `pins` | Daftar PIN per device |

Constraint dan index dari migration aktual:

- `attlogs`: unique `(cloud_id, pin, scan_time, verify, status_scan)`;
- `userinfos`: unique `(cloud_id, pin)`;
- `pins`: unique `(cloud_id, pin)`;
- `command_logs`: index `(trans_id, status, command_type, cloud_id)` untuk matching callback.

Refresh `pins` dibungkus transaction sehingga kegagalan insert tidak meninggalkan daftar parsial.

## State Consistency

- API failure memfinalisasi command sebagai `failed`.
- API success memfinalisasi command synchronous segera.
- API success untuk command async membiarkannya `pending` sampai callback.
- Webhook hanya mengubah command yang masih `pending` dan sesuai mapping.
- Timeout mengubah pending async lama menjadi `failed` dengan catatan.
- Duplicate dan late callback tidak menimpa final state.

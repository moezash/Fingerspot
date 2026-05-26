# Eldev Project Context

## Project Overview

Eldev is a modern developer dashboard platform built for SDK/API exploration and demo purposes.

This project is part of a software engineering internship program focused on:

* API integration
* SDK implementation
* Developer experience
* Dashboard architecture
* Reusable frontend systems

The app should feel like:

* Vercel Dashboard
* Stripe Developer Portal
* Supabase Dashboard
* Postman

NOT like:

* generic admin template
* colorful startup landing page
* overdesigned UI

---

# Tech Stack

* Next.js App Router
* TypeScript
* Tailwind CSS
* shadcn/ui
* Lucide Icons
* React Query
* Axios

---

# Architecture Rules

* Keep code clean and modular
* Avoid overengineering
* Use reusable components
* Separate UI from API logic
* Use scalable folder structure
* Keep components small and maintainable
* Use TypeScript properly
* Avoid unnecessary abstraction

---

# Design Direction

* Minimalist
* Enterprise SaaS
* Spacious layout
* Modern developer tools aesthetic
* Dark mode friendly
* Clean typography
* Consistent spacing

---

# Current Focus

Current phase:
FOUNDATION UI ARCHITECTURE

Priority:

1. Dashboard shell
2. Sidebar
3. Navbar
4. Theme system
5. Reusable layout components

DO NOT:

* create API business logic yet
* create authentication system yet
* create backend logic yet
* overengineer state management

---

# Folder Structure

src/
├── app/
├── components/
│   ├── api/
│   ├── layout/
│   ├── shared/
│   └── ui/
├── services/
├── hooks/
├── providers/
├── config/
├── lib/
├── types/
├── constants/
├── styles/
└── utils/

---

# Coding Standards

* Use functional React components
* Use TypeScript everywhere
* Use Tailwind utility classes
* Keep files readable
* Prefer composition over complexity
* Use consistent naming
* Avoid giant files

---

# Important

Cursor should act as a senior frontend engineer assistant, not as a project architect.
Architecture decisions must remain consistent with this document.

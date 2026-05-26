"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

import { cn } from "@/lib/utils";

import {
  footerNavItems,
  navGroups,
  type NavGroup,
  type NavItem,
} from "./navigation";

type SidebarNavProps = {
  onNavigate?: () => void;
  className?: string;
  groups?: NavGroup[];
  footerItems?: NavItem[];
};

function NavLink({
  item,
  isActive,
  onNavigate,
}: {
  item: NavItem;
  isActive: boolean;
  onNavigate?: () => void;
}) {
  return (
    <Link
      href={item.href}
      onClick={onNavigate}
      className={cn(
        "group relative flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-all duration-150",
        isActive
          ? "bg-sidebar-accent text-sidebar-accent-foreground shadow-[inset_0_0_0_1px_var(--sidebar-border)]"
          : "text-sidebar-foreground/65 hover:bg-sidebar-accent/70 hover:text-sidebar-accent-foreground"
      )}
    >
      {isActive && (
        <span
          className="absolute top-1/2 left-0 h-4 w-0.5 -translate-y-1/2 rounded-full bg-sidebar-foreground"
          aria-hidden
        />
      )}
      <item.icon
        className={cn(
          "size-4 shrink-0 transition-colors",
          isActive
            ? "text-sidebar-foreground"
            : "text-sidebar-foreground/50 group-hover:text-sidebar-foreground/80"
        )}
      />
      {item.title}
    </Link>
  );
}

function isNavActive(pathname: string, href: string) {
  return pathname === href || pathname.startsWith(`${href}/`);
}

export function SidebarNav({
  onNavigate,
  className,
  groups = navGroups,
  footerItems = footerNavItems,
}: SidebarNavProps) {
  const pathname = usePathname();

  return (
    <div className={cn("flex flex-col", className)}>
      {groups.map((group) => (
        <div key={group.label ?? group.items[0]?.href} className="mb-5 last:mb-0">
          {group.label && (
            <p className="mb-2 px-3 text-[11px] font-medium tracking-wider text-sidebar-foreground/45 uppercase">
              {group.label}
            </p>
          )}
          <nav className="flex flex-col gap-0.5">
            {group.items.map((item) => (
              <NavLink
                key={item.href}
                item={item}
                isActive={isNavActive(pathname, item.href)}
                onNavigate={onNavigate}
              />
            ))}
          </nav>
        </div>
      ))}

      {footerItems.length > 0 && (
        <div className="mt-1 flex flex-col gap-0.5 border-t border-sidebar-border pt-4">
          {footerItems.map((item) => (
            <NavLink
              key={item.href}
              item={item}
              isActive={isNavActive(pathname, item.href)}
              onNavigate={onNavigate}
            />
          ))}
        </div>
      )}
    </div>
  );
}

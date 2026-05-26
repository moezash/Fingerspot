import {
  CalendarCheck,
  Code2,
  LayoutDashboard,
  Monitor,
  ScrollText,
  Settings,
  Shield,
  Users,
  type LucideIcon,
} from "lucide-react";

export type NavItem = {
  title: string;
  href: string;
  icon: LucideIcon;
};

export type NavGroup = {
  label?: string;
  items: NavItem[];
};

export const navGroups: NavGroup[] = [
  {
    label: "Platform",
    items: [
      { title: "Dashboard", href: "/dashboard", icon: LayoutDashboard },
      { title: "Authentication", href: "/authentication", icon: Shield },
      { title: "Employees", href: "/employees", icon: Users },
      { title: "Attendance", href: "/attendance", icon: CalendarCheck },
      { title: "Devices", href: "/devices", icon: Monitor },
      { title: "Logs", href: "/logs", icon: ScrollText },
    ],
  },
  {
    label: "Developer",
    items: [
      { title: "SDK Playground", href: "/sdk-playground", icon: Code2 },
    ],
  },
];

export const footerNavItems: NavItem[] = [
  { title: "Settings", href: "/settings/integration", icon: Settings },
];

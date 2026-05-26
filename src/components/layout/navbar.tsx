import { Search, User } from "lucide-react";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";

import { MobileSidebar } from "./mobile-sidebar";
import { ThemeToggle } from "./theme-toggle";

export function Navbar() {
  return (
    <header className="sticky top-0 z-40 flex h-[52px] shrink-0 items-center gap-3 border-b border-border bg-background/80 px-4 backdrop-blur-md supports-[backdrop-filter]:bg-background/60 lg:gap-4 lg:px-6">
      <div className="flex items-center gap-2 lg:hidden">
        <MobileSidebar />
        <Separator orientation="vertical" className="h-5" />
      </div>

      <div className="relative hidden min-w-0 flex-1 lg:block lg:max-w-sm xl:max-w-md">
        <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/70" />
        <Input
          type="search"
          placeholder="Search documentation, endpoints..."
          className="h-9 border-transparent bg-muted/50 pl-9 shadow-none placeholder:text-muted-foreground/60 focus-visible:border-input focus-visible:bg-background"
          readOnly
          aria-label="Search"
        />
      </div>

      <div className="ml-auto flex items-center gap-1">
        <Button
          variant="ghost"
          size="icon"
          className="size-9 text-muted-foreground lg:hidden"
          aria-label="Search"
        >
          <Search className="size-4" />
        </Button>

        <div className="flex size-9 shrink-0 items-center justify-center">
          <ThemeToggle />
        </div>

        <Separator orientation="vertical" className="mx-1 hidden h-5 sm:block" />

        <Button
          variant="ghost"
          size="icon"
          className="size-9 rounded-full border border-border bg-muted/30"
          aria-label="User menu"
        >
          <User className="size-4 text-muted-foreground" />
        </Button>
      </div>
    </header>
  );
}

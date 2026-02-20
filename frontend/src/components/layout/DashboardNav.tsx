"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

const NAV_ITEMS = [
  { id: 1, href: "/dashboard",    name: "Dashboard"    },
  { id: 2, href: "/workoutslog",  name: "Workout Logs" },
  { id: 3, href: "/nutrition",    name: "Nutrition"    },
  { id: 4, href: "/exercises",    name: "Exercises"    },
];

export default function DashboardNav() {
  const pathname = usePathname();

  return (
    <header
      className="sticky top-0 z-40 bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-800"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex h-14 items-center justify-center">
          <nav className="hidden md:flex items-center gap-1">
            {NAV_ITEMS.map((item) => {
              const isActive = pathname === item.href;
              return (
                <Link
                  key={item.id}
                  href={item.href}
                  className={`relative px-4 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition-all duration-200 ${
                    isActive
                      ? "text-lime-400 bg-lime-400/10"
                      : "text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800"
                  }`}
                >
                  
                  {isActive && (
                    <span className="absolute bottom-0 left-1/2 -translate-x-1/2 w-4 h-px bg-lime-400 rounded-full" />
                  )}
                  {item.name}
                </Link>
              );
            })}
          </nav>
        </div>
      </div>
    </header>
  );
}
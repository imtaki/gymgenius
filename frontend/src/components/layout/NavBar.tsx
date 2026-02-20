import Link from "next/link";
import { Dumbbell } from "lucide-react";

const NAV_ITEMS = [
  { id: 1, href: "#features",  name: "Features"  },
  { id: 2, href: "#benefits",  name: "Benefits"  },
  { id: 3, href: "#pricing",   name: "Pricing"   },
];

export default function NavBar() {
  return (
    <header
      className="sticky top-0 z-50 bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-800"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex h-16 items-center justify-between">

          
          <Link href="/" className="flex items-center gap-2.5 group">
            <div className="w-8 h-8 rounded-lg bg-lime-400 flex items-center justify-center group-hover:bg-lime-300 transition-colors duration-200">
              <Dumbbell className="h-4 w-4 text-zinc-900" />
            </div>
            <span className="text-sm font-bold text-zinc-100 uppercase tracking-widest">
              GymGenius
            </span>
          </Link>

          
          <nav className="hidden md:flex items-center gap-1">
            {NAV_ITEMS.map((item) => (
              <Link
                key={item.id}
                href={item.href}
                className="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-500 uppercase tracking-widest hover:text-zinc-100 hover:bg-zinc-800 transition-all duration-200"
              >
                {item.name}
              </Link>
            ))}
          </nav>

          
          <div className="flex items-center gap-2">
            <Link
              href="/login"
              className="px-4 py-2 text-xs font-semibold text-zinc-500 uppercase tracking-widest hover:text-zinc-200 transition-colors duration-200"
            >
              Login
            </Link>
            <Link
              href="/signup"
              className="px-4 py-2 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-lg text-xs font-bold uppercase tracking-widest transition-all duration-200 shadow-lg shadow-lime-400/10"
            >
              Sign Up
            </Link>
          </div>

        </div>
      </div>
    </header>
  );
}
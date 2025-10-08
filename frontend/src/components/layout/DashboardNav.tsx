import { Dumbbell } from "lucide-react";
import Link from "next/link";

export default function DashboardNav() {
    const navItems = [
      { id: 1, href: "dashboard", name: "Dashboard" },
      { id: 2, href: "workoutslog", name: "Workout Logs" },
      { id: 3, href: "nutrition", name: "Nutrition" },
      { id: 4, href: "exercises", name: "Exercises" }
    ]
    return (
      <header className="border-b border-white/10 bg-white backdrop-blur supports-[backdrop-filter]:bg-black/80 sticky top-0 z-40">
  <div className="container mx-auto px-4 sm:px-6 lg:px-8">
    <div className="flex h-16 items-center justify-between">
      <div className="flex items-center space-x-2 flex-shrink-0">
        <Dumbbell className="h-8 w-8 text-white" />
        <span className="text-2xl font-bold text-white">GymGenius</span>
      </div>
      <nav className="flex-1 flex items-center justify-center">
        <div className="hidden md:flex space-x-6">
          {navItems.map((item) =>
            <Link
              key={item.id}
              href={item.href}
              className="text-white/70 hover:text-white transition-colors"
            >
              {item.name}
            </Link>
          )}
        </div>
      </nav>
      <div className="w-8" /> {/* Placeholder for spacing */}
    </div>
  </div>
</header>
    )
}
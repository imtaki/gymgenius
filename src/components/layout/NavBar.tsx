import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button";
import { Dumbbell } from "lucide-react";
import Link from "next/link";

export default function NavBar() {
    const navItems = [
      { id: 1, href: "features", name: "Features" },
      { id: 2, href: "benefits", name: "Benefits" },
      { id: 3, href: "pricing", name: "Pricing" }
    ]
    return (
      <header className="border-b border-white/10  backdrop-blur supports-[backdrop-filter]: sticky top-0 z-50">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex h-16 items-center justify-between">
            <div className="flex items-center space-x-2">
              <Dumbbell className="h-8 w-8 text-white" />
              <span className="text-2xl font-bold text-white">GymGenius</span>
              <Badge variant="outline" className="hidden sm:inline-flex text-white border-white">AI Powered</Badge>
            </div>
            <nav className="hidden md:flex items-center space-x-6">
              {navItems.map((item) =>
                <Link key={item.id} href={item.href} className="text-white/60 hover:text-white transition-colors"
                >{item.name}</Link>
              )}
            </nav>
            <div className="flex items-center space-x-4">
              <Button variant="outline" size="sm" className="text-white border-white hover:bg-white hover:text-black">Sign In</Button>
              <Button size="sm" className="bg-white text-black hover:bg-white/90">Get Started</Button>
            </div>
          </div>
        </div>
      </header>
    )
}
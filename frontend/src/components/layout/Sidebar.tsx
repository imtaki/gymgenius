"use client";

import Link from "next/link";
import { useState, useEffect } from "react";
import { usePathname, useRouter } from "next/navigation";
import {
  LucideIcon,
  Menu,
  X,
  House,
  User,
  Dumbbell,
  CodeXml,
  LogOut,
} from "lucide-react";
import { getUser, logout } from "../../app/api/authService";
import { jwtDecode } from "jwt-decode";



interface SidebarLinkProps {
  href: string;
  label: string;
  icon: LucideIcon;
}



function SidebarLink({ href, label, icon: Icon }: SidebarLinkProps) {
  const pathname = usePathname();
  const isActive = pathname === href;

  return (
    <Link href={href} className="block w-full px-3">
      <div
        className={`relative flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-widest transition-all duration-200 cursor-pointer ${
          isActive
            ? "bg-lime-400/10 text-lime-400"
            : "text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800"
        }`}
      >
        
        {isActive && (
          <span className="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-lime-400 rounded-full" />
        )}
        <Icon className="h-4 w-4 shrink-0" />
        <span>{label}</span>
      </div>
    </Link>
  );
}



export default function SidebarClient() {
  const [isCollapsed, setIsCollapsed] = useState(true);
  const [isLoggingOut, setIsLoggingOut] = useState(false);
  const [role, setRole] = useState("user");
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const router = useRouter();

  const navItems = [
    { label: "Dashboard", href: "/dashboard", icon: House },
    { label: "Settings",  href: "/settings",  icon: User  },
  ];

  useEffect(() => {
    async function fetchUserRole() {
      try {
        const res = await getUser();
        const decoded = jwtDecode<{ role: string }>(res.token).role;
        if (res) {
          setRole(decoded);
          setIsLoggedIn(true);
        } else {
          setIsLoggedIn(false);
        }
      } catch {
        setIsLoggedIn(false);
      }
    }
    fetchUserRole();
  }, []);

  async function handleLogout() {
    try {
      setIsLoggingOut(true);
      await logout();
      router.push("/login");
    } catch (error) {
      console.error("Logout failed:", error);
      setIsLoggingOut(false);
    }
  }

  return (
    <>
      
      {isCollapsed && (
        <button
          className="absolute top-3 left-3 z-50 w-9 h-9 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-lime-400 hover:border-lime-400/30 transition-all duration-200"
          onClick={() => setIsCollapsed(false)}
          aria-label="Open menu"
        >
          <Menu size={16} />
        </button>
      )}

      
      {!isCollapsed && (
        <div
          className="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm"
          onClick={() => setIsCollapsed(true)}
        />
      )}

      
      <div
        className={`fixed top-0 left-0 h-screen z-40 flex flex-col bg-zinc-950 border-r border-zinc-800 shadow-2xl transition-all duration-300 overflow-hidden ${
          isCollapsed ? "w-0 opacity-0 pointer-events-none" : "w-64 opacity-100"
        }`}
        style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
      >
        
        <div className="flex items-center justify-between px-5 py-4 border-b border-zinc-800 shrink-0">
          <Link
            href="/"
            className="flex items-center gap-2.5 group"
            onClick={() => setIsCollapsed(true)}
          >
            <div className="w-7 h-7 rounded-lg bg-lime-400 flex items-center justify-center group-hover:bg-lime-300 transition-colors">
              <Dumbbell className="h-3.5 w-3.5 text-zinc-900" />
            </div>
            <span className="text-xs font-bold text-zinc-100 uppercase tracking-widest">
              GymGenius
            </span>
          </Link>

          <button
            className="w-8 h-8 flex items-center justify-center rounded-lg text-zinc-600 hover:text-zinc-200 hover:bg-zinc-800 transition-all duration-200"
            onClick={() => setIsCollapsed(true)}
            aria-label="Close menu"
          >
            <X size={14} />
          </button>
        </div>

       
        <nav className="flex-1 py-4 space-y-0.5 overflow-y-auto">
          {navItems.map((item) => (
            <SidebarLink key={item.href} {...item} />
          ))}

          {role === "admin" && (
            <SidebarLink href="/admin" label="Admin Panel" icon={CodeXml} />
          )}
        </nav>

        
        {isLoggedIn && (
          <div className="px-3 py-4 border-t border-zinc-800 shrink-0">
            <button
              onClick={handleLogout}
              disabled={isLoggingOut}
              className="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-widest text-zinc-500 hover:text-red-400 hover:bg-red-400/10 transition-all duration-200 disabled:opacity-50"
            >
              <LogOut className="h-4 w-4 shrink-0" />
              {isLoggingOut ? "Logging out…" : "Logout"}
            </button>
          </div>
        )}

      </div>
    </>
  );
}
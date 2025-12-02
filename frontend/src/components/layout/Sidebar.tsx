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
  Settings,
  Dumbbell,
  CodeXml,
} from "lucide-react";
import { getUser, logout } from "../../app/api/authService";
import { jwtDecode } from "jwt-decode";

interface SidebarLinkProps {
  href: string;
  label: string;
  icon: LucideIcon;
}

const SidebarLink = ({ href, label, icon: Icon }: SidebarLinkProps) => {
  const pathname = usePathname();
  const isActive = pathname === href;
  
  return (
    <Link href={href} className="w-full">
      <div
        className={`relative flex cursor-pointer items-center transition-colors
        ${isActive ? "bg-white/10" : "hover:bg-white/20"} justify-start px-8 py-3`}
      >
        {isActive && (
          <div className="absolute left-0 top-0 h-[100%] w-[5px] bg-white" />
        )}
        <Icon className="mr-2 h-5 w-5 text-white" />
        <span className="text-white">{label}</span>
      </div>
    </Link>
  );
};

export default function SidebarClient() {
  const [isCollapsed, setIsCollapsed] = useState(true);
  const [isLoggingOut, setIsLoggingOut] = useState(false);
  const [role, setRole] = useState("user");
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const router = useRouter();

  const navItems = [
    { label: "Dashboard", href: "/dashboard", icon: House },
    { label: "Settings", href: "/settings", icon: User },
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
      } catch (error) {
        console.error("Failed to fetch user role:", error);
        setIsLoggedIn(false);
      } 
    }

    fetchUserRole();
  }, []);

  async function handleLogout() {
    try {
      await logout();
      setIsLoggingOut(true);
      router.push("/login");
    } catch (error) {
      console.error("Logout failed:", error);
    }
  }

  return (
    <>
      {isCollapsed && (
        <button
          className="absolute top-3 left-3 p-2 bg-black text-white hover:bg-white hover:text-black rounded-md z-50 border border-white"
          onClick={() => setIsCollapsed(false)}
        >
          <Menu size={24} />
        </button>
      )}

      <div
        className={`flex flex-col h-screen justify-between shadow-xl
        transition-all duration-300 z-40 overflow-y-auto
        bg-black text-white
        ${isCollapsed ? "w-0 -translate-x-full" : "w-64 translate-x-0"}`}
      >
        <div className="flex h-full w-full flex-col justify-start">
          <div className="flex min-h-[56px] w-64 items-center justify-between px-6 pt-3">
            <div className="flex items-center space-x-2 flex-shrink-0 text-white">
              <Dumbbell className="h-8 w-8 text-white" />
              <span className="text-2xl font-bold text-white">
                <Link href="/">GymGenius</Link>
              </span>
            </div>
            {!isCollapsed && (
              <button
                className="p-2 text-white hover:bg-white hover:text-black rounded-md border border-white"
                onClick={() => setIsCollapsed(true)}
              >
                <X size={24} />
              </button>
            )}
          </div>

          <nav className="z-10 w-full mt-8">
            {navItems.map((item) => (
              <SidebarLink
                key={item.href}
                href={item.href}
                label={item.label}
                icon={item.icon}
              />
            ))}

            {role == "admin" && (
              <SidebarLink href="/admin" label="Admin Panel" icon={CodeXml} />
            )}

            {isLoggedIn && (
              <button 
                onClick={handleLogout} 
                className="w-full text-left"
                disabled={isLoggingOut}
              >
                <div className="relative flex cursor-pointer items-center justify-start px-8 py-3 hover:bg-white/20 transition-colors">
                  <User className="mr-2 h-5 w-5 text-white" />
                  <span className="text-white">
                    {isLoggingOut ? "Logging out..." : "Logout"}
                  </span>
                </div>
              </button>
            )}
          </nav>
        </div>
      </div>
    </>
  );
}
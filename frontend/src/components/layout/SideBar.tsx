"use client";

import Link from "next/link";
import {useState} from "react";
import { usePathname } from "next/navigation";
import { LucideIcon, Menu, X, House, BookOpen, User,Settings, CodeXml } from "lucide-react";

interface SidebarLinkProps {
  href: string;
  label: string;
  icon: LucideIcon,
}
const SidebarLink = ({ href, label, icon: Icon }: SidebarLinkProps) => {
  const pathname = usePathname();
  const isActive = pathname === href;
  return (
    <Link href={href} className="w-full">
      <div className={`relative flex cursor-pointer items-center transition-colors
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

export default function Sidebar() {
  const [isCollapsed, setIsCollapsed] = useState(true);
  const navItems = [
    { label: "Home", href: "/dashboard", icon: House },
    { label: "My Sessions", href: "/sessions", icon: CodeXml },
    { label: "Profile", href: "/profile", icon: User },
    { label: "Settings", href: "/settings", icon: Settings },
    { label: "Docs", href: "/docs", icon: BookOpen },
  ];
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

      <div className={`flex flex-col h-screen justify-between shadow-xl
        transition-all duration-300 z-40 overflow-y-auto
        bg-black text-white
        ${isCollapsed ? "w-0 -translate-x-full" : "w-64 translate-x-0"}`}
      >
        <div className="flex h-full w-full flex-col justify-start">
          <div className="flex min-h-[56px] w-64 items-center justify-between px-6 pt-3">
            <div className="text-xl font-bold text-white">
              Menu
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
              <SidebarLink key={item.href} href={item.href} label={item.label} icon={item.icon} />
            ))}
          </nav>
        </div>
      </div>
    </>
  );
}
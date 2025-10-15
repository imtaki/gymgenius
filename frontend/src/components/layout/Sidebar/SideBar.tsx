import { cookies } from "next/headers";
import SidebarClient from "./SideBarClient";

export default async function Sidebar() {
  const cookieStore = await cookies();
  const token = cookieStore.get("token")?.value;
  const role = cookieStore.get("role")?.value;

  const isLoggedIn = !!token;

  return <SidebarClient isLoggedIn={isLoggedIn} role={role || null} />;
}
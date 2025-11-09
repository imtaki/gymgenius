import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

export function middleware(req: NextRequest) {
  const token = req.cookies.get("jwt_token")?.value;
  const role = req.cookies.get("role")?.value;

  if (!token) {
    return NextResponse.redirect(new URL("/login", req.url));
  }

  if (role !== "admin") {
    return NextResponse.redirect(new URL("/dashboard", req.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: [
      "/admin/:path*",
      "/dashboard/:path*",
  ],
};
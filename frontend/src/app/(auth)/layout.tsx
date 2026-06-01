import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "GymGenius - Health & Fitness Tracker",
  description: "Get fit with GymGenius - your ultimate health & fitness tracker.",
};

export default function AuthLayout({ children }: { children: React.ReactNode }) {
  // Nested layouts must not render <html> or <body> — root layout provides them.
  return <>{children}</>;
}

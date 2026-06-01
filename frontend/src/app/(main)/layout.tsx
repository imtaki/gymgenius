import type { Metadata } from "next";
import Footer from "../../components/layout/Footer";
import DashboardNav from "../../components/layout/DashboardNav";
import Sidebar from "../../components/layout/Sidebar";
import AuthGuard from "../../components/hooks/AuthGuard";

export const metadata: Metadata = {
  title: "GymGenius - Health & Fitness Tracker",
  description: "Get fit with GymGenius - your ultimate health & fitness tracker.",
};

const DashboardLayout = ({ children }: { children: React.ReactNode }) => {
  return (
    <>
      <div className="flex min-h-screen w-full z-50">
        <Sidebar />
        <main className="flex-1 flex flex-col">
          <AuthGuard>
            <DashboardNav />
            <div className="flex-1 overflow-y-auto p-6 md:pl-16">{children}</div>
          </AuthGuard>
        </main>
      </div>
      <Footer />
    </>
  );
};

export default function MainLayout({ children }: { children: React.ReactNode }) {
  // Nested layouts should not render <html> or <body>; the root layout provides them.
  return <DashboardLayout>{children}</DashboardLayout>;
}
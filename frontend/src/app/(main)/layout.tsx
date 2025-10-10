import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import Footer from "../../components/layout/Footer";
import DashboardNav from "../../components/layout/DashboardNav";
import Sidebar from "../../components/layout/SideBar";


const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

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
        <DashboardNav />
        <div className="flex-1 overflow-y-auto p-6 md:pl-16">
          {children}
        </div>
      </main>
    </div>
    <Footer />
    </>
    
  );
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="dark">
      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased`}
      >
        <DashboardLayout>{children}</DashboardLayout>
      </body>
    </html>
  );
}
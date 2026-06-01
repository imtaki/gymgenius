import type { Metadata } from "next";
import Footer from "../../components/layout/Footer";
import NavBar from "../../components/layout/NavBar";

export const metadata: Metadata = {
  title: "GymGenius - Health & Fitness Tracker",
  description: "Get fit with GymGenius - your ultimate health & fitness tracker.",
};

export default function LandingLayout({ children }: { children: React.ReactNode }) {
  // Do not render <html>/<body> in nested layouts — root layout handles them.
  return (
    <>
      <NavBar />
      {children}
      <Footer />
    </>
  );
}
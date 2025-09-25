import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { 
  TrendingUp, 
  ChevronRight,
} from "lucide-react";
import Hero from "@/components/sections/Hero";
import Features from "@/components/sections/Features";
import Benefits from "@/components/sections/Benefits";

export default function Page() {
  return (
    <div className="min-h-screen">
      <Hero />
      <Features />
      <Benefits />
    </div>
  );
};

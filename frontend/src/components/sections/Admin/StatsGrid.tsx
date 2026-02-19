import { useEffect, useState } from "react";
import axiosInstance from "../../../app/api/axios";
import { StatCard } from "./ui-elements";
import { CreditCard, Dumbbell, Users, UtensilsCrossed } from "lucide-react";
import { StatsData } from "../../../types/types";

export function StatsGrid() {
  const [statsData, setStatsData] = useState<StatsData>({ 
    user_count: 0,
    meal_logs_count: 0 
    });

  async function fetchStats() {
    try {
      const response = await axiosInstance.get("/users/data/count");
      setStatsData(response.data);
    } catch (error) {
      console.error("Error fetching stats:", error);
    }
  }

  useEffect(() => {
    fetchStats();
  }, []);

  const STATS = [
    { label: "Total Users", value: statsData.user_count?.toLocaleString(), change: "+8.2%", up: true, icon: Users, color: "from-violet-500 to-purple-600" },
    { label: "Active Subscriptions", value: "4,231", change: "+12.5%", up: true, icon: CreditCard, color: "from-emerald-500 to-teal-600" },
    { label: "Workouts Logged", value: "89,204", change: "+3.1%", up: true, icon: Dumbbell, color: "from-orange-500 to-red-500" },
    { label: "Meal Logs Today", value: statsData.meal_logs_count?.toLocaleString() || "0", change: "-1.4%", up: false, icon: UtensilsCrossed, color: "from-sky-500 to-blue-600" },
  ];

  return (
    <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
      {STATS.map((stat) => (
        <StatCard key={stat.label} stat={stat} />
      ))}
    </div>
  );
}
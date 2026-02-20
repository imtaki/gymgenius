"use client";
import { Dumbbell } from "lucide-react";
import { ChartsSection } from "./ChartSection";
import { SectionHeader } from "./ui-elements";
import { PLAN_COLOR, AVATAR_COLORS } from "../../../lib/admin/adminDataConstants";
import { WORKOUTS } from "../../../lib/admin/adminMockData";
import { useEffect, useState } from "react";
import axiosInstance from "../../../app/api/axios";
import { RecentUsers } from "../../../types/types";
import { RecentUserSkeleton } from "../../skeletons/OverviewTabSkeletons";

export function OverviewTab() {
  return (
    <div className="space-y-5">
      <ChartsSection />
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <RecentUsersCard />
        <TopWorkoutsCard />
      </div>
    </div>
  );
}

  

function RecentUsersCard() {
  const [userData, setUserData] = useState<RecentUsers[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await axiosInstance.get("/users/data/recent");
        setUserData(response.data);
      } catch (error) {
        console.error("Error fetching stats:", error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchStats();
  }, []);

  
  const getInitials = (name = "") => 
    name.split(" ").map(n => n[0]).join("").toUpperCase();

  
  const formatDate = (dateString: string) => 
    new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Recent Users" />
      <div className="space-y-2">
        {isLoading ? (
          // Skeletons
          Array.from({ length: 5 }).map((_, i) => <RecentUserSkeleton key={i} />)
        ) : (
          userData.map((user, i) => (
            <div key={user.id} className="flex items-center justify-between py-1.5 animate-in fade-in duration-500">
              <div className="flex items-center gap-3">
                <div className={`w-8 h-8 rounded-full ${AVATAR_COLORS[i % AVATAR_COLORS.length]} flex items-center justify-center text-xs font-bold text-white`}>
                  {getInitials(user.name)}
                </div>
                <div>
                  <p className="text-sm font-medium text-white">{user.name}</p>
                  <p className="text-xs text-slate-500">{formatDate(user.created_at)}</p>
                </div>
              </div>
              <span className={`text-xs font-semibold px-2 py-0.5 rounded-full border ${PLAN_COLOR[user.plan || "Free"]}`}>
                {user.plan || "Free"}
              </span>
            </div>
          ))
        )}

        {!isLoading && userData.length === 0 && (
          <p className="text-xs text-slate-500 text-center py-4">No recent users found.</p>
        )}
      </div>
    </div>
  );
}

function TopWorkoutsCard() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Top Workouts" />
      <div className="space-y-3">
        {WORKOUTS.slice(0, 5).map((workout) => (
          <div key={workout.id} className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center text-xs font-bold text-white">
                <Dumbbell size={13} className="text-orange-400" />
              </div>
              <span className="text-sm text-white font-medium">{workout.name}</span>
            </div>
            <div className="flex items-center gap-3">
              <span className="text-xs text-slate-500 font-mono">{workout.users.toLocaleString()}</span>
              <span className="text-xs text-amber-400">★ {workout.rating}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
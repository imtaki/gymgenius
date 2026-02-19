"use client";
import { Dumbbell } from "lucide-react";
import { ChartsSection } from "./ChartSection";
import { SectionHeader } from "./ui-elements";
import { PLAN_COLOR, AVATAR_COLORS } from "../../../lib/admin/adminDataConstants";
import { USERS, WORKOUTS } from "../../../lib/admin/adminMockData";

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
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Recent Users" />
      <div className="space-y-2">
        {USERS.slice(0, 4).map((user, i) => (
          <div key={user.id} className="flex items-center justify-between py-1.5">
            <div className="flex items-center gap-3">
              <div className={`w-8 h-8 rounded-full ${AVATAR_COLORS[i]} flex items-center justify-center text-xs font-bold text-white`}>
                {user.avatar}
              </div>
              <div>
                <p className="text-sm font-medium text-white">{user.name}</p>
                <p className="text-xs text-slate-500">{user.joined}</p>
              </div>
            </div>
            <span className={`text-xs font-semibold px-2 py-0.5 rounded-full border ${PLAN_COLOR[user.plan]}`}>
              {user.plan}
            </span>
          </div>
        ))}
      </div>
    </div>
  );
}

function TopWorkoutsCard() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Top Workouts" />
      <div className="space-y-3">
        {WORKOUTS.slice(0, 4).map((workout) => (
          <div key={workout.id} className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <div className="w-7 h-7 rounded-lg bg-orange-500/20 flex items-center justify-center">
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
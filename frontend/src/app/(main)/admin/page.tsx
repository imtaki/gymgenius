"use client";

import { useState } from "react";

import { AdminNav } from "../../../components/sections/Admin/AdminNav";
import { StatsGrid } from "../../../components/sections/Admin/StatsGrid";
import { OverviewTab } from "../../../components/sections/Admin/OverviewTab";
import { UsersTable } from "../../../components/sections/Admin/UserTable";
import { WorkoutsTable } from "../../../components/sections/Admin/WorkoutsTable";
import { MealLogsTable } from "../../../components/sections/Admin/MealLogTable";
import { ChartsSection } from "../../../components/sections/Admin/ChartSection";
import { SubscriptionsSection } from "../../../components/sections/Admin/SubscriptionSection";
import { TABS } from "../../../lib/admin/adminDataConstants";

export default function AdminPanel() {
  const [activeTab, setActiveTab] = useState("overview");

  const activeLabel = TABS.find((t) => t.id === activeTab)?.label ?? "";

  return (
    <div
      className="min-h-screen bg-[#0a0c10] text-white"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      {/* ── Navigation ── */}
      <AdminNav activeTab={activeTab} onTabChange={setActiveTab} />

      {/* ── Main content ── */}
      <div className="p-6 space-y-5 max-w-[1400px] mx-auto">

        {/* Page heading */}
        <div>
          <h1 className="text-xl font-bold text-white tracking-tight">{activeLabel}</h1>
          <p className="text-xs text-slate-500 mt-0.5">
            {new Date().toLocaleDateString("en-US", {
              weekday: "long",
              year:    "numeric",
              month:   "long",
              day:     "numeric",
            })}
          </p>
        </div>

        {/* Stats row — always visible */}
        <StatsGrid />

        {/* Tab views */}
        {activeTab === "overview"       && <OverviewTab />}
        {activeTab === "users"          && <UsersTable />}
        {activeTab === "workouts"       && <WorkoutsTable />}
        {activeTab === "meals"          && <MealLogsTable />}
        {activeTab === "subscriptions"  && (
          <div className="space-y-5">
            <SubscriptionsSection />
            <ChartsSection />
          </div>
        )}

      </div>
    </div>
  );
}
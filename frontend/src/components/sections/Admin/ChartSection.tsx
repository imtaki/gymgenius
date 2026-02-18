import { TrendingUp, Activity, Target, Calendar, Flame } from "lucide-react";
import { CHART_DATA } from "../../../lib/admin/adminMockData";

const MAX_USERS    = Math.max(...CHART_DATA.map((d) => d.users));
const MAX_WORKOUTS = Math.max(...CHART_DATA.map((d) => d.workouts));

const ACTIVITY_STATS = [
  { label: "Avg/User", value: "6.9", icon: Target,   color: "text-violet-400" },
  { label: "Peak Day", value: "Sat", icon: Calendar, color: "text-orange-400" },
  { label: "Streak",   value: "11d", icon: Flame,    color: "text-red-400"    },
];

export function ChartsSection() {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <UserGrowthChart />
      <ActivityOverviewChart />
    </div>
  );
}

function UserGrowthChart() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h2 className="text-sm font-semibold text-white uppercase tracking-widest">User Growth</h2>
          <p className="text-xs text-slate-500 mt-0.5">Past 7 months</p>
        </div>
        <div className="flex items-center gap-1.5 text-xs text-emerald-400 font-semibold bg-emerald-400/10 px-2.5 py-1 rounded-full">
          <TrendingUp size={12} />
          +56.7%
        </div>
      </div>

      <div className="flex items-end justify-between gap-1.5 h-32">
        {CHART_DATA.map((d, i) => {
          const heightPct = (d.users / MAX_USERS) * 100;
          const isLatest  = i === CHART_DATA.length - 1;
          return (
            <div key={d.month} className="flex-1 flex flex-col items-center gap-1.5 group">
              <div className="w-full flex items-end" style={{ height: "100px" }}>
                <div
                  className={`w-full rounded-t-lg transition-all duration-300 group-hover:brightness-125 ${
                    isLatest
                      ? "bg-gradient-to-t from-violet-600 to-violet-400"
                      : "bg-violet-900/60 border border-violet-500/20"
                  }`}
                  style={{ height: `${heightPct}%` }}
                />
              </div>
              <span className="text-xs text-slate-600 group-hover:text-slate-400 transition-colors">
                {d.month}
              </span>
            </div>
          );
        })}
      </div>
    </div>
  );
}

function ActivityOverviewChart() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h2 className="text-sm font-semibold text-white uppercase tracking-widest">Activity Overview</h2>
          <p className="text-xs text-slate-500 mt-0.5">Workouts logged per month</p>
        </div>
        <div className="flex items-center gap-1.5 text-xs text-orange-400 font-semibold bg-orange-400/10 px-2.5 py-1 rounded-full">
          <Activity size={12} />
          Live
        </div>
      </div>

      <div className="space-y-3">
        {CHART_DATA.slice(-4).map((d) => {
          const pct = Math.round((d.workouts / MAX_WORKOUTS) * 100);
          return (
            <div key={d.month} className="flex items-center gap-3">
              <span className="text-xs text-slate-500 w-8">{d.month}</span>
              <div className="flex-1 h-2 bg-white/[0.04] rounded-full overflow-hidden">
                <div
                  className="h-full rounded-full bg-gradient-to-r from-orange-500 to-red-500 transition-all duration-700"
                  style={{ width: `${pct}%` }}
                />
              </div>
              <span className="text-xs text-white font-mono w-16 text-right">
                {d.workouts.toLocaleString()}
              </span>
            </div>
          );
        })}
      </div>

      <div className="mt-4 pt-4 border-t border-white/[0.05] grid grid-cols-3 gap-3">
        {ACTIVITY_STATS.map((s) => (
          <div key={s.label} className="text-center">
            <s.icon size={14} className={`${s.color} mx-auto mb-1`} />
            <p className={`text-base font-bold font-mono ${s.color}`}>{s.value}</p>
            <p className="text-xs text-slate-600">{s.label}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
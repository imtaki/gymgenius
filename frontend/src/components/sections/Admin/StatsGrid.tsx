import { StatCard } from "./ui-elements";
import { STATS } from "../../../lib/admin/adminMockData";;

export function StatsGrid() {
  return (
    <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
      {STATS.map((stat) => (
        <StatCard key={stat.label} stat={stat} />
      ))}
    </div>
  );
}
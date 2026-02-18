import { Flame } from "lucide-react";
import { SectionHeader, SearchBar, UserAvatar } from "./ui-elements";
import { MEALS} from "../../../lib/admin/adminMockData";

const TABLE_HEADERS = ["User", "Meal", "Foods", "Calories", "Protein", "Time", "Date"];

function getInitials(name) {
  return name.split(" ").map((n) => n[0]).join("");
}

export function MealLogsTable() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Daily Meal Logs" />
      <SearchBar placeholder="Search meal logs…" />

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-white/[0.05]">
              {TABLE_HEADERS.map((h) => (
                <th key={h} className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider pb-3 pr-4">
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-white/[0.03]">
            {MEALS.map((entry, i) => (
              <MealRow key={entry.id} entry={entry} index={i} />
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function MealRow({ entry, index }) {
  return (
    <tr className="group hover:bg-white/[0.02] transition-colors">

      {/* User */}
      <td className="py-3.5 pr-4">
        <div className="flex items-center gap-2">
          <UserAvatar initials={getInitials(entry.user)} index={index} size="sm" />
          <span className="text-white font-medium text-sm">{entry.user}</span>
        </div>
      </td>

      {/* Meal type */}
      <td className="py-3.5 pr-4">
        <span className="text-xs px-2.5 py-1 rounded-full bg-sky-400/10 text-sky-400 font-medium">
          {entry.meal}
        </span>
      </td>

      {/* Foods */}
      <td className="py-3.5 pr-4 max-w-[180px]">
        <span className="text-slate-400 text-xs truncate block">{entry.foods}</span>
      </td>

      {/* Calories */}
      <td className="py-3.5 pr-4">
        <div className="flex items-center gap-1">
          <Flame size={12} className="text-orange-400" />
          <span className="text-white font-mono font-medium">{entry.calories}</span>
        </div>
      </td>

      {/* Protein */}
      <td className="py-3.5 pr-4">
        <span className="text-emerald-400 font-mono font-medium">{entry.protein}</span>
      </td>

      {/* Time */}
      <td className="py-3.5 pr-4">
        <span className="text-slate-400 text-xs">{entry.logged}</span>
      </td>

      {/* Date */}
      <td className="py-3.5">
        <span className={`text-xs font-medium ${entry.date === "Today" ? "text-violet-400" : "text-slate-500"}`}>
          {entry.date}
        </span>
      </td>

    </tr>
  );
}
import { Dumbbell, Edit2, Trash2 } from "lucide-react";
import { SectionHeader, SearchBar } from "./ui-elements";
import { DIFF_COLOR, CAT_COLOR } from "../../../lib/admin/adminDataConstants";
import { WORKOUTS } from "../../../lib/admin/adminMockData";

export function WorkoutsTable() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Workout Library" action="Add Workout" />
      <SearchBar placeholder="Search workouts…" />

      <div className="space-y-2">
        {WORKOUTS.map((workout) => (
          <WorkoutRow key={workout.id} workout={workout} />
        ))}
      </div>
    </div>
  );
}

function WorkoutRow({ workout }) {
  return (
    <div className="flex items-center justify-between p-3.5 rounded-xl bg-[#0d0f13] border border-white/[0.04] hover:border-white/[0.1] group transition-all">

      {/* Icon + name + tags */}
      <div className="flex items-center gap-3">
        <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center shrink-0">
          <Dumbbell size={16} className="text-white" />
        </div>
        <div>
          <p className="font-medium text-white text-sm">{workout.name}</p>
          <div className="flex items-center gap-2 mt-0.5">
            <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${CAT_COLOR[workout.category]}`}>
              {workout.category}
            </span>
            <span className="text-xs text-slate-500">{workout.duration}</span>
          </div>
        </div>
      </div>

      {/* Meta + actions */}
      <div className="flex items-center gap-6">
        <span className={`text-xs px-2.5 py-1 rounded-full font-medium ${DIFF_COLOR[workout.difficulty]}`}>
          {workout.difficulty}
        </span>

        <div className="text-right">
          <p className="text-sm font-semibold text-white font-mono">{workout.users.toLocaleString()}</p>
          <p className="text-xs text-slate-500">users</p>
        </div>

        <div className="text-right">
          <p className="text-sm font-semibold text-amber-400">★ {workout.rating}</p>
          <p className="text-xs text-slate-500">rating</p>
        </div>

        <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <button className="p-1.5 text-slate-500 hover:text-violet-400 hover:bg-violet-400/10 rounded-lg transition-all">
            <Edit2 size={14} />
          </button>
          <button className="p-1.5 text-slate-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all">
            <Trash2 size={14} />
          </button>
        </div>
      </div>

    </div>
  );
}
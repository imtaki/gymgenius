"use client";

import { useState } from "react";
import {
  Dumbbell, Plus, Calendar, Clock, Flame, ChevronDown,
  ChevronUp, Filter, Search, TrendingUp, Zap, MoreHorizontal,
  CheckCircle2, Circle, Trash2, Edit2, BarChart3, ArrowUpRight,
} from "lucide-react";
import BackButton from "../../../components/ui/backbutton";


interface Set {
  reps: number;
  weight: number;
  done: boolean;
}

interface Exercise {
  id: number;
  name: string;
  sets: Set[];
  category: "Strength" | "Cardio" | "Flexibility";
}

interface WorkoutSession {
  id: number;
  name: string;
  date: string;
  duration: string;
  calories: number;
  volume: number;
  split: string;
  exercises: Exercise[];
  completed: boolean;
}



const SESSIONS: WorkoutSession[] = [
  {
    id: 1,
    name: "Push Day",
    date: "Today",
    duration: "65 min",
    calories: 480,
    volume: 8400,
    split: "PPL",
    completed: true,
    exercises: [
      { id: 1, name: "Bench Press",        category: "Strength",    sets: [{ reps: 8, weight: 100, done: true }, { reps: 8, weight: 100, done: true }, { reps: 6, weight: 105, done: true }] },
      { id: 2, name: "Overhead Press",     category: "Strength",    sets: [{ reps: 10, weight: 60, done: true }, { reps: 9, weight: 60, done: true }, { reps: 8, weight: 62.5, done: true }] },
      { id: 3, name: "Incline Dumbbell",   category: "Strength",    sets: [{ reps: 12, weight: 36, done: true }, { reps: 11, weight: 36, done: true }, { reps: 10, weight: 38, done: true }] },
      { id: 4, name: "Tricep Pushdowns",   category: "Strength",    sets: [{ reps: 15, weight: 40, done: true }, { reps: 15, weight: 42, done: true }, { reps: 12, weight: 45, done: true }] },
    ],
  },
  {
    id: 2,
    name: "Pull Day",
    date: "Yesterday",
    duration: "58 min",
    calories: 420,
    volume: 7600,
    split: "PPL",
    completed: true,
    exercises: [
      { id: 5, name: "Deadlift",           category: "Strength",    sets: [{ reps: 5, weight: 160, done: true }, { reps: 5, weight: 160, done: true }, { reps: 3, weight: 170, done: true }] },
      { id: 6, name: "Barbell Row",        category: "Strength",    sets: [{ reps: 8, weight: 90, done: true }, { reps: 8, weight: 90, done: true }, { reps: 7, weight: 92.5, done: true }] },
      { id: 7, name: "Pull-ups",           category: "Strength",    sets: [{ reps: 10, weight: 0, done: true }, { reps: 9, weight: 0, done: true }, { reps: 8, weight: 0, done: true }] },
      { id: 8, name: "Face Pulls",         category: "Strength",    sets: [{ reps: 15, weight: 30, done: true }, { reps: 15, weight: 32, done: true }] },
    ],
  },
  {
    id: 3,
    name: "Leg Day",
    date: "2 days ago",
    duration: "72 min",
    calories: 610,
    volume: 11200,
    split: "PPL",
    completed: true,
    exercises: [
      { id: 9,  name: "Squat",             category: "Strength",    sets: [{ reps: 6, weight: 140, done: true }, { reps: 6, weight: 140, done: true }, { reps: 5, weight: 145, done: true }] },
      { id: 10, name: "Romanian Deadlift", category: "Strength",    sets: [{ reps: 10, weight: 100, done: true }, { reps: 10, weight: 100, done: true }, { reps: 8, weight: 105, done: true }] },
      { id: 11, name: "Leg Press",         category: "Strength",    sets: [{ reps: 12, weight: 200, done: true }, { reps: 12, weight: 210, done: true }, { reps: 10, weight: 220, done: true }] },
      { id: 12, name: "Calf Raises",       category: "Strength",    sets: [{ reps: 20, weight: 80, done: true }, { reps: 20, weight: 80, done: true }, { reps: 18, weight: 85, done: true }] },
    ],
  },
  {
    id: 4,
    name: "Upper Body",
    date: "4 days ago",
    duration: "55 min",
    calories: 390,
    volume: 6800,
    split: "Upper/Lower",
    completed: true,
    exercises: [
      { id: 13, name: "Dumbbell Press",    category: "Strength",    sets: [{ reps: 10, weight: 40, done: true }, { reps: 9, weight: 42, done: true }, { reps: 8, weight: 44, done: true }] },
      { id: 14, name: "Lat Pulldown",      category: "Strength",    sets: [{ reps: 12, weight: 70, done: true }, { reps: 11, weight: 72, done: true }, { reps: 10, weight: 75, done: true }] },
      { id: 15, name: "Lateral Raises",   category: "Strength",    sets: [{ reps: 15, weight: 12, done: true }, { reps: 15, weight: 14, done: true }, { reps: 12, weight: 14, done: true }] },
    ],
  },
];

const WEEKLY_STATS = [
  { label: "Sessions",     value: "4",       sub: "this week",      icon: Dumbbell,    accent: "text-lime-400",    bg: "bg-lime-400/10"    },
  { label: "Volume",       value: "34.0k",   sub: "kg total",       icon: BarChart3,   accent: "text-sky-400",     bg: "bg-sky-400/10"     },
  { label: "Calories",     value: "1,900",   sub: "kcal burned",    icon: Flame,       accent: "text-orange-400",  bg: "bg-orange-400/10"  },
  { label: "Streak",       value: "12",      sub: "day streak",     icon: Zap,         accent: "text-fuchsia-400", bg: "bg-fuchsia-400/10" },
];

const SPLIT_COLOR: Record<string, string> = {
  PPL:           "text-lime-400   bg-lime-400/10   border-lime-400/20",
  "Upper/Lower": "text-sky-400    bg-sky-400/10    border-sky-400/20",
  "Full Body":   "text-amber-400  bg-amber-400/10  border-amber-400/20",
};



function StatCard({ stat }: { stat: typeof WEEKLY_STATS[0] }) {
  return (
    <div className={`group relative bg-zinc-900 border border-zinc-800 rounded-2xl p-5 hover:border-zinc-700 transition-all duration-300`}>
      <div className="absolute top-0 inset-x-0 h-px rounded-t-2xl bg-gradient-to-r from-transparent via-current to-transparent opacity-0 group-hover:opacity-30 transition-opacity duration-500" style={{ color: "inherit" }} />
      <div className="flex items-start justify-between mb-3">
        <div className={`w-9 h-9 rounded-xl ${stat.bg} ${stat.accent} flex items-center justify-center`}>
          <stat.icon className="w-4 h-4" />
        </div>
        <ArrowUpRight className="w-3.5 h-3.5 text-zinc-700 group-hover:text-zinc-500 transition-colors" />
      </div>
      <p className={`text-2xl font-black font-mono tracking-tight ${stat.accent}`}>{stat.value}</p>
      <p className="text-xs text-zinc-600 uppercase tracking-widest mt-1">{stat.label}</p>
      <p className="text-xs text-zinc-700 font-mono mt-0.5">{stat.sub}</p>
    </div>
  );
}

function ExerciseRow({ exercise }: { exercise: Exercise }) {
  const totalVol = exercise.sets.reduce((s, set) => s + set.reps * (set.weight || 1), 0);
  return (
    <div className="flex items-center justify-between py-2.5 border-b border-zinc-800/50 last:border-0">
      <div className="flex items-center gap-3">
        <div className="w-1.5 h-1.5 rounded-full bg-lime-400/50" />
        <span className="text-sm text-zinc-200 font-mono">{exercise.name}</span>
      </div>
      <div className="flex items-center gap-4 text-xs font-mono text-zinc-500">
        <span>{exercise.sets.length} sets</span>
        <span className="text-zinc-700">·</span>
        <span className="text-zinc-400">{totalVol.toLocaleString()} kg vol</span>
      </div>
    </div>
  );
}

function SessionCard({ session }: { session: WorkoutSession }) {
  const [expanded, setExpanded] = useState(false);

  return (
    <div className="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-zinc-700 transition-all duration-200 group">

      
      <button
        onClick={() => setExpanded(!expanded)}
        className="w-full text-left p-5"
      >
        <div className="flex items-start justify-between">
          <div className="flex items-start gap-3">
            {/* Icon */}
            <div className="w-10 h-10 rounded-xl bg-zinc-800 group-hover:bg-zinc-700 flex items-center justify-center transition-colors shrink-0">
              <Dumbbell className="w-4 h-4 text-lime-400" />
            </div>

            {/* Title + meta */}
            <div>
              <div className="flex items-center gap-2 flex-wrap">
                <h3 className="text-sm font-bold text-zinc-100 uppercase tracking-widest">{session.name}</h3>
                <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full border font-mono ${SPLIT_COLOR[session.split] ?? "text-zinc-400 bg-zinc-800 border-zinc-700"}`}>
                  {session.split}
                </span>
                {session.completed && (
                  <span className="flex items-center gap-1 text-[10px] text-emerald-400 font-mono">
                    <CheckCircle2 className="w-3 h-3" /> Done
                  </span>
                )}
              </div>

              <div className="flex items-center gap-3 mt-1.5 flex-wrap">
                <span className="flex items-center gap-1 text-xs text-zinc-600 font-mono">
                  <Calendar className="w-3 h-3" /> {session.date}
                </span>
                <span className="text-zinc-800">·</span>
                <span className="flex items-center gap-1 text-xs text-zinc-600 font-mono">
                  <Clock className="w-3 h-3" /> {session.duration}
                </span>
                <span className="text-zinc-800">·</span>
                <span className="flex items-center gap-1 text-xs text-zinc-600 font-mono">
                  <Flame className="w-3 h-3" /> {session.calories} kcal
                </span>
              </div>
            </div>
          </div>

          
          <div className="flex items-center gap-3 shrink-0">
            <div className="text-right hidden sm:block">
              <p className="text-sm font-black font-mono text-zinc-100">{(session.volume / 1000).toFixed(1)}k</p>
              <p className="text-[10px] text-zinc-600 uppercase tracking-widest">kg vol</p>
            </div>
            <div className="w-7 h-7 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-500">
              {expanded ? <ChevronUp className="w-3.5 h-3.5" /> : <ChevronDown className="w-3.5 h-3.5" />}
            </div>
          </div>
        </div>

        
        <div className="mt-4 h-1 bg-zinc-800 rounded-full overflow-hidden">
          <div
            className="h-full bg-gradient-to-r from-lime-600 to-lime-400 rounded-full"
            style={{ width: session.completed ? "100%" : "60%" }}
          />
        </div>
        <div className="flex justify-between mt-1">
          <span className="text-[10px] text-zinc-700 font-mono">{session.exercises.length} exercises</span>
          <span className="text-[10px] text-zinc-700 font-mono">{session.exercises.reduce((s, e) => s + e.sets.length, 0)} sets total</span>
        </div>
      </button>

      
      {expanded && (
        <div className="border-t border-zinc-800 px-5 pb-4">
          <div className="pt-3">
            {session.exercises.map((ex) => (
              <ExerciseRow key={ex.id} exercise={ex} />
            ))}
          </div>
          <div className="flex items-center gap-2 mt-4">
            <button className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-xs text-zinc-400 hover:text-zinc-200 font-mono uppercase tracking-widest transition-all">
              <Edit2 className="w-3 h-3" /> Edit
            </button>
            <button className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-xs text-red-400 hover:text-red-300 font-mono uppercase tracking-widest transition-all">
              <Trash2 className="w-3 h-3" /> Delete
            </button>
          </div>
        </div>
      )}

    </div>
  );
}

// ── Page ──────────────────────────────────────────────────────────────────────

export default function Page() {
  const [search, setSearch] = useState("");
  const [filterSplit, setFilterSplit] = useState<string>("All");

  const splits = ["All", "PPL", "Upper/Lower", "Full Body"];

  const filtered = SESSIONS.filter((s) => {
    const matchSearch = s.name.toLowerCase().includes(search.toLowerCase());
    const matchSplit  = filterSplit === "All" || s.split === filterSplit;
    return matchSearch && matchSplit;
  });

  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      {/* Background atmosphere */}
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-0 right-1/4 w-[500px] h-[350px] bg-lime-400/[0.025] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.015]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="wl-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#wl-grid)" />
        </svg>
      </div>

      <div className="relative max-w-4xl mx-auto space-y-6">

        {/* ── Page header ── */}
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3">
            <BackButton />
            <span className="w-px h-5 bg-zinc-800" />
            <div className="w-8 h-8 rounded-lg bg-lime-400/10 border border-lime-400/20 flex items-center justify-center">
              <Dumbbell className="w-4 h-4 text-lime-400" />
            </div>
            <div className="flex items-center gap-2 text-xs uppercase tracking-widest">
              <span className="text-zinc-600">Training</span>
              <span className="text-zinc-700">/</span>
              <span className="text-zinc-100 font-semibold">Workout Log</span>
            </div>
          </div>

          <button className="flex items-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-lime-400/10">
            <Plus className="w-3.5 h-3.5" />
            Log Workout
          </button>
        </div>

        {/* ── Weekly stats ── */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
          {WEEKLY_STATS.map((stat) => (
            <StatCard key={stat.label} stat={stat} />
          ))}
        </div>

        {/* ── Weekly volume bar ── */}
        <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-xs font-bold text-zinc-400 uppercase tracking-widest">Weekly Volume</h2>
              <p className="text-xs text-zinc-600 mt-0.5">kg lifted per day</p>
            </div>
            <div className="flex items-center gap-1.5 text-xs text-lime-400 font-semibold bg-lime-400/10 px-2.5 py-1 rounded-full">
              <TrendingUp className="w-3 h-3" /> +14% vs last week
            </div>
          </div>
          <div className="flex items-end gap-2 h-16">
            {["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((day, i) => {
              const heights = [72, 0, 68, 0, 74, 76, 0];
              const isToday = i === 4;
              return (
                <div key={day} className="flex-1 flex flex-col items-center gap-1.5 group">
                  <div className="w-full flex items-end justify-center" style={{ height: 52 }}>
                    {heights[i] > 0 ? (
                      <div
                        className={`w-full rounded-t-lg transition-all ${isToday ? "bg-gradient-to-t from-lime-600 to-lime-400" : "bg-zinc-700 group-hover:bg-zinc-600"}`}
                        style={{ height: `${heights[i]}%` }}
                      />
                    ) : (
                      <div className="w-full h-0.5 bg-zinc-800 rounded self-end" />
                    )}
                  </div>
                  <span className={`text-[10px] font-mono ${isToday ? "text-lime-400" : "text-zinc-700"}`}>{day}</span>
                </div>
              );
            })}
          </div>
        </div>

        {/* ── Search + filter ── */}
        <div className="flex items-center gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" />
            <input
              type="text"
              placeholder="Search sessions…"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full bg-zinc-900 border border-zinc-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-700 font-mono focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
            />
          </div>
          <div className="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-xl p-1">
            {splits.map((s) => (
              <button
                key={s}
                onClick={() => setFilterSplit(s)}
                className={`px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-200 ${
                  filterSplit === s
                    ? "bg-lime-400 text-zinc-900"
                    : "text-zinc-600 hover:text-zinc-300"
                }`}
              >
                {s}
              </button>
            ))}
          </div>
        </div>

        
        <div className="space-y-3">
          {filtered.length > 0 ? (
            filtered.map((session) => (
              <SessionCard key={session.id} session={session} />
            ))
          ) : (
            <div className="border border-dashed border-zinc-800 rounded-2xl p-12 text-center">
              <Dumbbell className="w-8 h-8 text-zinc-800 mx-auto mb-3" />
              <p className="text-xs text-zinc-700 font-mono uppercase tracking-widest">No sessions found</p>
            </div>
          )}
        </div>

      </div>
    </div>
  );
}
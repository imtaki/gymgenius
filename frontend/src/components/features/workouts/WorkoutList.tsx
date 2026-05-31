'use client' // Needs interactivity: start workouts, filter by date

import { useState, useEffect } from "react";
import {
  Dumbbell, Plus, Calendar, Clock, Flame, ChevronDown, ChevronUp,
  Filter, Search, MoreHorizontal, Trash2, CheckCircle2, Circle,
} from "lucide-react";
import BackButton from "@/components/ui/backbutton";
import { Workout } from "@/types/workouts";
import SplitSelector from "./SplitSelector";
import { deleteWorkout } from "@/app/api/workoutSessionService";
import Link from "next/link";

interface WorkoutListProps {
  initialWorkouts: Workout[];
  error?: string | null;
}

export default function WorkoutList({ initialWorkouts, error }: WorkoutListProps) {
  const [workouts, setWorkouts] = useState<Workout[]>(initialWorkouts);
  const [loading, setLoading] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [expandedId, setExpandedId] = useState<string | number | null>(null);
  const [showSplitSelector, setShowSplitSelector] = useState(false);
  const [deleteError, setDeleteError] = useState<string | null>(null);

  const filtered = workouts.filter((workout) =>
    workout.split_name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleDeleteWorkout = async (workoutId: string | number) => {
    if (!confirm("Delete this workout? This action cannot be undone.")) {
      return;
    }

    try {
      setLoading(true);
      await deleteWorkout(workoutId);
      setWorkouts((prev) => prev.filter((w) => w.id !== workoutId));
      setDeleteError(null);
    } catch (err) {
      setDeleteError("Failed to delete workout");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleWorkoutCreated = (newWorkout: Workout) => {
    setWorkouts((prev) => [newWorkout, ...prev]);
    setShowSplitSelector(false);
  };

  const calculateVolume = (workout: Workout): number => {
    return workout.exercises.reduce((total, ex) => {
      return (
        total +
        ex.logged_sets.reduce(
          (exTotal, set) => exTotal + (set.reps * (set.weight || 1)),
          0
        )
      );
    }, 0);
  };

  const formatDate = (dateStr: string): string => {
    const date = new Date(dateStr);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) return "Today";
    if (date.toDateString() === yesterday.toDateString()) return "Yesterday";

    return date.toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: date.getFullYear() !== today.getFullYear() ? "numeric" : undefined,
    });
  };

  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      {/* Background decorations */}
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-0 right-1/4 w-[500px] h-[350px] bg-lime-400/[0.025] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.015]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="wo-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#wo-grid)" />
        </svg>
      </div>

      <div className="relative max-w-4xl mx-auto space-y-6">
        {/* Header */}
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
              <span className="text-zinc-100 font-semibold">Workouts</span>
            </div>
          </div>
          <button
            onClick={() => setShowSplitSelector(true)}
            className="flex items-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-lime-400/10"
          >
            <Plus className="w-3.5 h-3.5" />
            Start Workout
          </button>
        </div>

        {/* Error messages */}
        {error && (
          <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
            {error}
          </div>
        )}
        {deleteError && (
          <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
            {deleteError}
          </div>
        )}

        {/* Stats */}
        {!loading && workouts.length > 0 && (
          <div className="flex items-center gap-4 px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl">
            <div className="text-center">
              <p className="text-lg font-black text-lime-400 font-mono leading-none">
                {workouts.filter((w) => w.ended_at).length}
              </p>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-0.5">Completed</p>
            </div>
            <div className="w-px h-8 bg-zinc-800" />
            <div className="text-center">
              <p className="text-lg font-black text-amber-400 font-mono leading-none">
                {workouts.filter((w) => !w.ended_at).length}
              </p>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-0.5">In Progress</p>
            </div>
          </div>
        )}

        {/* Split selector modal */}
        {showSplitSelector && (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 max-w-sm w-full mx-4">
              <h2 className="text-sm font-bold text-zinc-100 uppercase tracking-widest mb-4">
                Select a Split to Start
              </h2>
              <SplitSelector
                onWorkoutStarted={handleWorkoutCreated}
                onCancel={() => setShowSplitSelector(false)}
              />
            </div>
          </div>
        )}

        {/* Search */}
        {!showSplitSelector && (
          <div className="relative">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" />
            <input
              type="text"
              placeholder="Search workouts..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full bg-zinc-900 border border-zinc-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-700 font-mono focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
            />
          </div>
        )}

        {/* Workouts list */}
        {loading ? (
          <div className="space-y-2.5">
            {Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="bg-zinc-900 border border-zinc-800 rounded-2xl px-5 py-4 animate-pulse">
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-xl bg-zinc-800 shrink-0" />
                  <div className="flex-1 space-y-2">
                    <div className="h-3 bg-zinc-800 rounded w-1/3" />
                    <div className="h-2 bg-zinc-800/60 rounded w-1/5" />
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="border border-dashed border-zinc-800 rounded-2xl p-14 text-center">
            <Dumbbell className="w-8 h-8 text-zinc-800 mx-auto mb-3" />
            <p className="text-xs text-zinc-700 uppercase tracking-widest">
              {searchTerm ? "No workouts found" : "No workouts yet"}
            </p>
            {!searchTerm && (
              <button
                onClick={() => setShowSplitSelector(true)}
                className="mt-3 text-[10px] text-lime-400 hover:text-lime-300 uppercase tracking-widest transition-colors"
              >
                Start your first workout
              </button>
            )}
          </div>
        ) : (
          <div className="space-y-3">
            {filtered.map((workout) => (
              <Link
                key={workout.id}
                href={`/workouts/${workout.id}`}
                className="group relative bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden transition-all duration-300 hover:border-zinc-700 hover:shadow-xl hover:shadow-black/40"
              >
                <div className="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-lime-400 to-transparent transition-opacity duration-500 opacity-0 group-hover:opacity-30" />

                <button
                  onClick={(e) => {
                    e.preventDefault();
                    setExpandedId(expandedId === workout.id ? null : workout.id);
                  }}
                  className="w-full text-left p-5"
                >
                  <div className="flex items-start justify-between">
                    <div className="flex items-start gap-3">
                      <div className="w-10 h-10 rounded-xl bg-zinc-800 group-hover:bg-zinc-700 flex items-center justify-center transition-colors shrink-0">
                        {workout.ended_at ? (
                          <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                        ) : (
                          <Circle className="w-4 h-4 text-amber-400" />
                        )}
                      </div>

                      <div>
                        <div className="flex items-center gap-2 flex-wrap">
                          <h3 className="text-sm font-bold text-zinc-100 uppercase tracking-widest">
                            {workout.split_name}
                          </h3>
                          {workout.ended_at && (
                            <span className="flex items-center gap-1 text-[10px] text-emerald-400 font-mono">
                              <CheckCircle2 className="w-3 h-3" /> Done
                            </span>
                          )}
                        </div>

                        <div className="flex items-center gap-3 mt-1.5 flex-wrap">
                          <span className="flex items-center gap-1 text-xs text-zinc-600 font-mono">
                            <Calendar className="w-3 h-3" /> {formatDate(workout.date)}
                          </span>
                          {workout.started_at && (
                            <>
                              <span className="text-zinc-800">•</span>
                              <span className="flex items-center gap-1 text-xs text-zinc-600 font-mono">
                                <Clock className="w-3 h-3" /> {new Date(workout.started_at).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" })}
                              </span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>

                    <div className="flex items-center gap-3 shrink-0">
                      <div className="text-right hidden sm:block">
                        <p className="text-sm font-black font-mono text-zinc-100">
                          {(calculateVolume(workout) / 1000).toFixed(1)}k
                        </p>
                        <p className="text-[10px] text-zinc-600 uppercase tracking-widest">kg vol</p>
                      </div>
                      <div className="w-7 h-7 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-500">
                        {expandedId === workout.id ? (
                          <ChevronUp className="w-3.5 h-3.5" />
                        ) : (
                          <ChevronDown className="w-3.5 h-3.5" />
                        )}
                      </div>
                    </div>
                  </div>

                  <div className="mt-4 h-1 bg-zinc-800 rounded-full overflow-hidden">
                    <div
                      className="h-full bg-gradient-to-r from-lime-600 to-lime-400 rounded-full"
                      style={{
                        width: `${(workout.exercises.reduce((acc, ex) => acc + ex.logged_sets.length, 0) / (workout.exercises.reduce((acc, ex) => acc + (ex.target_sets || 3), 0))) * 100}%`,
                      }}
                    />
                  </div>
                  <div className="flex justify-between mt-1">
                    <span className="text-[10px] text-zinc-700 font-mono">
                      {workout.exercises.length} exercises
                    </span>
                    <span className="text-[10px] text-zinc-700 font-mono">
                      {workout.exercises.reduce((acc, ex) => acc + ex.logged_sets.length, 0)} sets logged
                    </span>
                  </div>
                </button>

                {expandedId === workout.id && (
                  <div className="border-t border-zinc-800 px-5 pb-4">
                    <div className="pt-3 space-y-2">
                      {workout.exercises.map((ex) => (
                        <div key={ex.id} className="flex items-center justify-between py-2">
                          <div className="flex items-center gap-2">
                            <div className="w-1.5 h-1.5 rounded-full bg-lime-400/50" />
                            <span className="text-sm text-zinc-200 font-mono">{ex.exercise_name}</span>
                          </div>
                          <span className="text-xs text-zinc-600 font-mono">
                            {ex.logged_sets.length} / {ex.target_sets || "?"}
                          </span>
                        </div>
                      ))}
                    </div>
                    <div className="flex items-center gap-2 mt-4">
                      <button
                        onClick={(e) => {
                          e.preventDefault();
                          handleDeleteWorkout(workout.id);
                        }}
                        className="flex-1 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-xs text-red-400 hover:text-red-300 font-mono uppercase tracking-widest transition-all"
                      >
                        <Trash2 className="w-3 h-3" /> Delete
                      </button>
                    </div>
                  </div>
                )}
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

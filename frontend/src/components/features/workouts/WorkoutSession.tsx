'use client' // Needs interactivity: log sets, complete workout

import { useState } from "react";
import {
  Dumbbell, ChevronDown, ChevronUp, Plus, Check, X, Calendar,
  Clock, ArrowLeft,
} from "lucide-react";
import { Workout, LoggedSet } from "@/types/workouts";
import { completeWorkout } from "@/app/api/workoutSessionService";
import { logSet, deleteLoggedSet } from "@/app/api/loggedSetService";
import SetLogger from "./SetLogger";
import Link from "next/link";

interface WorkoutSessionProps {
  initialWorkout: Workout;
}

export default function WorkoutSession({ initialWorkout }: WorkoutSessionProps) {
  const [workout, setWorkout] = useState<Workout>(initialWorkout);
  const [expandedExercises, setExpandedExercises] = useState<Set<string | number>>(new Set());
  const [loggingSetExerciseId, setLoggingSetExerciseId] = useState<string | number | null>(null);
  const [completingWorkout, setCompletingWorkout] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [deleteError, setDeleteError] = useState<string | null>(null);

  const toggleExercise = (exerciseId: string | number) => {
    const newExpanded = new Set(expandedExercises);
    if (newExpanded.has(exerciseId)) {
      newExpanded.delete(exerciseId);
    } else {
      newExpanded.add(exerciseId);
    }
    setExpandedExercises(newExpanded);
  };

  const handleSetLogged = async (
    exerciseId: string | number,
    setNumber: number,
    reps: number,
    weight: number | undefined,
    rpe: number | undefined
  ) => {
    try {
      setError(null);
      const newSet = await logSet(workout.id, {
        workout_split_exercise_id: exerciseId,
        set_number: setNumber,
        reps,
        weight,
        rpe,
      });

      setWorkout((prev) => ({
        ...prev,
        exercises: prev.exercises.map((ex) =>
          ex.id === exerciseId
            ? { ...ex, logged_sets: [...ex.logged_sets, newSet] }
            : ex
        ),
      }));

      setLoggingSetExerciseId(null);
    } catch (err) {
      setError(
        err instanceof Error ? err.message : "Failed to log set"
      );
      console.error(err);
    }
  };

  const handleDeleteSet = async (exerciseId: string | number, setId: string | number) => {
    if (!confirm("Delete this set?")) {
      return;
    }

    try {
      setDeleteError(null);
      await deleteLoggedSet(setId);

      setWorkout((prev) => ({
        ...prev,
        exercises: prev.exercises.map((ex) =>
          ex.id === exerciseId
            ? {
                ...ex,
                logged_sets: ex.logged_sets.filter((s) => s.id !== setId),
              }
            : ex
        ),
      }));
    } catch (err) {
      setDeleteError(
        err instanceof Error ? err.message : "Failed to delete set"
      );
      console.error(err);
    }
  };

  const handleCompleteWorkout = async () => {
    if (!confirm("Complete this workout?")) {
      return;
    }

    try {
      setCompletingWorkout(true);
      setError(null);
      const now = new Date().toISOString();
      const completed = await completeWorkout(workout.id, {
        ended_at: now,
      });
      setWorkout(completed);
    } catch (err) {
      setError(
        err instanceof Error ? err.message : "Failed to complete workout"
      );
      console.error(err);
    } finally {
      setCompletingWorkout(false);
    }
  };

  const totalVolume = workout.exercises.reduce((acc, ex) => {
    return (
      acc +
      ex.logged_sets.reduce(
        (exAcc, set) => exAcc + (set.reps * (set.weight || 1)),
        0
      )
    );
  }, 0);

  const isWorkoutComplete = !!workout.ended_at;

  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      {/* Background decorations */}
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-0 right-1/3 w-[500px] h-[400px] bg-lime-400/[0.025] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.015]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="session-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#session-grid)" />
        </svg>
      </div>

      <div className="relative max-w-2xl mx-auto space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <Link href="/workouts" className="flex items-center gap-2 text-zinc-600 hover:text-zinc-400 transition-colors">
            <ArrowLeft className="w-4 h-4" />
            <span className="text-xs uppercase tracking-widest font-semibold">Back</span>
          </Link>
          <div className="text-center">
            <h1 className="text-2xl font-black text-zinc-100 uppercase tracking-widest">
              {workout.split_name}
            </h1>
            <p className="text-xs text-zinc-600 mt-1 uppercase tracking-widest">
              {new Date(workout.date).toLocaleDateString("en-US", {
                weekday: "short",
                month: "short",
                day: "numeric",
              })}
            </p>
          </div>
          {isWorkoutComplete ? (
            <div className="flex items-center gap-2 px-3 py-1.5 bg-emerald-400/10 border border-emerald-400/20 rounded-full">
              <Check className="w-3 h-3 text-emerald-400" />
              <span className="text-[10px] text-emerald-400 font-semibold uppercase tracking-widest">
                Complete
              </span>
            </div>
          ) : (
            <div className="flex items-center gap-2 px-3 py-1.5 bg-amber-400/10 border border-amber-400/20 rounded-full">
              <Clock className="w-3 h-3 text-amber-400" />
              <span className="text-[10px] text-amber-400 font-semibold uppercase tracking-widest">
                In Progress
              </span>
            </div>
          )}
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
        <div className="grid grid-cols-3 gap-3">
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center">
            <p className="text-xl font-black text-lime-400 font-mono">{workout.exercises.length}</p>
            <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-1">Exercises</p>
          </div>
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center">
            <p className="text-xl font-black text-lime-400 font-mono">
              {workout.exercises.reduce((acc, ex) => acc + ex.logged_sets.length, 0)}
            </p>
            <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-1">Sets Logged</p>
          </div>
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center">
            <p className="text-xl font-black text-lime-400 font-mono">
              {(totalVolume / 1000).toFixed(1)}k
            </p>
            <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-1">Volume</p>
          </div>
        </div>

        {/* Exercises */}
        <div className="space-y-3">
          {workout.exercises.map((exercise) => (
            <div
              key={exercise.id}
              className="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden"
            >
              <button
                onClick={() => toggleExercise(exercise.id)}
                className="w-full text-left p-4 flex items-center justify-between hover:bg-zinc-800/50 transition-colors"
              >
                <div className="flex items-center gap-3 flex-1 min-w-0">
                  <div className="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center shrink-0">
                    <Dumbbell className="w-4 h-4 text-lime-400" />
                  </div>
                  <div className="min-w-0">
                    <p className="text-sm font-bold text-zinc-100 truncate">
                      {exercise.exercise_name}
                    </p>
                    <p className="text-[10px] text-zinc-600 mt-0.5 uppercase tracking-widest">
                      {exercise.logged_sets.length} / {exercise.target_sets || "?"}
                      {exercise.target_reps && ` x ${exercise.target_reps}`}
                    </p>
                  </div>
                </div>
                <div className="w-7 h-7 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-500 shrink-0">
                  {expandedExercises.has(exercise.id) ? (
                    <ChevronUp className="w-3.5 h-3.5" />
                  ) : (
                    <ChevronDown className="w-3.5 h-3.5" />
                  )}
                </div>
              </button>

              {expandedExercises.has(exercise.id) && (
                <div className="border-t border-zinc-800 px-4 pb-4">
                  {/* Logged sets */}
                  <div className="pt-4 space-y-2">
                    {exercise.logged_sets.length > 0 ? (
                      exercise.logged_sets.map((set, idx) => (
                        <div
                          key={set.id}
                          className="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg"
                        >
                          <div className="flex items-center gap-3">
                            <span className="text-xs font-bold text-zinc-600 w-6 text-right">
                              Set {set.set_number}
                            </span>
                            <span className="text-sm text-zinc-300 font-mono">
                              {set.reps} reps
                              {set.weight && ` × ${set.weight} kg`}
                              {set.rpe && ` · RPE ${set.rpe}`}
                            </span>
                          </div>
                          {!isWorkoutComplete && (
                            <button
                              onClick={() => handleDeleteSet(exercise.id, set.id)}
                              className="p-1 rounded text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                              title="Delete set"
                            >
                              <X className="w-3.5 h-3.5" />
                            </button>
                          )}
                        </div>
                      ))
                    ) : (
                      <p className="text-[10px] text-zinc-600 text-center py-2">
                        No sets logged yet
                      </p>
                    )}
                  </div>

                  {/* Log set form */}
                  {!isWorkoutComplete && loggingSetExerciseId === exercise.id ? (
                    <div className="mt-4 pt-4 border-t border-zinc-800">
                      <SetLogger
                        exerciseId={exercise.id}
                        setNumber={exercise.logged_sets.length + 1}
                        onSetLogged={(reps, weight, rpe) => {
                          handleSetLogged(exercise.id, exercise.logged_sets.length + 1, reps, weight, rpe);
                        }}
                        onCancel={() => setLoggingSetExerciseId(null)}
                      />
                    </div>
                  ) : (
                    !isWorkoutComplete && (
                      <button
                        onClick={() => setLoggingSetExerciseId(exercise.id)}
                        className="mt-4 w-full flex items-center justify-center gap-2 px-3 py-2.5 bg-lime-400/10 hover:bg-lime-400/20 border border-lime-400/20 hover:border-lime-400/40 text-lime-400 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
                      >
                        <Plus className="w-3 h-3" /> Log Set
                      </button>
                    )
                  )}
                </div>
              )}
            </div>
          ))}
        </div>

        {/* Actions */}
        {!isWorkoutComplete && (
          <button
            onClick={handleCompleteWorkout}
            disabled={completingWorkout}
            className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white rounded-xl text-sm font-bold uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20"
          >
            {completingWorkout ? (
              <>
                <span className="inline-block w-3 h-3 rounded-full border-2 border-white border-t-transparent animate-spin" />
                Completing...
              </>
            ) : (
              <>
                <Check className="w-4 h-4" /> Complete Workout
              </>
            )}
          </button>
        )}
      </div>
    </div>
  );
}

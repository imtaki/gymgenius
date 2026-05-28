'use client' // Needs interactivity: add/remove exercises

import { useState } from "react";
import {
  Dumbbell, Plus, Trash2, Edit2, X, ChevronDown, ChevronUp,
  ArrowLeft,
} from "lucide-react";
import BackButton from "@/components/ui/backbutton";
import { WorkoutSplit, WorkoutSplitExercise } from "@/types/workouts";
import AddExerciseToSplit from "./AddExerciseToSplit";
import SplitExerciseList from "./SplitExerciseList";
import { removeExerciseFromSplit } from "@/app/api/workoutSplitService";
import Link from "next/link";

interface SplitDetailProps {
  initialSplit: WorkoutSplit & { exercises: WorkoutSplitExercise[] };
}

export default function SplitDetail({ initialSplit }: SplitDetailProps) {
  const [split, setSplit] = useState(initialSplit);
  const [showAddExercise, setShowAddExercise] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleExerciseAdded = (exercise: WorkoutSplitExercise) => {
    setSplit((prev) => ({
      ...prev,
      exercises: [...prev.exercises, exercise],
      exercises_count: prev.exercises_count + 1,
    }));
    setShowAddExercise(false);
  };

  const handleExerciseRemoved = async (exerciseId: string | number) => {
    if (!confirm("Remove this exercise from the split?")) {
      return;
    }

    try {
      setLoading(true);
      setError(null);
      await removeExerciseFromSplit(split.id, exerciseId);
      setSplit((prev) => ({
        ...prev,
        exercises: prev.exercises.filter((ex) => ex.exercise_id !== exerciseId),
        exercises_count: prev.exercises_count - 1,
      }));
    } catch (err) {
      setError(
        err instanceof Error ? err.message : "Failed to remove exercise"
      );
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

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
            <pattern id="detail-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#detail-grid)" />
        </svg>
      </div>

      <div className="relative max-w-3xl mx-auto space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <Link href="/workout-splits" className="flex items-center gap-2 text-zinc-600 hover:text-zinc-400 transition-colors">
            <ArrowLeft className="w-4 h-4" />
            <span className="text-xs uppercase tracking-widest font-semibold">Back</span>
          </Link>
          <div className="text-center">
            <h1 className="text-2xl font-black text-zinc-100 uppercase tracking-widest">
              {split.name}
            </h1>
            {split.description && (
              <p className="text-xs text-zinc-600 mt-1">{split.description}</p>
            )}
          </div>
          <button
            onClick={() => setShowAddExercise(!showAddExercise)}
            className="flex items-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
          >
            <Plus className="w-3.5 h-3.5" />
            Add
          </button>
        </div>

        {/* Error messages */}
        {error && (
          <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
            {error}
          </div>
        )}

        {/* Stats */}
        <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
          <div className="text-center">
            <p className="text-lg font-black text-lime-400 font-mono leading-none">
              {split.exercises_count}
            </p>
            <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-1">
              Exercises
            </p>
          </div>
        </div>

        {/* Add exercise form */}
        {showAddExercise && (
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-sm font-bold text-zinc-100">Add Exercise</h2>
              <button
                onClick={() => setShowAddExercise(false)}
                className="text-zinc-600 hover:text-zinc-300"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
            <AddExerciseToSplit
              splitId={split.id}
              currentExerciseIds={split.exercises.map((ex) => ex.exercise_id)}
              onExerciseAdded={handleExerciseAdded}
              onCancel={() => setShowAddExercise(false)}
            />
          </div>
        )}

        {/* Exercises list */}
        {split.exercises.length === 0 ? (
          <div className="border border-dashed border-zinc-800 rounded-2xl p-14 text-center">
            <Dumbbell className="w-8 h-8 text-zinc-800 mx-auto mb-3" />
            <p className="text-xs text-zinc-700 uppercase tracking-widest mb-4">
              No exercises in this split yet
            </p>
            <button
              onClick={() => setShowAddExercise(true)}
              className="text-[10px] text-lime-400 hover:text-lime-300 uppercase tracking-widest transition-colors"
            >
              Add your first exercise
            </button>
          </div>
        ) : (
          <SplitExerciseList
            exercises={split.exercises}
            onRemoveExercise={handleExerciseRemoved}
          />
        )}
      </div>
    </div>
  );
}

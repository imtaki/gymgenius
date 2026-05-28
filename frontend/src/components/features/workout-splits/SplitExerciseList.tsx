'use client' // Needs interactivity: remove exercises

import { useState } from "react";
import { Trash2, GripVertical } from "lucide-react";
import { WorkoutSplitExercise } from "@/types/workouts";

interface SplitExerciseListProps {
  exercises: WorkoutSplitExercise[];
  onRemoveExercise: (exerciseId: string | number) => Promise<void>;
}

export default function SplitExerciseList({
  exercises,
  onRemoveExercise,
}: SplitExerciseListProps) {
  const [deleting, setDeleting] = useState<string | number | null>(null);

  const sorted = [...exercises].sort((a, b) => a.order - b.order);

  const handleDelete = async (exerciseId: string | number) => {
    try {
      setDeleting(exerciseId);
      await onRemoveExercise(exerciseId);
    } finally {
      setDeleting(null);
    }
  };

  return (
    <div className="space-y-2.5">
      {sorted.map((exercise, idx) => (
        <div
          key={exercise.id}
          className="group relative bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-zinc-700 transition-all duration-200"
        >
          <div className="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-lime-400 to-transparent transition-opacity duration-500 opacity-0 group-hover:opacity-30" />

          <div className="px-5 py-4 flex items-center gap-4">
            <div className="w-6 h-6 rounded-lg bg-zinc-800 flex items-center justify-center shrink-0">
              <span className="text-xs font-bold text-zinc-500">{idx + 1}</span>
            </div>

            <div className="flex-1 min-w-0">
              <p className="text-sm font-bold text-zinc-100 truncate">
                {exercise.exercise_name}
              </p>
              <div className="flex items-center gap-2 mt-1.5 flex-wrap">
                {exercise.target_sets && (
                  <span className="text-[9px] px-2 py-0.5 rounded-full border border-amber-400/20 bg-amber-400/10 text-amber-400 uppercase tracking-widest font-semibold">
                    {exercise.target_sets} sets
                  </span>
                )}
                {exercise.target_reps && (
                  <span className="text-[9px] px-2 py-0.5 rounded-full border border-sky-400/20 bg-sky-400/10 text-sky-400 uppercase tracking-widest font-semibold">
                    {exercise.target_reps} reps
                  </span>
                )}
              </div>
              {exercise.notes && (
                <p className="text-[10px] text-zinc-600 mt-2 font-mono">
                  {exercise.notes}
                </p>
              )}
            </div>

            <button
              onClick={() => handleDelete(exercise.exercise_id)}
              disabled={deleting === exercise.exercise_id}
              className="p-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 disabled:opacity-50 text-red-400 hover:text-red-300 transition-all shrink-0"
              title="Remove exercise"
            >
              <Trash2 className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      ))}
    </div>
  );
}

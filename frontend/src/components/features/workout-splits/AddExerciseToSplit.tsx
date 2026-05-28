'use client' // Needs interactivity: add exercise to split

import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { addExerciseToSplitSchema } from "@/lib/workoutSchemas";
import { addExerciseToSplit } from "@/app/api/workoutSplitService";
import { getExercises } from "@/app/api/exerciseService";
import { WorkoutSplitExercise, Exercise } from "@/types/workouts";
import { Loader, Search } from "lucide-react";

interface AddExerciseToSplitProps {
  splitId: string | number;
  currentExerciseIds: (string | number)[];
  onExerciseAdded: (exercise: WorkoutSplitExercise) => void;
  onCancel: () => void;
}

interface ExerciseOption extends Exercise {
  id: string | number;
}

export default function AddExerciseToSplit({
  splitId,
  currentExerciseIds,
  onExerciseAdded,
  onCancel,
}: AddExerciseToSplitProps) {
  const [exercises, setExercises] = useState<ExerciseOption[]>([]);
  const [filteredExercises, setFilteredExercises] = useState<ExerciseOption[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedExerciseId, setSelectedExerciseId] = useState<string | number | null>(null);
  const [error, setError] = useState<string | null>(null);

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: zodResolver(addExerciseToSplitSchema),
    defaultValues: {
      exercise_id: undefined,
      order: 0,
      target_sets: undefined,
      target_reps: undefined,
      notes: "",
    },
  });

  useEffect(() => {
    const fetchExercises = async () => {
      try {
        const data = await getExercises();
        setExercises(data);
      } catch (err) {
        setError("Failed to load exercises");
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchExercises();
  }, []);

  useEffect(() => {
    const available = exercises.filter(
      (ex) => !currentExerciseIds.includes(ex.id)
    );
    const filtered = available.filter((ex) =>
      ex.name.toLowerCase().includes(searchTerm.toLowerCase())
    );
    setFilteredExercises(filtered);
  }, [exercises, searchTerm, currentExerciseIds]);

  const onSubmit = async (data: any) => {
    if (!selectedExerciseId) {
      setError("Please select an exercise");
      return;
    }

    try {
      setSubmitting(true);
      setError(null);

      const result = await addExerciseToSplit(splitId, {
        exercise_id: selectedExerciseId,
        order: data.order || 0,
        target_sets: data.target_sets,
        target_reps: data.target_reps,
        notes: data.notes,
      });

      onExerciseAdded(result);
      reset();
      setSelectedExerciseId(null);
    } catch (err) {
      setError(
        err instanceof Error ? err.message : "Failed to add exercise"
      );
      console.error(err);
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-8">
        <div className="flex items-center gap-2 text-zinc-400">
          <Loader className="w-4 h-4 animate-spin" />
          <span className="text-sm font-mono">Loading exercises...</span>
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
      {error && (
        <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-2 rounded-lg text-sm">
          {error}
        </div>
      )}

      {/* Exercise selector */}
      <div>
        <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
          Exercise *
        </label>
        <div className="relative">
          <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" />
          <input
            type="text"
            placeholder="Search exercises..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
          />
        </div>

        {filteredExercises.length > 0 && (
          <div className="mt-2 max-h-48 overflow-y-auto space-y-1">
            {filteredExercises.map((exercise) => (
              <button
                key={exercise.id}
                type="button"
                onClick={() => setSelectedExerciseId(exercise.id)}
                className={`w-full text-left px-3 py-2 rounded-lg border transition-all ${
                  selectedExerciseId === exercise.id
                    ? "bg-lime-400/10 border-lime-400/40 text-lime-400"
                    : "bg-zinc-800 border-zinc-700 text-zinc-300 hover:bg-zinc-700 hover:border-zinc-600"
                }`}
              >
                <div className="font-semibold text-sm">{exercise.name}</div>
                <div className="text-[10px] text-zinc-500 mt-0.5 truncate capitalize">
                  {exercise.muscleGroup}
                </div>
              </button>
            ))}
          </div>
        )}

        {filteredExercises.length === 0 && searchTerm && (
          <p className="text-[10px] text-zinc-600 mt-2 text-center py-2">
            No exercises found
          </p>
        )}

        {filteredExercises.length === 0 && !searchTerm && exercises.length > 0 && (
          <p className="text-[10px] text-zinc-600 mt-2 text-center py-2">
            All exercises already added
          </p>
        )}
      </div>

      {/* Order */}
      <div>
        <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
          Order
        </label>
        <input
          type="number"
          placeholder="0"
          inputMode="numeric"
          {...register("order")}
          className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
        />
        {errors.order && (
          <p className="text-xs text-red-400 mt-1">{errors.order.message}</p>
        )}
      </div>

      {/* Target sets & reps */}
      <div className="grid grid-cols-2 gap-3">
        <div>
          <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
            Target Sets
          </label>
          <input
            type="number"
            placeholder="4"
            inputMode="numeric"
            {...register("target_sets")}
            className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
          />
          {errors.target_sets && (
            <p className="text-xs text-red-400 mt-1">{errors.target_sets.message}</p>
          )}
        </div>

        <div>
          <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
            Target Reps
          </label>
          <input
            type="number"
            placeholder="8"
            inputMode="numeric"
            {...register("target_reps")}
            className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
          />
          {errors.target_reps && (
            <p className="text-xs text-red-400 mt-1">{errors.target_reps.message}</p>
          )}
        </div>
      </div>

      {/* Notes */}
      <div>
        <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
          Notes
        </label>
        <textarea
          placeholder="e.g., Warm up light, then go heavy"
          {...register("notes")}
          rows={2}
          className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all resize-none"
        />
        {errors.notes && (
          <p className="text-xs text-red-400 mt-1">{errors.notes.message}</p>
        )}
      </div>

      {/* Actions */}
      <div className="flex items-center gap-2 pt-2">
        <button
          type="submit"
          disabled={submitting || !selectedExerciseId}
          className="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 text-zinc-900 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
        >
          {submitting ? (
            <>
              <Loader className="w-3.5 h-3.5 animate-spin" />
              Adding...
            </>
          ) : (
            "Add Exercise"
          )}
        </button>
        <button
          type="button"
          onClick={onCancel}
          className="flex-1 px-4 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
        >
          Cancel
        </button>
      </div>
    </form>
  );
}

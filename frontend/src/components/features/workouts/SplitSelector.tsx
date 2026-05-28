'use client' // Needs to fetch splits and start workout

import { useState, useEffect } from "react";
import { getWorkoutSplits } from "@/app/api/workoutSplitService";
import { startWorkout } from "@/app/api/workoutSessionService";
import { WorkoutSplit, Workout } from "@/types/workouts";
import { Loader } from "lucide-react";

interface SplitSelectorProps {
  onWorkoutStarted: (workout: Workout) => void;
  onCancel: () => void;
}

export default function SplitSelector({
  onWorkoutStarted,
  onCancel,
}: SplitSelectorProps) {
  const [splits, setSplits] = useState<WorkoutSplit[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [selectedSplitId, setSelectedSplitId] = useState<string | number | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchSplits = async () => {
      try {
        const data = await getWorkoutSplits();
        setSplits(data);
      } catch (err) {
        setError("Failed to load splits");
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchSplits();
  }, []);

  const handleStartWorkout = async () => {
    if (!selectedSplitId) {
      setError("Please select a split");
      return;
    }

    try {
      setSubmitting(true);
      setError(null);

      const today = new Date().toISOString().split("T")[0];
      const workout = await startWorkout({
        workout_split_id: selectedSplitId,
        date: today,
      });

      onWorkoutStarted(workout);
    } catch (err) {
      setError(
        err instanceof Error ? err.message : "Failed to start workout"
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
          <span className="text-sm font-mono">Loading splits...</span>
        </div>
      </div>
    );
  }

  if (splits.length === 0) {
    return (
      <div className="text-center py-8">
        <p className="text-xs text-zinc-600 uppercase tracking-widest mb-4">
          No splits available
        </p>
        <p className="text-[10px] text-zinc-700 font-mono">
          Create a split first to start a workout
        </p>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {error && (
        <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-2 rounded-lg text-sm">
          {error}
        </div>
      )}

      <div className="space-y-2">
        {splits.map((split) => (
          <button
            key={split.id}
            onClick={() => setSelectedSplitId(split.id)}
            className={`w-full text-left px-4 py-3 rounded-lg border transition-all ${
              selectedSplitId === split.id
                ? "bg-lime-400/10 border-lime-400/40 text-lime-400"
                : "bg-zinc-800 border-zinc-700 text-zinc-300 hover:bg-zinc-700 hover:border-zinc-600"
            }`}
          >
            <div className="font-semibold text-sm">{split.name}</div>
            {split.description && (
              <div className="text-[10px] text-zinc-500 mt-0.5 truncate">
                {split.description}
              </div>
            )}
            <div className="text-[9px] text-zinc-600 mt-1 uppercase tracking-widest">
              {split.exercises_count} exercises
            </div>
          </button>
        ))}
      </div>

      <div className="flex items-center gap-2 pt-2">
        <button
          onClick={handleStartWorkout}
          disabled={!selectedSplitId || submitting}
          className="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 disabled:cursor-not-allowed text-zinc-900 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
        >
          {submitting ? (
            <>
              <Loader className="w-3.5 h-3.5 animate-spin" />
              Starting...
            </>
          ) : (
            "Start Workout"
          )}
        </button>
        <button
          onClick={onCancel}
          className="flex-1 px-4 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
        >
          Cancel
        </button>
      </div>
    </div>
  );
}

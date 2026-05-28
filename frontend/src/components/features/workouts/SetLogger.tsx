'use client' // Needs form submission

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { logSetSchema } from "@/lib/workoutSchemas";
import { X, Loader } from "lucide-react";

interface SetLoggerProps {
  exerciseId: string | number;
  setNumber: number;
  onSetLogged: (reps: number, weight: number | undefined, rpe: number | undefined) => void;
  onCancel: () => void;
}

export default function SetLogger({
  exerciseId,
  setNumber,
  onSetLogged,
  onCancel,
}: SetLoggerProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: zodResolver(logSetSchema),
    defaultValues: {
      workout_split_exercise_id: exerciseId,
      set_number: setNumber,
      reps: undefined,
      weight: undefined,
      rpe: undefined,
    },
  });

  const onSubmit = async (data: any) => {
    try {
      setIsSubmitting(true);
      onSetLogged(data.reps, data.weight, data.rpe);
      reset();
    } catch (error) {
      console.error("Error logging set:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
      <div className="grid grid-cols-3 gap-2">
        {/* Reps */}
        <div>
          <label className="block text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">
            Reps *
          </label>
          <input
            type="number"
            placeholder="8"
            inputMode="numeric"
            {...register("reps")}
            className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
          />
          {errors.reps && (
            <p className="text-[9px] text-red-400 mt-0.5">{errors.reps.message}</p>
          )}
        </div>

        {/* Weight */}
        <div>
          <label className="block text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">
            Weight (kg)
          </label>
          <input
            type="number"
            placeholder="0"
            step="0.5"
            inputMode="decimal"
            {...register("weight")}
            className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
          />
          {errors.weight && (
            <p className="text-[9px] text-red-400 mt-0.5">{errors.weight.message}</p>
          )}
        </div>

        {/* RPE */}
        <div>
          <label className="block text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">
            RPE (1-10)
          </label>
          <input
            type="number"
            placeholder="7"
            min="1"
            max="10"
            inputMode="numeric"
            {...register("rpe")}
            className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
          />
          {errors.rpe && (
            <p className="text-[9px] text-red-400 mt-0.5">{errors.rpe.message}</p>
          )}
        </div>
      </div>

      {/* Actions */}
      <div className="flex items-center gap-2 pt-1">
        <button
          type="submit"
          disabled={isSubmitting}
          className="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 text-zinc-900 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
        >
          {isSubmitting ? (
            <>
              <Loader className="w-3 h-3 animate-spin" />
              Saving...
            </>
          ) : (
            "Log Set"
          )}
        </button>
        <button
          type="button"
          onClick={onCancel}
          className="p-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-600 hover:text-zinc-300 transition-all"
        >
          <X className="w-4 h-4" />
        </button>
      </div>
    </form>
  );
}

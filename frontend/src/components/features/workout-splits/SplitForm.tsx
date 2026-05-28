'use client' // Needs form submission and validation

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { createWorkoutSplitSchema, updateWorkoutSplitSchema } from "@/lib/workoutSchemas";
import { createWorkoutSplit, updateWorkoutSplit } from "@/app/api/workoutSplitService";
import { WorkoutSplit } from "@/types/workouts";
import { Loader } from "lucide-react";

interface SplitFormProps {
  initialData?: WorkoutSplit;
  onSuccess: (split: WorkoutSplit) => void;
  onCancel: () => void;
}

export default function SplitForm({ initialData, onSuccess, onCancel }: SplitFormProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [serverError, setServerError] = useState<string | null>(null);

  const schema = initialData ? updateWorkoutSplitSchema : createWorkoutSplitSchema;

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: initialData || {
      name: "",
      description: "",
    },
  });

  const onSubmit = async (data: any) => {
    try {
      setIsSubmitting(true);
      setServerError(null);

      let result;
      if (initialData) {
        result = await updateWorkoutSplit(initialData.id, data);
      } else {
        result = await createWorkoutSplit(data);
      }

      onSuccess(result);
    } catch (error) {
      setServerError(
        error instanceof Error ? error.message : "Failed to save split"
      );
      console.error(error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
      {serverError && (
        <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-2 rounded-lg text-sm">
          {serverError}
        </div>
      )}

      {/* Name */}
      <div>
        <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
          Name *
        </label>
        <input
          type="text"
          placeholder="e.g., Upper Body A"
          {...register("name")}
          className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
        />
        {errors.name && (
          <p className="text-xs text-red-400 mt-1">{errors.name.message}</p>
        )}
      </div>

      {/* Description */}
      <div>
        <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">
          Description
        </label>
        <textarea
          placeholder="e.g., Chest & Back focus"
          {...register("description")}
          rows={3}
          className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all resize-none"
        />
        {errors.description && (
          <p className="text-xs text-red-400 mt-1">{errors.description.message}</p>
        )}
      </div>

      {/* Actions */}
      <div className="flex items-center gap-2 pt-2">
        <button
          type="submit"
          disabled={isSubmitting}
          className="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 text-zinc-900 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
        >
          {isSubmitting ? (
            <>
              <Loader className="w-3.5 h-3.5 animate-spin" />
              Saving...
            </>
          ) : (
            initialData ? "Update Split" : "Create Split"
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

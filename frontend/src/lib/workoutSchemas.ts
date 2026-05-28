import { z } from "zod";

// Workout Split Schemas
export const createWorkoutSplitSchema = z.object({
  name: z.string().min(1, "Name is required").max(255, "Name must be less than 255 characters"),
  description: z.string().max(500, "Description must be less than 500 characters").optional(),
});

export const updateWorkoutSplitSchema = createWorkoutSplitSchema;

// Add Exercise to Split Schema
export const addExerciseToSplitSchema = z.object({
  exercise_id: z.coerce.number().positive("Exercise ID must be positive"),
  order: z.coerce.number().int("Order must be an integer").min(0, "Order must be 0 or greater"),
  target_sets: z.coerce.number().int().positive("Target sets must be positive").optional(),
  target_reps: z.coerce.number().int().positive("Target reps must be positive").optional(),
  notes: z.string().max(500, "Notes must be less than 500 characters").optional(),
});

export const updateSplitExerciseSchema = addExerciseToSplitSchema;

// Workout Session Schemas
export const startWorkoutSchema = z.object({
  workout_split_id: z.coerce.number().positive("Workout split ID is required"),
  date: z.string().refine(
    (val) => !isNaN(Date.parse(val)),
    "Date must be a valid date"
  ),
  notes: z.string().max(500, "Notes must be less than 500 characters").optional(),
});

export const completeWorkoutSchema = z.object({
  ended_at: z.string().refine(
    (val) => !isNaN(Date.parse(val)),
    "End time must be a valid date"
  ).optional(),
  notes: z.string().max(500, "Notes must be less than 500 characters").optional(),
});

// Logged Set Schema
export const logSetSchema = z.object({
  workout_split_exercise_id: z.coerce.number().positive("Workout split exercise ID is required"),
  set_number: z.coerce.number().int().positive("Set number must be positive"),
  reps: z.coerce.number().int().positive("Reps must be positive"),
  weight: z.coerce.number().positive("Weight must be positive").optional(),
  rpe: z.coerce.number().int().min(1, "RPE must be between 1 and 10").max(10, "RPE must be between 1 and 10").optional(),
});

export const updateLoggedSetSchema = z.object({
  reps: z.coerce.number().int().positive("Reps must be positive").optional(),
  weight: z.coerce.number().positive("Weight must be positive").optional(),
  rpe: z.coerce.number().int().min(1, "RPE must be between 1 and 10").max(10, "RPE must be between 1 and 10").optional(),
});

// TypeScript types inferred from schemas
export type CreateWorkoutSplitInput = z.infer<typeof createWorkoutSplitSchema>;
export type UpdateWorkoutSplitInput = z.infer<typeof updateWorkoutSplitSchema>;
export type AddExerciseToSplitInput = z.infer<typeof addExerciseToSplitSchema>;
export type UpdateSplitExerciseInput = z.infer<typeof updateSplitExerciseSchema>;
export type StartWorkoutInput = z.infer<typeof startWorkoutSchema>;
export type CompleteWorkoutInput = z.infer<typeof completeWorkoutSchema>;
export type LogSetInput = z.infer<typeof logSetSchema>;
export type UpdateLoggedSetInput = z.infer<typeof updateLoggedSetSchema>;

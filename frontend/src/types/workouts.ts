// Workout Splits
export interface WorkoutSplit {
  id: string | number;
  user_id: string | number;
  name: string;
  description?: string;
  exercises_count: number;
  created_at: string;
  updated_at: string;
}

export interface WorkoutSplitExercise {
  id: string | number;
  workout_split_id: string | number;
  exercise_id: string | number;
  exercise_name: string;
  order: number;
  target_sets?: number;
  target_reps?: number;
  notes?: string;
  created_at: string;
  updated_at: string;
}

// Workout Sessions
export interface LoggedSet {
  id: string | number;
  workout_id: string | number;
  workout_split_exercise_id: string | number;
  set_number: number;
  reps: number;
  weight?: number;
  rpe?: number; // Rate of Perceived Exertion (1-10)
  created_at: string;
  updated_at: string;
}

export interface WorkoutExerciseWithSets {
  id: string | number;
  exercise_id: string | number;
  exercise_name: string;
  order: number;
  target_sets?: number;
  target_reps?: number;
  logged_sets: LoggedSet[];
}

export interface Workout {
  id: string | number;
  user_id: string | number;
  workout_split_id: string | number;
  split_name: string;
  date: string;
  started_at?: string;
  ended_at?: string;
  notes?: string;
  exercises: WorkoutExerciseWithSets[];
  created_at: string;
  updated_at: string;
}

// Form Input Types
export interface CreateWorkoutSplitInput {
  name: string;
  description?: string;
}

export interface UpdateWorkoutSplitInput extends CreateWorkoutSplitInput {}

export interface AddExerciseToSplitInput {
  exercise_id: string | number;
  order: number;
  target_sets?: number;
  target_reps?: number;
  notes?: string;
}

export interface UpdateSplitExerciseInput extends AddExerciseToSplitInput {}

export interface StartWorkoutInput {
  workout_split_id: string | number;
  date: string;
  notes?: string;
}

export interface CompleteWorkoutInput {
  ended_at?: string;
  notes?: string;
}

export interface LogSetInput {
  workout_split_exercise_id: string | number;
  set_number: number;
  reps: number;
  weight?: number;
  rpe?: number;
}

export interface UpdateLoggedSetInput {
  reps?: number;
  weight?: number;
  rpe?: number;
}

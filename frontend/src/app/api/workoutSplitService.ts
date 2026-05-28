import axiosInstance from "./axios";
import { WorkoutSplit, CreateWorkoutSplitInput, UpdateWorkoutSplitInput, WorkoutSplitExercise, AddExerciseToSplitInput, UpdateSplitExerciseInput } from "@/types/workouts";

// Get all workout splits for the user
export const getWorkoutSplits = async (): Promise<WorkoutSplit[]> => {
  try {
    const response = await axiosInstance.get("/workout-splits");
    return response.data.data;
  } catch (error) {
    console.error("Error fetching workout splits:", error);
    throw error;
  }
};

// Get a specific workout split with exercises
export const getWorkoutSplitById = async (splitId: string | number): Promise<WorkoutSplit & { exercises: WorkoutSplitExercise[] }> => {
  try {
    const response = await axiosInstance.get(`/workout-splits/${splitId}`);
    return response.data.data;
  } catch (error) {
    console.error("Error fetching workout split:", error);
    throw error;
  }
};

// Create a new workout split
export const createWorkoutSplit = async (data: CreateWorkoutSplitInput): Promise<WorkoutSplit> => {
  try {
    const response = await axiosInstance.post("/workout-splits", data);
    return response.data.data;
  } catch (error) {
    console.error("Error creating workout split:", error);
    throw error;
  }
};

// Update a workout split
export const updateWorkoutSplit = async (splitId: string | number, data: UpdateWorkoutSplitInput): Promise<WorkoutSplit> => {
  try {
    const response = await axiosInstance.put(`/workout-splits/${splitId}`, data);
    return response.data.data;
  } catch (error) {
    console.error("Error updating workout split:", error);
    throw error;
  }
};

// Delete a workout split
export const deleteWorkoutSplit = async (splitId: string | number): Promise<void> => {
  try {
    await axiosInstance.delete(`/workout-splits/${splitId}`);
  } catch (error) {
    console.error("Error deleting workout split:", error);
    throw error;
  }
};

// Add an exercise to a split
export const addExerciseToSplit = async (splitId: string | number, data: AddExerciseToSplitInput): Promise<WorkoutSplitExercise> => {
  try {
    const response = await axiosInstance.post(`/workout-splits/${splitId}/exercises`, data);
    return response.data.data;
  } catch (error) {
    console.error("Error adding exercise to split:", error);
    throw error;
  }
};

// Update an exercise in a split
export const updateSplitExercise = async (splitId: string | number, exerciseId: string | number, data: UpdateSplitExerciseInput): Promise<WorkoutSplitExercise> => {
  try {
    const response = await axiosInstance.put(`/workout-splits/${splitId}/exercises/${exerciseId}`, data);
    return response.data.data;
  } catch (error) {
    console.error("Error updating split exercise:", error);
    throw error;
  }
};

// Remove an exercise from a split
export const removeExerciseFromSplit = async (splitId: string | number, exerciseId: string | number): Promise<void> => {
  try {
    await axiosInstance.delete(`/workout-splits/${splitId}/exercises/${exerciseId}`);
  } catch (error) {
    console.error("Error removing exercise from split:", error);
    throw error;
  }
};

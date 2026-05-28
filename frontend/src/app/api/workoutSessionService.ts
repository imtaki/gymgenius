import axiosInstance from "./axios";
import { Workout, StartWorkoutInput, CompleteWorkoutInput } from "@/types/workouts";

// Get all workouts for the user (optionally filtered by date range)
export const getWorkouts = async (params?: { from_date?: string; to_date?: string }): Promise<Workout[]> => {
  try {
    const response = await axiosInstance.get("/workouts", { params });
    return response.data.data;
  } catch (error) {
    console.error("Error fetching workouts:", error);
    throw error;
  }
};

// Get a specific workout session
export const getWorkoutById = async (workoutId: string | number): Promise<Workout> => {
  try {
    const response = await axiosInstance.get(`/workouts/${workoutId}`);
    return response.data.data;
  } catch (error) {
    console.error("Error fetching workout:", error);
    throw error;
  }
};

// Start a new workout session
export const startWorkout = async (data: StartWorkoutInput): Promise<Workout> => {
  try {
    const response = await axiosInstance.post("/workouts", data);
    return response.data.data;
  } catch (error) {
    console.error("Error starting workout:", error);
    throw error;
  }
};

// Complete/end a workout session
export const completeWorkout = async (workoutId: string | number, data: CompleteWorkoutInput): Promise<Workout> => {
  try {
    const response = await axiosInstance.put(`/workouts/${workoutId}`, data);
    return response.data.data;
  } catch (error) {
    console.error("Error completing workout:", error);
    throw error;
  }
};

// Delete a workout session
export const deleteWorkout = async (workoutId: string | number): Promise<void> => {
  try {
    await axiosInstance.delete(`/workouts/${workoutId}`);
  } catch (error) {
    console.error("Error deleting workout:", error);
    throw error;
  }
};

// Get workouts for a specific date
export const getWorkoutsByDate = async (date: string): Promise<Workout[]> => {
  return getWorkouts({ from_date: date, to_date: date });
};

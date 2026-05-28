import axiosInstance from "./axios";
import { LoggedSet, LogSetInput, UpdateLoggedSetInput } from "@/types/workouts";

// Log a new set during a workout
export const logSet = async (workoutId: string | number, data: LogSetInput): Promise<LoggedSet> => {
  try {
    const response = await axiosInstance.post(`/workouts/${workoutId}/sets`, data);
    return response.data.data;
  } catch (error) {
    console.error("Error logging set:", error);
    throw error;
  }
};

// Update a logged set
export const updateLoggedSet = async (setId: string | number, data: UpdateLoggedSetInput): Promise<LoggedSet> => {
  try {
    const response = await axiosInstance.put(`/sets/${setId}`, data);
    return response.data.data;
  } catch (error) {
    console.error("Error updating logged set:", error);
    throw error;
  }
};

// Delete a logged set
export const deleteLoggedSet = async (setId: string | number): Promise<void> => {
  try {
    await axiosInstance.delete(`/sets/${setId}`);
  } catch (error) {
    console.error("Error deleting logged set:", error);
    throw error;
  }
};

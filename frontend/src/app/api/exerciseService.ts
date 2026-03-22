import axiosInstance from "./axios";

export const getExercises = async () => {
    try {
        const response = await axiosInstance.get('/exercises');
        return response.data.data;
    } catch (error) {
        console.error("Error fetching exercises:", error);
        throw error;
    }
};

export const getExerciseById = async (exerciseId: string) => {
    try {
        const response = await axiosInstance.get(`/exercises/${exerciseId}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching exercise:", error);
        throw error;
    }
};

export const createExercise = async (exerciseData: any) => {
    try {
        const response = await axiosInstance.post('/exercises', exerciseData);
        if (response.data.success) {
            return response.data.data;
        } else {
            throw new Error(response.data.error || 'Failed to create exercise');
        }
    } catch (error) {
        console.error("Error creating exercise:", error);
        throw error;
    }
};

export const updateExercise = async (exerciseId: string, exerciseData: any) => {
    try {
        const response = await axiosInstance.put(`/exercises/${exerciseId}`, exerciseData);
        if (response.data.success) {
            return response.data.data;
        } else {
            throw new Error(response.data.error || 'Failed to update exercise');
        }
    } catch (error) {
        console.error("Error updating exercise:", error);
        throw error;
    }
};

export const deleteExerciseById = async (exerciseId: string) => {
    try {
        const response = await axiosInstance.delete(`/exercises/${exerciseId}`);
        if (response.data.success) {
            return response.data;
        } else {
            throw new Error(response.data.error || 'Failed to delete exercise');
        }
    } catch (error) {
        console.error("Error deleting exercise:", error);
        throw error;
    }
};
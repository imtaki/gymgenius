import axiosInstance from "./axios";


export const getExercises = async () => {
    try {
        const response = await axiosInstance.get('/exercises');
        return response.data;
    } catch (error) {
        console.error("Error fetching exercises:", error);
        throw error;
    }
};

export const getExerciseById = async (exerciseId: string) => {
    try {
        const response = await axiosInstance.get(`/exercises/${exerciseId}`);
        return response.data;
    } catch (error) {
        console.error("Error fetching exercise:", error);
        throw error;
    }
};

export const createExercise = async (exerciseData: any) => {
    try {
        const response = await axiosInstance.post('/exercises', exerciseData);
        return response.data;
    } catch (error) {
        console.error("Error creating exercise:", error);
        throw error;
    }
};

export const deleteExerciseById = async (exerciseId: string) => {
    try {
        const response = await axiosInstance.delete(`/exercises/${exerciseId}`);
        return response.data;
    } catch (error) {
        console.error("Error deleting exercise:", error);
        throw error;
    }
};
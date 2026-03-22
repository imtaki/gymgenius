import axiosInstance from "./axios";

export const getMealsByUserId = async (userId: string) => {
    try {
        const response = await axiosInstance.get(`/meals/user/${userId}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching meals:", error);
        throw error;
    }
};

export const getMealById = async (mealId: string) => {
    try {
        const response = await axiosInstance.get(`/meals/${mealId}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching meal:", error);
        throw error;
    }
};

export const createMealForUser = async (userId: string, mealData: any) => {
    try {
        const response = await axiosInstance.post(`/meals/user/${userId}`, mealData);
        if (response.data.success) {
            return response.data.data;
        } else {
            throw new Error(response.data.error || 'Failed to create meal');
        }
    } catch (error) {
        console.error("Error creating meal:", error);
        throw error;
    }
};

export const updateMealById = async (mealId: string, mealData: any) => {
    try {
        const response = await axiosInstance.put(`/meals/${mealId}`, mealData);
        if (response.data.success) {
            return response.data.data;
        } else {
            throw new Error(response.data.error || 'Failed to update meal');
        }
    } catch (error) {
        console.error("Error updating meal:", error);
        throw error;
    }
};

export const deleteMealById = async (mealId: string) => {
    try {
        const response = await axiosInstance.delete(`/meals/${mealId}`);
        if (response.data.success) {
            return response.data;
        } else {
            throw new Error(response.data.error || 'Failed to delete meal');
        }
    } catch (error) {
        console.error("Error deleting meal:", error);
        throw error;
    }
};

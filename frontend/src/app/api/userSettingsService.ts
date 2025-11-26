import axiosInstance from "./axios";

export const getUserSettingsById = async (userId: string) => {
    try {
        const response = await axiosInstance.get(`/settings/user/${userId}`);
        return response.data;
    } catch (error) {
        console.error("Error fetching meals:", error);
        throw error;
    }
};
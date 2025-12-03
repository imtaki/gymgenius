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


export const updateUserSettings = async (userId: string, settingsData: any) => {
    try {
        console.log('Request URL:', `/settings/user/${userId}`);
        console.log('Request data:', settingsData);
        
        const response = await axiosInstance.put(`/settings/user/${userId}`, settingsData);
        return response.data;
    } catch (error) {
        console.error("Error updating user settings:", error);
        console.error("Error response:", error.response?.data); // This is key!
        throw error;
    }
};
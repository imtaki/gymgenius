import axiosInstance from "./axios";

export const getUserSettingsById = async (userId: string) => {
    try {
        const response = await axiosInstance.get(`/settings/user/${userId}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching user settings:", error);
        throw error;
    }
};

export const updateUserSettings = async (userId: string, settingsData: any) => {
    try {
        console.log('Request URL:', `/settings/user/${userId}`);
        console.log('Request data:', settingsData);
        
        const response = await axiosInstance.put(`/settings/user/${userId}`, settingsData);
        if (response.data.success) {
            return response.data.data;
        } else {
            throw new Error(response.data.error || 'Failed to update user settings');
        }
    } catch (error) {
        console.error("Error updating user settings:", error);
        console.error("Error response:", error.response?.data);
        throw error;
    }
};
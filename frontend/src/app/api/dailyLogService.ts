import axiosInstance from "./axios";

export const getDailyLogByDate = async (userId: string, date: string) => {
    try {
        const response = await axiosInstance.get(`/daily-goals/user/${userId}/date/${date}`);
        return response.data;
    } catch (error) {
        console.error("Error fetching daily log:", error);
        throw error;
    }
};

export const getTodayDailyLog = async (userId: string) => {
    try {
        const response = await axiosInstance.get(`/daily-goals/user/${userId}/today`);
        return response.data;
    } catch (error) {
        console.error("Error fetching today's daily log:", error);
        throw error;
    }
};

export const getWeeklyDailyLogs = async (userId: string) => {
    try {
        const response = await axiosInstance.get(`/daily-goals/user/${userId}/weekly`);
        return response.data;
    } catch (error) {
        console.error("Error fetching weekly daily logs:", error);
        throw error;
    };
};
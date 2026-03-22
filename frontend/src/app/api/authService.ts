
import axiosInstance from "./axios";

export const getUser = () => {
  const user = localStorage.getItem("user")
  return user ? JSON.parse(user) : null;
}

export const registerUser = async (name: string, email: string, password: string) => {
    try {
        const response = await axiosInstance.post("/register", { name, email, password });
        return response.data;
    } catch (error) {
        throw error;
    }       
};

export const loginUser = async (email: string, password: string) => {
    try {
        const response = await axiosInstance.post("/login", { email, password });
        const { id, token } = response.data;
        localStorage.setItem("user", JSON.stringify({ id, token }));
        return { id, token };
    } catch (error) {
        throw error;
    }
};

export const logout = async () => {
    try {
        await axiosInstance.post("/auth/logout");
        localStorage.removeItem("user");
    } catch (error) {
        console.error("Error during logout:", error);
        localStorage.removeItem("user");
    }
}

export async function verifyEmailCode(email: string, code: string) {
    try {
        const response = await axiosInstance.post("/verify-email", { email, code });
        return response.data;
    } catch (error) {
        throw error;
    }
}

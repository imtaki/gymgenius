import axiosInstance from "./axios";
import { User } from "../types/types";

export const updateSubscriptionTier = async (
  tier: "free" | "pro" | "pro_plus"
): Promise<User> => {
  try {
    const response = await axiosInstance.patch("/user/subscription", { tier });
    
    if (response.data && response.data.attributes) {
      return {
        id: response.data.id,
        name: response.data.attributes.name,
        email: response.data.attributes.email,
        role: response.data.attributes.role,
        subscription_tier: response.data.attributes.subscription_tier,
        created_at: response.data.attributes.created_at || new Date().toISOString(),
        updated_at: response.data.attributes.updated_at || new Date().toISOString(),
      };
    }
    
    throw new Error("Invalid response format");
  } catch (error) {
    console.error("Error updating subscription tier:", error);
    throw error;
  }
};

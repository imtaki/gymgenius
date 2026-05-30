export interface Meal {
    id: string;
    name: string;
    calories: number;
    protein: number;
    carbs: number;
    fats: number;
    category: 'breakfast' | 'lunch' | 'dinner' | 'snacks';
    image?: string;
}

export interface User {
    id: string;
    name: string;
    email: string;
    role: string;
    subscription_tier: 'free' | 'pro' | 'pro_plus';
    created_at: string;
    updated_at: string;
}

export interface DecodedToken {
    id: number;
    username: string;
    role: string;
    email?: string;
}

export interface StatsData {
    user_count: number;
    meal_logs_count: number;
}

export interface RecentUsers {
    id: number;
    name: string;
    email: string;
    created_at: string;
}
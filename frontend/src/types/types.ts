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

export interface DecodedToken {
    id: number;
    username: string;
    role: string;
    email?: string;
}
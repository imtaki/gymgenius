import { Meal } from "./types";

export interface MacroDisplayProps {
  label: string;
  value: number;
  color: string;
  barColor: string;
  icon: React.ReactNode;
  goal: number;
}

export interface MealCardProps {
  meal: Meal;
  onDelete: (id: string) => void;
}

export interface MealColumnProps {
  title: string;
  icon: React.ReactNode;
  accent: string;
  iconBg: string;
  meals: Meal[];
  onDelete: (id: string) => void;
}
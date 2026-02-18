import { Users, Dumbbell, UtensilsCrossed, BarChart3, CreditCard } from "lucide-react";

export const AVATAR_COLORS = [
  "bg-violet-600",
  "bg-emerald-600",
  "bg-orange-600",
  "bg-sky-600",
  "bg-rose-600",
  "bg-teal-600",
];

export const PLAN_COLOR = {
  Pro:   "text-violet-400 bg-violet-400/10 border-violet-400/20",
  Elite: "text-amber-400  bg-amber-400/10  border-amber-400/20",
  Free:  "text-slate-400  bg-slate-400/10  border-slate-400/20",
};

export const STATUS_COLOR = {
  active:    "text-emerald-400 bg-emerald-400/10",
  inactive:  "text-slate-400   bg-slate-400/10",
  suspended: "text-red-400     bg-red-400/10",
};

export const DIFF_COLOR = {
  Easy:   "text-emerald-400 bg-emerald-400/10",
  Medium: "text-amber-400   bg-amber-400/10",
  Hard:   "text-red-400     bg-red-400/10",
};

export const CAT_COLOR = {
  Strength:    "text-violet-400 bg-violet-400/10",
  Cardio:      "text-orange-400 bg-orange-400/10",
  Flexibility: "text-sky-400    bg-sky-400/10",
};

export const TABS = [
  { id: "overview",      label: "Overview",      icon: BarChart3       },
  { id: "users",         label: "Users",          icon: Users           },
  { id: "workouts",      label: "Workouts",        icon: Dumbbell        },
  { id: "meals",         label: "Meal Logs",       icon: UtensilsCrossed },
  { id: "subscriptions", label: "Subscriptions",   icon: CreditCard      },
];
import { Users, Dumbbell, UtensilsCrossed, CreditCard } from "lucide-react";

export const STATS = [
  { label: "Total Users",          value: "12,847", change: "+8.2%",  up: true,  icon: Users,           color: "from-violet-500 to-purple-600" },
  { label: "Active Subscriptions", value: "4,231",  change: "+12.5%", up: true,  icon: CreditCard,      color: "from-emerald-500 to-teal-600"  },
  { label: "Workouts Logged",      value: "89,204", change: "+3.1%",  up: true,  icon: Dumbbell,        color: "from-orange-500 to-red-500"    },
  { label: "Meal Logs Today",      value: "3,109",  change: "-1.4%",  up: false, icon: UtensilsCrossed, color: "from-sky-500 to-blue-600"      },
];

export const USERS = [
  { id: 1, name: "Jordan Mitchell", email: "jordan@example.com", plan: "Pro",   status: "active",    joined: "Jan 12, 2025", workouts: 142, avatar: "JM" },
  { id: 2, name: "Priya Sharma",    email: "priya@example.com",  plan: "Elite", status: "active",    joined: "Feb 3, 2025",  workouts: 87,  avatar: "PS" },
  { id: 3, name: "Carlos Reyes",    email: "carlos@example.com", plan: "Free",  status: "inactive",  joined: "Mar 18, 2025", workouts: 12,  avatar: "CR" },
  { id: 4, name: "Aisha Okonkwo",   email: "aisha@example.com",  plan: "Pro",   status: "active",    joined: "Nov 5, 2024",  workouts: 310, avatar: "AO" },
  { id: 5, name: "Sam Whitfield",   email: "sam@example.com",    plan: "Elite", status: "suspended", joined: "Oct 9, 2024",  workouts: 229, avatar: "SW" },
  { id: 6, name: "Mei Lin Chen",    email: "mei@example.com",    plan: "Free",  status: "active",    joined: "Apr 22, 2025", workouts: 5,   avatar: "ML" },
];

export const WORKOUTS = [
  { id: 1, name: "Upper Body Blast",  category: "Strength",    duration: "45 min", difficulty: "Hard",   users: 2341, rating: 4.8 },
  { id: 2, name: "Morning Yoga Flow", category: "Flexibility", duration: "30 min", difficulty: "Easy",   users: 5102, rating: 4.9 },
  { id: 3, name: "HIIT Cardio Burn",  category: "Cardio",      duration: "20 min", difficulty: "Hard",   users: 3874, rating: 4.6 },
  { id: 4, name: "Core & Abs Focus",  category: "Strength",    duration: "25 min", difficulty: "Medium", users: 1892, rating: 4.5 },
  { id: 5, name: "Lower Body Power",  category: "Strength",    duration: "50 min", difficulty: "Medium", users: 2108, rating: 4.7 },
];

export const MEALS = [
  { id: 1, user: "Jordan Mitchell", meal: "Breakfast", foods: "Oats, Banana, Almond Milk",             calories: 420, protein: "18g", logged: "07:32 AM", date: "Today"     },
  { id: 2, user: "Priya Sharma",    meal: "Lunch",     foods: "Grilled Chicken, Brown Rice, Broccoli", calories: 680, protein: "52g", logged: "12:15 PM", date: "Today"     },
  { id: 3, user: "Aisha Okonkwo",   meal: "Dinner",    foods: "Salmon, Quinoa, Asparagus",             calories: 590, protein: "44g", logged: "07:02 PM", date: "Today"     },
  { id: 4, user: "Sam Whitfield",   meal: "Snack",     foods: "Greek Yogurt, Mixed Berries",           calories: 180, protein: "15g", logged: "03:30 PM", date: "Yesterday" },
  { id: 5, user: "Mei Lin Chen",    meal: "Breakfast", foods: "Avocado Toast, Eggs",                   calories: 510, protein: "22g", logged: "08:45 AM", date: "Yesterday" },
];

export const SUBSCRIPTIONS = [
  { plan: "Free",  price: "$0",     users: 8616, color: "bg-slate-500",  pct: 67 },
  { plan: "Pro",   price: "$12/mo", users: 2891, color: "bg-violet-500", pct: 22 },
  { plan: "Elite", price: "$29/mo", users: 1340, color: "bg-amber-500",  pct: 10 },
];

export const RECENT_TRANSACTIONS = [
  { user: "Priya Sharma",    plan: "Elite → Elite",   amount: "+$29.00", date: "Today",      positive: true  },
  { user: "Aisha Okonkwo",   plan: "Free → Pro",      amount: "+$12.00", date: "Today",      positive: true  },
  { user: "Sam Whitfield",   plan: "Elite Cancelled", amount: "-$29.00", date: "Yesterday",  positive: false },
  { user: "Jordan Mitchell", plan: "Pro Renewal",      amount: "+$12.00", date: "2 days ago", positive: true  },
];

export const CHART_DATA = [
  { month: "Aug", users: 8200,  workouts: 62000 },
  { month: "Sep", users: 9100,  workouts: 70000 },
  { month: "Oct", users: 9800,  workouts: 74000 },
  { month: "Nov", users: 10500, workouts: 79000 },
  { month: "Dec", users: 11200, workouts: 83000 },
  { month: "Jan", users: 11800, workouts: 86000 },
  { month: "Feb", users: 12847, workouts: 89204 },
];
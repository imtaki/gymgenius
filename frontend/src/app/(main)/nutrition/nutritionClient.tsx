"use client";

import { Coffee, UtensilsCrossed, Apple, Flame, Droplet, Wheat, Beef, Trash2, Moon, ChevronRight, ChevronLeft } from "lucide-react";
import Image from "next/image";
import { useState, useEffect } from "react";
import { Meal } from "../../../types/types";
import AddMealModal from "../../../components/sections/AddMealModal";
import { getUser } from "../../api/authService";
import { deleteMealById, getMealsByUserId } from "../../api/mealService";
import { getUserSettingsById } from "../../api/userSettingsService";
import { format, subDays, addDays, isToday } from "date-fns";
import { MacroDisplayProps, MealCardProps, MealColumnProps } from "../../../types/nutrition";
import SkeletonLoader from "../../../components/skeletons/NutritionPageSkeleton";
import { getDailyLogByDate } from "../../api/dailyLogService";

const CATEGORY_META: Record<string, { icon: React.ReactNode; accent: string; iconBg: string; emptyLabel: string }> = {
  breakfast: {
    icon: <Coffee className="w-4 h-4" />,
    accent: "text-amber-400",
    iconBg: "bg-amber-400/10",
    emptyLabel: "No breakfast logged",
  },
  lunch: {
    icon: <UtensilsCrossed className="w-4 h-4" />,
    accent: "text-sky-400",
    iconBg: "bg-sky-400/10",
    emptyLabel: "No lunch logged",
  },
  snacks: {
    icon: <Apple className="w-4 h-4" />,
    accent: "text-lime-400",
    iconBg: "bg-lime-400/10",
    emptyLabel: "No snacks logged",
  },
  dinner: {
    icon: <Moon className="w-4 h-4" />,
    accent: "text-fuchsia-400",
    iconBg: "bg-fuchsia-400/10",
    emptyLabel: "No dinner logged",
  },
};


function CalorieCounter({
  current,
  goal = 2000,
  macros,
}: {
  current: number;
  goal?: number;
  macros: { protein: number; carbs: number; fats: number };
}) {
  const pct = Math.min((current / goal) * 100, 100);
  const remaining = Math.max(goal - current, 0);
  const isOver = current > goal;

  return (
    <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
      <div className="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-6">
        <div>
          <p className="text-xs text-zinc-500 uppercase tracking-widest mb-2 font-mono">Daily Total</p>
          <div className="flex items-baseline gap-2">
            <span className="text-5xl font-black text-white font-mono tracking-tighter">{current.toLocaleString()}</span>
            <span className="text-sm text-zinc-500 font-mono">kcal</span>
          </div>
          <p className={`text-xs font-mono mt-1 ${isOver ? "text-red-400" : "text-zinc-500"}`}>
            {isOver
              ? `${(current - goal).toLocaleString()} kcal over goal`
              : `${remaining.toLocaleString()} kcal remaining`}
          </p>
        </div>

        
        <div className="flex items-center gap-3">
          <MacroDisplay label="Protein" value={macros.protein} goal={200} color="text-sky-400"    barColor="bg-sky-400"    icon={<Beef    className="w-3.5 h-3.5" />} />
          <MacroDisplay label="Carbs"   value={macros.carbs}   goal={250} color="text-amber-400"  barColor="bg-amber-400"  icon={<Wheat   className="w-3.5 h-3.5" />} />
          <MacroDisplay label="Fats"    value={macros.fats}    goal={70}  color="text-rose-400"   barColor="bg-rose-400"   icon={<Droplet className="w-3.5 h-3.5" />} />
        </div>
      </div>

    
      <div className="space-y-2">
        <div className="relative w-full h-2.5 bg-zinc-800 rounded-full overflow-hidden">
          <div
            className={`absolute top-0 left-0 h-full rounded-full transition-all duration-700 ease-out ${
              isOver
                ? "bg-gradient-to-r from-red-600 to-red-400"
                : "bg-gradient-to-r from-lime-600 to-lime-400"
            }`}
            style={{ width: `${pct}%` }}
          />
        </div>
        <div className="flex justify-between">
          <span className="text-xs text-zinc-600 font-mono">{pct.toFixed(0)}% of goal</span>
          <span className="text-xs text-zinc-600 font-mono">Goal: {goal.toLocaleString()} kcal</span>
        </div>
      </div>
    </div>
  );
}


function MacroDisplay({ label, value, goal, color, barColor, icon }: MacroDisplayProps) {
  const pct = Math.min((value / goal) * 100, 100);
  return (
    <div className="flex flex-col items-center gap-2 bg-zinc-800/60 border border-zinc-700/50 rounded-xl px-4 py-3 min-w-[80px]">
      <div className={`${color} flex items-center gap-1.5`}>
        {icon}
        <span className="text-xs font-bold font-mono">{value}g</span>
      </div>
      <div className="w-full h-1 bg-zinc-700 rounded-full overflow-hidden">
        <div
          className={`h-full rounded-full ${barColor} transition-all duration-500`}
          style={{ width: `${pct}%` }}
        />
      </div>
      <span className="text-[10px] text-zinc-600 uppercase tracking-widest font-mono">{label}</span>
    </div>
  );
}



function MealCard({ meal, onDelete }: MealCardProps) {
  const meta = CATEGORY_META[meal.category] ?? CATEGORY_META.snacks;

  return (
    <div className="group flex items-center gap-3 bg-zinc-800/50 hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-700 rounded-xl p-3.5 transition-all duration-200">

      
      <div className="w-10 h-10 rounded-lg overflow-hidden bg-zinc-700 shrink-0">
        {meal.image ? (
          <Image src={meal.image} alt={meal.name} width={40} height={40} className="w-full h-full object-cover" />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-lg">🍽️</div>
        )}
      </div>

      
      <div className="flex-1 min-w-0">
        <p className="text-white text-xs font-semibold truncate">{meal.name}</p>
        <p className={`text-xs font-mono font-bold ${meta.accent}`}>{meal.calories} kcal</p>
        <p className="text-zinc-600 text-[10px] font-mono mt-0.5">
          P:{meal.protein}g · C:{meal.carbs}g · F:{meal.fats}g
        </p>
      </div>

      
      <button
        onClick={() => onDelete(meal.id)}
        className="opacity-0 group-hover:opacity-100 p-1.5 rounded-lg text-zinc-600 hover:text-red-400 hover:bg-red-400/10 transition-all duration-200 shrink-0"
        aria-label="Delete meal"
      >
        <Trash2 className="w-3.5 h-3.5" />
      </button>
    </div>
  );
}


function MealColumn({ title, icon, accent, iconBg, meals, onDelete }: MealColumnProps) {
  const colCalories = meals.reduce((s, m) => s + (m.calories || 0), 0);

  return (
    <div className="flex flex-col gap-3">

      
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div className={`w-7 h-7 rounded-lg ${iconBg} ${accent} flex items-center justify-center`}>
            {icon}
          </div>
          <span className={`text-xs font-bold uppercase tracking-widest font-mono ${accent}`}>{title}</span>
        </div>
        {meals.length > 0 && (
          <span className="text-[10px] text-zinc-600 font-mono">{colCalories} kcal</span>
        )}
      </div>

      
      <div className="space-y-2">
        {meals.length > 0 ? (
          meals.map((meal) => (
            <MealCard key={meal.id} meal={meal} onDelete={onDelete} />
          ))
        ) : (
          <div className="border border-dashed border-zinc-800 rounded-xl p-5 text-center">
            <p className="text-zinc-700 text-xs font-mono">Nothing logged</p>
          </div>
        )}
      </div>
    </div>
  );
}




export default function NutritionClient() {
  const [meals, setMeals] = useState<Meal[]>([]);
  const [loading, setLoading] = useState(true);
  const [userId, setUserId] = useState<string>("");
  const [caloricGoal, setCaloricGoal] = useState<number>(2000);
  const [selectedDate, setSelectedDate] = useState<Date>(new Date());

  useEffect(() => {
    const user = getUser();
    const id = user?.id || "";
    setUserId(id);

    async function fetchSettings() {
      if (!id) { setLoading(false); return; }
      try {
        const settings = await getUserSettingsById(id);
        setCaloricGoal(parseInt(settings.caloric_goal) || 2000);
      } catch (error) {
        console.error("Failed to fetch settings:", error);
      }
    }

    fetchSettings();
  }, []);

  useEffect(() => {
    if (!userId) return;

    async function fetchMealsForDate() {
      try {
        setLoading(true);
        const dateString = format(selectedDate, "yyyy-MM-dd");
        const dailyLog = await getDailyLogByDate(userId, dateString);
        
        setMeals(dailyLog.meals || []);
      } catch (error) {
        console.error("Failed to fetch meals for date:", error);
        setMeals([]);
      } finally {
        setLoading(false);
      }
    }

    fetchMealsForDate();
  }, [selectedDate, userId]);

  const handlePreviousDay = () => setSelectedDate(prev => subDays(prev, 1));
  const handleNextDay = () => {
    const tomorrow = addDays(selectedDate, 1);
    if (tomorrow <= new Date()) setSelectedDate(tomorrow);
  };

  const handleDeleteMeal = async (id: string) => {
    setMeals((prev) => prev.filter((m) => m.id !== id));
    try {
      await deleteMealById(id);
    } catch (error) {
      console.error("Failed to delete meal:", error);
      const dateString = format(selectedDate, "yyyy-MM-dd");
      const dailyLog = await getDailyLogByDate(userId, dateString);
      setMeals(dailyLog.meals || []);
    }
  };

  if (loading) return <SkeletonLoader />;

  const dateDisplay = isToday(selectedDate) 
    ? "Today" 
    : format(selectedDate, "EEE, MMM d");

  const totals = meals.reduce(
    (acc, m) => ({
      calories: acc.calories + (m.calories || 0),
      protein:  acc.protein  + (m.protein  || 0),
      carbs:    acc.carbs    + (m.carbs    || 0),
      fats:     acc.fats     + (m.fats     || 0),
    }),
    { calories: 0, protein: 0, carbs: 0, fats: 0 }
  );

  const categorized = {
    breakfast: meals.filter((m) => m.category === "breakfast"),
    lunch:     meals.filter((m) => m.category === "lunch"),
    snacks:    meals.filter((m) => m.category === "snacks"),
    dinner:    meals.filter((m) => m.category === "dinner"),
  };

  return (
    <div className="space-y-6" style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}>

      
      <div className="flex items-center justify-between bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
        <button onClick={handlePreviousDay} className="p-2 hover:bg-zinc-800 rounded-lg transition">
          <ChevronLeft className="w-5 h-5 text-zinc-400" />
        </button>
        <div className="text-center">
          <p className="text-xs text-zinc-500 uppercase tracking-widest mb-1 font-mono">Meal Log</p>
          <p className="text-xl font-bold text-white font-mono">{dateDisplay}</p>
        </div>
        <button 
          onClick={handleNextDay} 
          disabled={addDays(selectedDate, 1) > new Date()}
          className="p-2 hover:bg-zinc-800 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <ChevronRight className="w-5 h-5 text-zinc-400" />
        </button>
      </div>

      
      <CalorieCounter
        current={totals.calories}
        goal={caloricGoal}
        macros={{ protein: totals.protein, carbs: totals.carbs, fats: totals.fats }}
      />

      
      <AddMealModal userId={userId} date={selectedDate} />

      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {(["breakfast", "lunch", "snacks", "dinner"] as const).map((cat) => {
          const meta = CATEGORY_META[cat];
          return (
            <MealColumn
              key={cat}
              title={cat}
              icon={meta.icon}
              accent={meta.accent}
              iconBg={meta.iconBg}
              meals={categorized[cat]}
              onDelete={handleDeleteMeal}
            />
          );
        })}
      </div>

    </div>
  );
}
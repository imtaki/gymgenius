import { cookies } from "next/headers";
import { Coffee, UtensilsCrossed, Apple, SquareX } from "lucide-react";

interface Meal {
    id: string;
    name: string;
    calories: number;
    protein: number;
    carbs: number;
    fats: number;
    category: 'breakfast' | 'lunch' | 'dinner' | 'snacks';
    image?: string;
}

const MealCard = ({ meal }: { meal: Meal }) => {
    const categoryIcons = {
        breakfast: <Coffee className="w-5 h-5" />,
        lunch: <UtensilsCrossed className="w-5 h-5" />,
        dinner: <UtensilsCrossed className="w-5 h-5" />,
        snacks: <Apple className="w-5 h-5" />
    };

    return (
        <div className="bg-zinc-900 rounded-xl p-4 hover:bg-zinc-800 transition-all cursor-pointer border border-zinc-800 hover:border-zinc-700">
            <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-lg overflow-hidden bg-zinc-800 flex-shrink-0">
                    {meal.image ? (
                        <img
                            src={meal.image}
                            alt={meal.name}
                            className="w-full h-full object-cover"
                        />
                    ) : (
                        <div className="w-full h-full flex items-center justify-center text-2xl">
                            🍽️
                        </div>
                    )}
                </div>
                <div className="flex-1 min-w-0">
                    <h3 className="text-white font-medium text-sm truncate">{meal.name}</h3>
                    <p className="text-emerald-400 text-xs font-medium">{meal.calories} kcal</p>
                    <p className="text-zinc-500 text-xs mt-1">
                        P:{meal.protein}g • C:{meal.carbs}g • F:{meal.fats}g
                    </p>
                </div>
                <div className="text-zinc-500">
                    {categoryIcons[meal.category]}
                </div>
                <button><SquareX className="text-red-400"/></button>
            </div>
        </div>
    );
};

const CalorieCounter = ({ current, goal = 2000 }: { current: number; goal?: number }) => {
    const percentage = Math.min((current / goal) * 100, 100);

    return (
        <div className="bg-zinc-900 rounded-2xl p-6 border border-zinc-800">
            <div className="flex items-center justify-between mb-4">
                <div>
                    <h2 className="text-3xl font-bold text-white">{current} kcal</h2>
                    <p className="text-zinc-400 text-sm mt-1">Daily intake</p>
                </div>
                <div className="flex gap-2">
                    <div className="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                        <span className="text-yellow-500 text-lg">⚡</span>
                    </div>
                    <div className="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                        <span className="text-red-500 text-lg">🔥</span>
                    </div>
                    <div className="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <span className="text-blue-500 text-lg">💧</span>
                    </div>
                    <div className="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <span className="text-orange-500 text-lg">🍊</span>
                    </div>
                </div>
            </div>
            <div className="w-full bg-zinc-800 rounded-full h-2 overflow-hidden">
                <div
                    className="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-500"
                    style={{ width: `${percentage}%` }}
                />
            </div>
            <p className="text-zinc-500 text-xs mt-2">Goal: {goal} kcal</p>
        </div>
    );
};

export default async function NutritionPage() {
    const cookieStore = await cookies();
    const userIdObject = cookieStore.get("userId");
    const userId = userIdObject?.value;

    const data = await fetch(`http://localhost:8000/api/users/${userId}/meals`);
    const meals: Meal[] = await data.json();

    const calculated = meals.reduce((sum: number, meal: Meal) => sum + meal.calories, 0);

    // Categorize meals
    const categorizedMeals = {
        breakfast: meals.filter(m => m.category === 'breakfast'),
        lunch: meals.filter(m => m.category === 'lunch'),
        snacks: meals.filter(m => m.category === 'snacks'),
        dinner: meals.filter(m => m.category === 'dinner')
    };

    return (
        <div className="min-h-screen p-6">
            <div className="max-w-7xl mx-auto">
                <div className="flex items-center gap-2 mb-6">
                    <button className="text-zinc-400 hover:text-white">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h1 className="text-zinc-400 text-lg">
                        Meal plans / <span className="text-white font-semibold">Nutrition Tracker</span>
                    </h1>
                </div>

                <CalorieCounter current={calculated} goal={2000} />

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                    <div>
                        <h3 className="text-zinc-400 text-sm font-medium mb-4 uppercase tracking-wider">
                            Breakfast
                        </h3>
                        <div className="space-y-3">
                            {categorizedMeals.breakfast.length > 0 ? (
                                categorizedMeals.breakfast.map(meal => (
                                    <MealCard key={meal.id} meal={meal} />
                                ))
                            ) : (
                                <p className="text-zinc-600 text-sm">No meals yet</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <h3 className="text-zinc-400 text-sm font-medium mb-4 uppercase tracking-wider">
                            Lunch
                        </h3>
                        <div className="space-y-3">
                            {categorizedMeals.lunch.length > 0 ? (
                                categorizedMeals.lunch.map(meal => (
                                    <MealCard key={meal.id} meal={meal} />
                                ))
                            ) : (
                                <p className="text-zinc-600 text-sm">No meals yet</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <h3 className="text-zinc-400 text-sm font-medium mb-4 uppercase tracking-wider">
                            Snacks
                        </h3>
                        <div className="space-y-3">
                            {categorizedMeals.snacks.length > 0 ? (
                                categorizedMeals.snacks.map(meal => (
                                    <MealCard key={meal.id} meal={meal} />
                                ))
                            ) : (
                                <p className="text-zinc-600 text-sm">No meals yet</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <h3 className="text-zinc-400 text-sm font-medium mb-4 uppercase tracking-wider">
                            Dinner
                        </h3>
                        <div className="space-y-3">
                            {categorizedMeals.dinner.length > 0 ? (
                                categorizedMeals.dinner.map(meal => (
                                    <MealCard key={meal.id} meal={meal} />
                                ))
                            ) : (
                                <p className="text-zinc-600 text-sm">No meals yet</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
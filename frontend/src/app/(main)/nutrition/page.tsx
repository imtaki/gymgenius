import { cookies} from "next/headers";

export default async function NutritionPage() {
    const cookieStore = await cookies();
    const userIdObject = cookieStore.get("userId");
    const userId = userIdObject?.value
    const data = await fetch(`http://localhost:8000/api/users/${userId}/meals`)
    const meals = await data.json();
    const calculated = meals.reduce((sum: number, meal: any) => sum + meal.calories, 0);
    return (
        <div className="max-w-2xl mx-auto p-6">
            <h1 className="text-3xl font-bold mb-6">🍽️ Nutrition</h1>
            <ul className="space-y-3">
                {meals.map((meal) => (
                    <li key={meal.id} className="border border-gray-300 p-3 rounded-lg shadow-sm">
                        <h2 className="font-semibold text-lg">{meal.name}</h2>
                        <p className="text-gray-600 text-sm">
                            {meal.calories} kcal — P:{meal.protein}g | C:{meal.carbs}g | F:{meal.fats}g
                        </p>
                    </li>
                ))}
            </ul>
            <div>
                Daily calories: {calculated}
            </div>
        </div>
    );
}

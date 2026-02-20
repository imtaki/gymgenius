"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Plus, X, Loader2, Save } from "lucide-react";
import { createMealForUser } from "../../app/api/mealService";
import { format } from "date-fns";

export default function AddMealModal({ userId, date }: { userId: string; date: Date }) {
    const [isOpen, setIsOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    const [formData, setFormData] = useState({
        name: "",
        calories: "",
        protein: "",
        carbs: "",
        fats: "",
        category: "breakfast",

    });

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            const payload = {
                ...formData,
                date: format(date, "yyyy-MM-dd"),
                calories: Number(formData.calories),
                protein: Number(formData.protein),
                carbs: Number(formData.carbs),
                fats: Number(formData.fats),
            };

            const res = await createMealForUser(userId, payload);

            if (res) {
                setFormData({ name: "", calories: "", protein: "", carbs: "", fats: "", category: "breakfast"});
                setIsOpen(false);
                router.refresh(); 
            } else {
                console.error("Failed to add meal");
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    return (
        <>
            <button 
                onClick={() => setIsOpen(true)}
                className="flex items-center gap-2 bg-lime-600 hover:bg-lime-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
            >
                <Plus className="w-4 h-4" /> Add Meal
            </button>

            {isOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                    <div className="bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
                        
    
                        <div className="flex items-center justify-between p-4 border-b border-zinc-800">
                            <h2 className="text-white font-semibold">Track a new meal</h2>
                            <button onClick={() => setIsOpen(false)} className="text-zinc-400 hover:text-white">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="p-5 space-y-4">
                            <div>
                                <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Meal Name</label>
                                <input 
                                    required name="name" value={formData.name} onChange={handleChange}
                                    placeholder="e.g. Chicken Salad"
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition-all"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Category</label>
                                    <select 
                                        name="category" value={formData.category} onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-emerald-500"
                                    >
                                        <option value="breakfast">Breakfast</option>
                                        <option value="lunch">Lunch</option>
                                        <option value="dinner">Dinner</option>
                                        <option value="snacks">Snacks</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Calories</label>
                                    <input 
                                        required type="number" name="calories" value={formData.calories} onChange={handleChange}
                                        placeholder="0"
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-emerald-500"
                                    />
                                </div>
                            </div>

                            <div className="grid grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-[10px] text-zinc-500 mb-1 text-center">PROTEIN (g)</label>
                                    <input 
                                        required type="number" name="protein" value={formData.protein} onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2 text-center text-white outline-none focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] text-zinc-500 mb-1 text-center">CARBS (g)</label>
                                    <input 
                                        required type="number" name="carbs" value={formData.carbs} onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2 text-center text-white outline-none focus:border-amber-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] text-zinc-500 mb-1 text-center">FATS (g)</label>
                                    <input 
                                        required type="number" name="fats" value={formData.fats} onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2 text-center text-white outline-none focus:border-rose-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Image URL (Optional)</label>
                                <input 
                                    name="image" value={formData.image} onChange={handleChange}
                                    placeholder="https://..."
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-sm text-white outline-none focus:border-emerald-500"
                                />
                            </div>

                            <button 
                                type="submit" 
                                disabled={loading}
                                className="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-medium p-3 rounded-lg mt-2 flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {loading ? <Loader2 className="w-5 h-5 animate-spin"/> : <Save className="w-5 h-5"/>}
                                {loading ? "Saving..." : "Save Meal"}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </>
    );
}
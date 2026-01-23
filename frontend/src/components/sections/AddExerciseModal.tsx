"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Plus, X, Loader2, Save } from "lucide-react";
import { createExercise } from "../../app/api/exerciseService";
import { 
    MUSCLE_GROUPS, 
    EQUIPMENT_OPTIONS,
    muscleGroup, 
    equipment, 
    difficultyLevel
} from "../../types/exercises";

export default function AddExerciseModal() {
    const [isOpen, setIsOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    const [formData, setFormData] = useState({
        name: "",
        muscleGroup: "",
        secondaryMuscles: "",
        description: "",
        difficulty: difficultyLevel.intermediate,
        equipment: "",
    });

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            const payload = {
                name: formData.name,
                muscleGroup: formData.muscleGroup,
                secondary_muscles: formData.secondaryMuscles || null,
                description: formData.description || null,
                difficulty: formData.difficulty,
                equipment: formData.equipment || null,
            };

            const res = await createExercise(payload);

            if (res) {
                setFormData({ 
                    name: "", 
                    muscleGroup: "", 
                    secondaryMuscles: "",
                    description: "",
                    difficulty: difficultyLevel.intermediate,
                    equipment: ""
                });
                setIsOpen(false);
                router.refresh();
            } else {
                console.error("Failed to add exercise");
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };


    return (
        <>
            <button 
                onClick={() => setIsOpen(true)}
                className="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium transition-colors"
            >
                <Plus className="w-4 h-4" /> Add Exercise
            </button>

            {isOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                    <div className="bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
                        
                        <div className="flex items-center justify-between p-4 border-b border-zinc-800">
                            <h2 className="text-white font-semibold">Create new exercise</h2>
                            <button onClick={() => setIsOpen(false)} className="text-zinc-400 hover:text-white">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="p-5 space-y-4 max-h-[90vh] overflow-y-auto">
                            <div>
                                <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Exercise Name</label>
                                <input 
                                    required 
                                    name="name" 
                                    value={formData.name} 
                                    onChange={handleChange}
                                    placeholder="e.g. Barbell Bench Press"
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition-all"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Primary Muscle</label>
                                    <select 
                                        required
                                        name="muscleGroup" 
                                        value={formData.muscleGroup} 
                                        onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-emerald-500"
                                    >
                                        <option value="">Select muscle</option>
                                        {Object.values(muscleGroup).map((val, index) => (
                                            <option key={val} value={val}>
                                                {MUSCLE_GROUPS[index]}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Secondary Muscles</label>
                                    <input 
                                        type="text" 
                                        name="secondaryMuscles" 
                                        value={formData.secondaryMuscles} 
                                        onChange={handleChange}
                                        placeholder="e.g. Triceps, Shoulders"
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-emerald-500"
                                    />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Difficulty</label>
                                    <select 
                                        name="difficulty" 
                                        value={formData.difficulty} 
                                        onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-emerald-500"
                                    >
                                        {Object.values(difficultyLevel).map((level) => (
                                            <option key={level} value={level}>
                                                {level.charAt(0).toUpperCase() + level.slice(1)}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Equipment</label>
                                    <select 
                                        name="equipment" 
                                        value={formData.equipment} 
                                        onChange={handleChange}
                                        className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-emerald-500"
                                    >
                                        <option value="">Select equipment</option>
                                        {Object.values(equipment).map((val, index) => (
                                            <option key={val} value={val}>
                                                {EQUIPMENT_OPTIONS[index]}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs text-zinc-400 mb-1 uppercase tracking-wide">Description</label>
                                <textarea 
                                    name="description" 
                                    value={formData.description} 
                                    onChange={handleChange}
                                    placeholder="How to perform this exercise, form tips, etc."
                                    rows={4}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-sm text-white outline-none focus:border-emerald-500 resize-none"
                                />
                            </div>

                            <button 
                                type="submit" 
                                disabled={loading}
                                className="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-medium p-3 rounded-lg mt-2 flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {loading ? <Loader2 className="w-5 h-5 animate-spin"/> : <Save className="w-5 h-5"/>}
                                {loading ? "Creating..." : "Create Exercise"}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </>
    );
}
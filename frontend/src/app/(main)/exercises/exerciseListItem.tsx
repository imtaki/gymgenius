"use client";

import { useState } from "react";
import { ChevronDown, ChevronUp, Dumbbell, Info } from "lucide-react";
import { Exercise } from "../../../types/exercises";

interface Props {
    exercise: Exercise;
}

export default function ExerciseListItem({ exercise }: Props) {
    const [isExpanded, setIsExpanded] = useState(false);

    return (
        <div className="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden transition-all duration-200">
           
            <div className="p-4 flex items-center justify-between">
                <div className="flex items-center gap-4">
                    <div className="p-2 bg-emerald-500/10 rounded-lg">
                        <Dumbbell className="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                        <h3 className="text-white font-medium">{exercise.name}</h3>
                        <p className="text-xs text-zinc-500 uppercase tracking-wider">
                            {exercise.muscleGroup} • {exercise.difficulty}
                        </p>
                    </div>
                </div>

                <button 
                    onClick={() => setIsExpanded(!isExpanded)}
                    className={`p-2 rounded-lg transition-colors ${
                        isExpanded ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-800'
                    }`}
                >
                    {isExpanded ? <ChevronUp className="w-5 h-5" /> : <ChevronDown className="w-5 h-5" />}
                </button>
            </div>

            {isExpanded && (
                <div className="px-4 pb-4 pt-0 animate-in slide-in-from-top-2 duration-200">
                    <div className="h-px bg-zinc-800 mb-4" />
                    
                    <div className="space-y-3">
                        
                        {exercise.equipment && (
                            <div className="flex items-center gap-2 text-sm text-zinc-300">
                                <span className="text-zinc-500 font-semibold text-xs uppercase">Equipment:</span>
                                <span className="bg-zinc-800 px-2 py-0.5 rounded border border-zinc-700">
                                    {exercise.equipment.replace('_', ' ')}
                                </span>
                            </div>
                        )}

                        
                        <div className="flex gap-2">
                            <Info className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                            <div>
                                <span className="text-zinc-500 font-semibold text-xs uppercase block mb-1">Instruction</span>
                                <p className="text-sm text-zinc-400 leading-relaxed">
                                    {exercise.description || "No description provided for this exercise."}
                                </p>
                            </div>
                        </div>

                        
                        {exercise.secondaryMuscles && (
                            <div className="text-xs text-zinc-500 italic">
                                Targets also: {exercise.secondaryMuscles}
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}
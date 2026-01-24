"use client";

import { useEffect, useState } from "react";
import { getExercises } from "../../api/exerciseService";
import AddExerciseModal from "../../../components/sections/AddExerciseModal";
import { Exercise } from "../../../types/exercises";
import { ChevronDown, ChevronUp, Info, Dumbbell, Box } from "lucide-react"; // Added Icons

export default function ExercisesPage() {
  const [exercises, setExercises] = useState<Exercise[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [expandedId, setExpandedId] = useState<string | null>(null); // Track which row is open

  async function fetchExercises() {
    try {
      setLoading(true);
      const res = await getExercises();
      setExercises(res);
    } catch (error) {
      console.error("Error fetching exercises:", error);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    fetchExercises();
  }, []);

  const filteredExercises = exercises.filter(exercise =>
    exercise.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const toggleExpand = (id: string) => {
    setExpandedId(expandedId === id ? null : id);
  };

  return (
    <div className="min-h-screen py-6 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl mx-auto">
        
        <div className="flex items-center justify-between mb-6">
          <h1 className="text-2xl font-bold">Exercises</h1>
          <AddExerciseModal />
        </div>

        <div className="mb-6">
          <input
            type="text"
            placeholder="Search exercises..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm text-white"
          />
        </div>

        {loading ? (
          <div className="text-center py-12">
            <p className="text-gray-500">Loading...</p>
          </div>
        ) : filteredExercises.length === 0 ? (
          <div className="text-center py-12">
            <p className="text-gray-500">No exercises found</p>
          </div>
        ) : (
          <div className="space-y-3">
            {filteredExercises.map((exercise: Exercise) => {
              const isOpen = expandedId === exercise.id;
              
              return (
                <div
                  key={exercise.id}
                  className="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden transition-all duration-200"
                >
                  
                  <div className="p-4 flex items-center gap-4">
                    
                    <div className="w-12 h-12 bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0">
                        <Dumbbell className="w-6 h-6 text-emerald-500" />
                    </div>
                    
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-white">
                        {exercise.name}
                      </h3>
                      <p className="text-xs text-zinc-500 uppercase tracking-wider">
                        {exercise.muscleGroup || "General"} • {exercise.difficulty}
                      </p>
                    </div>

                    <button 
                      onClick={() => toggleExpand(exercise.id)} 
                      className={`flex items-center gap-1 text-sm font-medium px-3 py-1.5 rounded-lg transition-colors ${
                        isOpen ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white'
                      }`}
                    >
                      {isOpen ? 'Close' : 'View'}
                      {isOpen ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                    </button>
                  </div>

                  
                  {isOpen && (
                    <div className="px-4 pb-5 pt-0 animate-in fade-in slide-in-from-top-2 duration-300">
                      <div className="h-px bg-zinc-800 mb-4" />
                      
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div className="space-y-1">
                            <span className="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Equipment</span>
                            <div className="flex items-center gap-2 text-zinc-300 text-sm">
                                <Box className="w-4 h-4 text-emerald-500" />
                                {exercise.equipment?.replace('_', ' ') || "None"}
                            </div>
                        </div>
                        <div className="space-y-1">
                            <span className="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Secondary Muscles</span>
                            <p className="text-zinc-300 text-sm">{exercise.secondaryMuscles || "None"}</p>
                        </div>
                      </div>

                      <div className="space-y-2">
                        <span className="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Description / Tips</span>
                        <div className="bg-zinc-950/50 p-3 rounded-lg border border-zinc-800/50 flex gap-3">
                            <Info className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                            <p className="text-sm text-zinc-400 leading-relaxed italic">
                                {exercise.description || "No specific instructions provided."}
                            </p>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
}
"use client";

import { useEffect, useState } from "react";
import { getExercises } from "../../api/exerciseService";
import { Plus } from "lucide-react";

export default function ExercisesPage() {
  const [exercises, setExercises] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");

  async function fetchExercises() {
    try {
      setLoading(true);
      const res = await getExercises();
      console.log("Response from getExercises:", res);
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

  // Filter exercises
  const filteredExercises = exercises.filter(exercise =>
    exercise.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="min-h-screen  py-6 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl mx-auto">
        
        <div className="flex items-center justify-between mb-6">
          <h1 className="text-2xl font-bold ">
            Exercises
          </h1>
          <button 
                className="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium transition-colors"
            >
                <Plus className="w-4 h-4" /> Add Custom Exercise
            </button>
        </div>

       
        <div className="mb-6">
          <input
            type="text"
            placeholder="Search exercises..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full px-4 py-2.5  border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
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
            {filteredExercises.map((exercise) => (
              <div
                key={exercise.id}
                className="bg-card border border-gray-200 rounded-lg p-4 hover:border-gray-300 hover:shadow-sm transition-all cursor-pointer flex items-start gap-4"
              >
                
                <div className="w-20 h-20 bg-gray-300 rounded-lg flex-shrink-0"></div>
                
                
                <div className="flex-1 min-w-0">
                  <h3 className="font-semibold  mb-1">
                    {exercise.name}
                  </h3>
                  <p className="text-sm">
                    {exercise.muscleGroup || "General"}
                  </p>
                </div>

                
                <button className="text-sm font-medium flex-shrink-0">
                  View
                </button>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
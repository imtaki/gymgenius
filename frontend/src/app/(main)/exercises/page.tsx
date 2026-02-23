"use client";

import { useEffect, useState } from "react";
import {
  Dumbbell, Search, Box, Target, Zap,
  ChevronDown, Filter, X, Plus,
} from "lucide-react";
import { getExercises } from "../../api/exerciseService";
import AddExerciseModal from "../../../components/sections/AddExerciseModal";
import BackButton from "../../../components/ui/backbutton";
import { Exercise } from "../../../types/exercises";


const DIFFICULTY_META: Record<string, { label: string; color: string; bg: string; bars: number }> = {
  beginner:     { label: "Beginner",     color: "text-emerald-400", bg: "bg-emerald-400/10 border-emerald-400/20", bars: 1 },
  intermediate: { label: "Intermediate", color: "text-amber-400",   bg: "bg-amber-400/10   border-amber-400/20",   bars: 2 },
  advanced:     { label: "Advanced",     color: "text-red-400",     bg: "bg-red-400/10     border-red-400/20",     bars: 3 },
};

const MUSCLE_META: Record<string, string> = {
  chest:     "text-sky-400    bg-sky-400/10    border-sky-400/20",
  back:      "text-violet-400 bg-violet-400/10 border-violet-400/20",
  legs:      "text-lime-400   bg-lime-400/10   border-lime-400/20",
  shoulders: "text-orange-400 bg-orange-400/10 border-orange-400/20",
  arms:      "text-fuchsia-400 bg-fuchsia-400/10 border-fuchsia-400/20",
  core:      "text-amber-400  bg-amber-400/10  border-amber-400/20",
};

function getMuscleStyle(group = "") {
  const key = group.toLowerCase().split(/[\s/]/)[0];
  return MUSCLE_META[key] ?? "text-zinc-400 bg-zinc-800 border-zinc-700";
}

function getDiffStyle(level = "") {
  return DIFFICULTY_META[level.toLowerCase()] ?? DIFFICULTY_META.beginner;
}



function ExerciseListItem({
  exercise,
  isOpen,
  onToggle,
}: {
  exercise: Exercise;
  isOpen: boolean;
  onToggle: () => void;
}) {
  const diff   = getDiffStyle(exercise.difficulty);
  const muscle = getMuscleStyle(exercise.muscleGroup);

  return (
    <div
      className={`group relative bg-zinc-900 border rounded-2xl overflow-hidden transition-all duration-300 ${
        isOpen ? "border-zinc-700 shadow-xl shadow-black/40" : "border-zinc-800 hover:border-zinc-700"
      }`}
    >
      
      <div
        className={`absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-lime-400 to-transparent transition-opacity duration-500 ${
          isOpen ? "opacity-30" : "opacity-0 group-hover:opacity-15"
        }`}
      />

      
      <button onClick={onToggle} className="w-full text-left px-5 py-4 flex items-center gap-4">

        
        <div
          className={`w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all duration-200 ${
            isOpen ? "bg-lime-400/15 text-lime-400" : "bg-zinc-800 text-zinc-500 group-hover:bg-zinc-700 group-hover:text-zinc-300"
          }`}
        >
          <Dumbbell className="w-4 h-4" />
        </div>

        
        <div className="flex-1 min-w-0">
          <p className="text-sm font-bold text-zinc-100 truncate mb-1.5">{exercise.name}</p>
          <div className="flex items-center gap-2 flex-wrap">
            {exercise.muscleGroup && (
              <span className={`text-[9px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-widest ${muscle}`}>
                {exercise.muscleGroup}
              </span>
            )}
            {exercise.difficulty && (
              <span className={`text-[9px] font-semibold uppercase tracking-widest ${diff.color}`}>
                {diff.label}
              </span>
            )}
            {exercise.equipment && (
              <span className="text-[9px] text-zinc-600 font-mono capitalize">
                {exercise.equipment.replace(/_/g, " ")}
              </span>
            )}
          </div>
        </div>

        
        <div className="flex items-center gap-3 shrink-0">
          <div className="hidden sm:block">
  
          </div>
          <div
            className={`w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-300 ${
              isOpen ? "bg-lime-400/15 text-lime-400" : "bg-zinc-800 text-zinc-600"
            }`}
          >
            <ChevronDown className={`w-3.5 h-3.5 transition-transform duration-300 ${isOpen ? "rotate-180" : ""}`} />
          </div>
        </div>
      </button>

      
      {isOpen && (
        <div className="px-5 pb-5">
          <div className="h-px bg-zinc-800 mb-4" />

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mb-4">
            {/* Equipment */}
            <div className="bg-zinc-800/50 border border-zinc-800 rounded-xl p-3 space-y-1.5">
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest font-bold">Equipment</p>
              <div className="flex items-center gap-2">
                <Box className="w-3.5 h-3.5 text-lime-400 shrink-0" />
                <span className="text-xs text-zinc-200 capitalize font-mono">
                  {exercise.equipment?.replace(/_/g, " ") || "Bodyweight"}
                </span>
              </div>
            </div>

            {/* Primary */}
            <div className="bg-zinc-800/50 border border-zinc-800 rounded-xl p-3 space-y-1.5">
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest font-bold">Primary</p>
              <div className="flex items-center gap-2">
                <Target className="w-3.5 h-3.5 text-lime-400 shrink-0" />
                <span className="text-xs text-zinc-200 capitalize font-mono">
                  {exercise.muscleGroup || "General"}
                </span>
              </div>
            </div>

            {/* Secondary */}
            <div className="bg-zinc-800/50 border border-zinc-800 rounded-xl p-3 space-y-1.5">
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest font-bold">Secondary</p>
              <div className="flex items-center gap-2">
                <Zap className="w-3.5 h-3.5 text-zinc-600 shrink-0" />
                <span className="text-xs text-zinc-400 capitalize font-mono">
                  {exercise.secondaryMuscles || "None"}
                </span>
              </div>
            </div>
          </div>

          {/* Instructions */}
          <div className="bg-zinc-950/70 border border-zinc-800/60 rounded-xl p-4">
            <p className="text-[9px] text-zinc-600 uppercase tracking-widest font-bold mb-2">Instructions</p>
            <p className="text-xs text-zinc-400 leading-relaxed font-mono">
              {exercise.description || "No specific instructions provided for this exercise."}
            </p>
          </div>
        </div>
      )}
    </div>
  );
}



function SkeletonItem() {
  return (
    <div className="bg-zinc-900 border border-zinc-800 rounded-2xl px-5 py-4 flex items-center gap-4 animate-pulse">
      <div className="w-10 h-10 rounded-xl bg-zinc-800 shrink-0" />
      <div className="flex-1 space-y-2">
        <div className="h-3 bg-zinc-800 rounded w-1/3" />
        <div className="h-2 bg-zinc-800/60 rounded w-1/5" />
      </div>
      <div className="w-7 h-7 rounded-lg bg-zinc-800" />
    </div>
  );
}



const MUSCLE_FILTERS = ["All", "Chest", "Back", "Legs", "Shoulders", "Arms", "Core"];
const DIFF_FILTERS   = ["All", "Beginner", "Intermediate", "Advanced"];

export default function ExercisesPage() {
  const [exercises,    setExercises]   = useState<Exercise[]>([]);
  const [loading,      setLoading]     = useState(true);
  const [searchTerm,   setSearchTerm]  = useState("");
  const [expandedId,   setExpandedId]  = useState<string | null>(null);
  const [muscleFilter, setMuscleFilter]= useState("All");
  const [diffFilter,   setDiffFilter]  = useState("All");
  const [showFilters,  setShowFilters] = useState(false);

  useEffect(() => {
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
    fetchExercises();
  }, []);

  const filtered = exercises.filter((ex) => {
    const matchSearch = ex.name.toLowerCase().includes(searchTerm.toLowerCase());
    const matchMuscle = muscleFilter === "All" || (ex.muscleGroup ?? "").toLowerCase().includes(muscleFilter.toLowerCase());
    const matchDiff   = diffFilter   === "All" || (ex.difficulty  ?? "").toLowerCase() === diffFilter.toLowerCase();
    return matchSearch && matchMuscle && matchDiff;
  });

  const activeFilters = [
    muscleFilter !== "All" && muscleFilter,
    diffFilter   !== "All" && diffFilter,
  ].filter(Boolean);

  function clearFilters() {
    setMuscleFilter("All");
    setDiffFilter("All");
  }

  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-0 right-1/3 w-[500px] h-[400px] bg-lime-400/[0.025] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.015]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="ex-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#ex-grid)" />
        </svg>
      </div>

      <div className="relative max-w-3xl mx-auto space-y-5">

       
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3">
            <BackButton />
            <span className="w-px h-5 bg-zinc-800" />
            <div className="w-8 h-8 rounded-lg bg-lime-400/10 border border-lime-400/20 flex items-center justify-center">
              <Dumbbell className="w-4 h-4 text-lime-400" />
            </div>
            <div className="flex items-center gap-2 text-xs uppercase tracking-widest">
              <span className="text-zinc-600">Training</span>
              <span className="text-zinc-700">/</span>
              <span className="text-zinc-100 font-semibold">Exercises</span>
            </div>
          </div>
          <AddExerciseModal />
        </div>

        
        {!loading && (
          <div className="flex items-center gap-4 px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl">
            <div className="text-center">
              <p className="text-lg font-black text-lime-400 font-mono leading-none">{exercises.length}</p>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-0.5">Total</p>
            </div>
            <div className="w-px h-8 bg-zinc-800" />
            {["Beginner", "Intermediate", "Advanced"].map((d) => {
              const count = exercises.filter(e => (e.difficulty ?? "").toLowerCase() === d.toLowerCase()).length;
              const meta  = getDiffStyle(d);
              return (
                <div key={d} className="text-center">
                  <p className={`text-sm font-black font-mono leading-none ${meta.color}`}>{count}</p>
                  <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-0.5">{meta.label.slice(0, 4)}</p>
                </div>
              );
            })}
            <div className="ml-auto text-[10px] text-zinc-600 font-mono">
              {filtered.length} shown
            </div>
          </div>
        )}

        
        <div className="flex items-center gap-2">
          <div className="relative flex-1">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" />
            <input
              type="text"
              placeholder="Search exercises…"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full bg-zinc-900 border border-zinc-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-700 font-mono focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
            />
          </div>
          <button
            onClick={() => setShowFilters(!showFilters)}
            className={`flex items-center gap-2 px-4 py-2.5 rounded-xl border text-xs font-bold uppercase tracking-widest transition-all duration-200 ${
              showFilters || activeFilters.length > 0
                ? "bg-lime-400/10 border-lime-400/30 text-lime-400"
                : "bg-zinc-900 border-zinc-800 text-zinc-500 hover:text-zinc-300 hover:border-zinc-700"
            }`}
          >
            <Filter className="w-3.5 h-3.5" />
            Filters
            {activeFilters.length > 0 && (
              <span className="w-4 h-4 rounded-full bg-lime-400 text-zinc-900 text-[9px] font-black flex items-center justify-center">
                {activeFilters.length}
              </span>
            )}
          </button>
        </div>

        
        {showFilters && (
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-4">
            
            <div>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mb-2">Muscle Group</p>
              <div className="flex flex-wrap gap-1.5">
                {MUSCLE_FILTERS.map((m) => (
                  <button
                    key={m}
                    onClick={() => setMuscleFilter(m)}
                    className={`px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-150 ${
                      muscleFilter === m
                        ? "bg-lime-400 text-zinc-900"
                        : "bg-zinc-800 text-zinc-500 hover:text-zinc-300"
                    }`}
                  >
                    {m}
                  </button>
                ))}
              </div>
            </div>

           
            <div>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mb-2">Difficulty</p>
              <div className="flex flex-wrap gap-1.5">
                {DIFF_FILTERS.map((d) => {
                  const meta = d === "All" ? null : getDiffStyle(d);
                  return (
                    <button
                      key={d}
                      onClick={() => setDiffFilter(d)}
                      className={`px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-150 ${
                        diffFilter === d
                          ? "bg-lime-400 text-zinc-900"
                          : "bg-zinc-800 text-zinc-500 hover:text-zinc-300"
                      }`}
                    >
                      {d}
                    </button>
                  );
                })}
              </div>
            </div>

            
            {activeFilters.length > 0 && (
              <button
                onClick={clearFilters}
                className="flex items-center gap-1.5 text-[10px] text-red-400 hover:text-red-300 font-bold uppercase tracking-widest transition-colors"
              >
                <X className="w-3 h-3" /> Clear filters
              </button>
            )}
          </div>
        )}

       
        {activeFilters.length > 0 && !showFilters && (
          <div className="flex items-center gap-2 flex-wrap">
            {activeFilters.map((f) => (
              <span key={f as string} className="inline-flex items-center gap-1.5 px-3 py-1 bg-lime-400/10 border border-lime-400/20 rounded-full text-[10px] text-lime-400 font-bold uppercase tracking-widest">
                {f as string}
                <button onClick={clearFilters} className="text-lime-600 hover:text-lime-400 transition-colors">
                  <X className="w-2.5 h-2.5" />
                </button>
              </span>
            ))}
          </div>
        )}

        
        {loading ? (
          <div className="space-y-2.5">
            {Array.from({ length: 6 }).map((_, i) => <SkeletonItem key={i} />)}
          </div>
        ) : filtered.length === 0 ? (
          <div className="border border-dashed border-zinc-800 rounded-2xl p-14 text-center">
            <Dumbbell className="w-8 h-8 text-zinc-800 mx-auto mb-3" />
            <p className="text-xs text-zinc-700 uppercase tracking-widest">No exercises found</p>
            {(searchTerm || activeFilters.length > 0) && (
              <button
                onClick={() => { setSearchTerm(""); clearFilters(); }}
                className="mt-3 text-[10px] text-lime-400 hover:text-lime-300 uppercase tracking-widest transition-colors"
              >
                Clear search & filters
              </button>
            )}
          </div>
        ) : (
          <div className="space-y-2.5">
            {filtered.map((exercise) => (
              <ExerciseListItem
                key={exercise.id}
                exercise={exercise}
                isOpen={expandedId === exercise.id}
                onToggle={() => setExpandedId(expandedId === exercise.id ? null : exercise.id)}
              />
            ))}
          </div>
        )}

      </div>
    </div>
  );
}
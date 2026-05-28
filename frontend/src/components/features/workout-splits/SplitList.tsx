'use client' // Needs interactivity: create/edit splits, manage exercises

import { useState } from "react";
import {
  Dumbbell, Plus, ChevronDown, ChevronUp, Trash2, Edit2,
  Search, Filter, X
} from "lucide-react";
import BackButton from "@/components/ui/backbutton";
import { WorkoutSplit } from "@/types/workouts";
import SplitForm from "./SplitForm";
import { deleteWorkoutSplit } from "@/app/api/workoutSplitService";
import Link from "next/link";

interface SplitListProps {
  initialSplits: WorkoutSplit[];
  error?: string | null;
}

export default function SplitList({ initialSplits, error }: SplitListProps) {
  const [splits, setSplits] = useState<WorkoutSplit[]>(initialSplits);
  const [loading, setLoading] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [expandedId, setExpandedId] = useState<string | number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [editingSplit, setEditingSplit] = useState<WorkoutSplit | null>(null);
  const [deleteError, setDeleteError] = useState<string | null>(null);

  const filtered = splits.filter((split) =>
    split.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleDeleteSplit = async (splitId: string | number) => {
    if (!confirm("Are you sure you want to delete this split? This will also delete all related workouts.")) {
      return;
    }

    try {
      setLoading(true);
      await deleteWorkoutSplit(splitId);
      setSplits((prev) => prev.filter((s) => s.id !== splitId));
      setDeleteError(null);
    } catch (err) {
      setDeleteError("Failed to delete split");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleSplitCreated = (newSplit: WorkoutSplit) => {
    setSplits((prev) => [newSplit, ...prev]);
    setShowForm(false);
  };

  const handleSplitUpdated = (updatedSplit: WorkoutSplit) => {
    setSplits((prev) =>
      prev.map((s) => (s.id === updatedSplit.id ? updatedSplit : s))
    );
    setEditingSplit(null);
  };

  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      {/* Background decorations */}
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-0 right-1/3 w-[500px] h-[400px] bg-lime-400/[0.025] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.015]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="split-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#split-grid)" />
        </svg>
      </div>

      <div className="relative max-w-3xl mx-auto space-y-5">
        {/* Header */}
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
              <span className="text-zinc-100 font-semibold">Splits</span>
            </div>
          </div>
          <button
            onClick={() => {
              setEditingSplit(null);
              setShowForm(!showForm);
            }}
            className="flex items-center gap-2 px-4 py-2.5 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-lime-400/10"
          >
            <Plus className="w-3.5 h-3.5" />
            New Split
          </button>
        </div>

        {/* Error messages */}
        {error && (
          <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
            {error}
          </div>
        )}
        {deleteError && (
          <div className="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
            {deleteError}
          </div>
        )}

        {/* Stats */}
        {!loading && (
          <div className="flex items-center gap-4 px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl">
            <div className="text-center">
              <p className="text-lg font-black text-lime-400 font-mono leading-none">{splits.length}</p>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-0.5">Splits</p>
            </div>
            <div className="w-px h-8 bg-zinc-800" />
            <div className="text-center flex-1">
              <p className="text-sm font-mono text-zinc-400 leading-none">
                {splits.reduce((acc, s) => acc + s.exercises_count, 0)} exercises
              </p>
              <p className="text-[9px] text-zinc-600 uppercase tracking-widest mt-0.5">Total</p>
            </div>
          </div>
        )}

        {/* Form */}
        {showForm && (
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-sm font-bold text-zinc-100">Create New Split</h2>
              <button
                onClick={() => setShowForm(false)}
                className="text-zinc-600 hover:text-zinc-300"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
            <SplitForm onSuccess={handleSplitCreated} onCancel={() => setShowForm(false)} />
          </div>
        )}

        {editingSplit && (
          <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-sm font-bold text-zinc-100">Edit Split</h2>
              <button
                onClick={() => setEditingSplit(null)}
                className="text-zinc-600 hover:text-zinc-300"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
            <SplitForm
              initialData={editingSplit}
              onSuccess={handleSplitUpdated}
              onCancel={() => setEditingSplit(null)}
            />
          </div>
        )}

        {/* Search */}
        {!showForm && !editingSplit && (
          <div className="relative">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" />
            <input
              type="text"
              placeholder="Search splits..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full bg-zinc-900 border border-zinc-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-zinc-200 placeholder:text-zinc-700 font-mono focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all"
            />
          </div>
        )}

        {/* Splits list */}
        {loading ? (
          <div className="space-y-2.5">
            {Array.from({ length: 3 }).map((_, i) => (
              <div key={i} className="bg-zinc-900 border border-zinc-800 rounded-2xl px-5 py-4 flex items-center gap-4 animate-pulse">
                <div className="w-10 h-10 rounded-xl bg-zinc-800 shrink-0" />
                <div className="flex-1 space-y-2">
                  <div className="h-3 bg-zinc-800 rounded w-1/3" />
                  <div className="h-2 bg-zinc-800/60 rounded w-1/5" />
                </div>
              </div>
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="border border-dashed border-zinc-800 rounded-2xl p-14 text-center">
            <Dumbbell className="w-8 h-8 text-zinc-800 mx-auto mb-3" />
            <p className="text-xs text-zinc-700 uppercase tracking-widest">
              {searchTerm ? "No splits found" : "No splits yet"}
            </p>
            {!searchTerm && (
              <button
                onClick={() => {
                  setEditingSplit(null);
                  setShowForm(true);
                }}
                className="mt-3 text-[10px] text-lime-400 hover:text-lime-300 uppercase tracking-widest transition-colors"
              >
                Create your first split
              </button>
            )}
          </div>
        ) : (
          <div className="space-y-2.5">
            {filtered.map((split) => (
              <Link
                key={split.id}
                href={`/workouts/splits/${split.id}`}
                className="group relative bg-zinc-900 border rounded-2xl overflow-hidden transition-all duration-300 hover:border-zinc-700 hover:shadow-xl hover:shadow-black/40 cursor-pointer"
              >
                <div className="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-lime-400 to-transparent transition-opacity duration-500 opacity-0 group-hover:opacity-30" />
                
                <div className="px-5 py-4 flex items-center gap-4">
                  <div className="w-10 h-10 rounded-xl bg-zinc-800 group-hover:bg-zinc-700 flex items-center justify-center shrink-0 transition-all duration-200">
                    <Dumbbell className="w-4 h-4 text-lime-400" />
                  </div>

                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-bold text-zinc-100 truncate">{split.name}</p>
                    {split.description && (
                      <p className="text-xs text-zinc-600 truncate mt-0.5">{split.description}</p>
                    )}
                    <div className="flex items-center gap-2 mt-1.5 flex-wrap">
                      <span className="text-[9px] font-semibold px-2 py-0.5 rounded-full border border-lime-400/20 bg-lime-400/10 text-lime-400 uppercase tracking-widest">
                        {split.exercises_count} exercises
                      </span>
                    </div>
                  </div>

                  <div className="flex items-center gap-2 shrink-0">
                    <button
                      onClick={(e) => {
                        e.preventDefault();
                        setEditingSplit(split);
                        setShowForm(false);
                      }}
                      className="p-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-500 hover:text-zinc-300 transition-all"
                      title="Edit split"
                    >
                      <Edit2 className="w-3.5 h-3.5" />
                    </button>
                    <button
                      onClick={(e) => {
                        e.preventDefault();
                        handleDeleteSplit(split.id);
                      }}
                      className="p-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 transition-all"
                      title="Delete split"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

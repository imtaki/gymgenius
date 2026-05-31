import { getWorkoutSplits } from "@/app/api/workoutSplitService";
import SplitList from "@/components/features/workout-splits/SplitList";
import { WorkoutSplit } from "@/types/workouts";
import Link from "next/link";

export const metadata = {
  title: "Workout Splits",
  description: "Manage your workout split templates",
};

export default async function WorkoutSplitsPage() {
  let splits = [] as WorkoutSplit[];
  let error = null;

  try {
    splits = await getWorkoutSplits();
  } catch (err) {
    error = "Failed to load workout splits";
    console.error(err);
  }

  if (error) {
    return (
      <div className="min-h-screen bg-zinc-950 flex items-center justify-center p-6">
        <div className="text-center">
          <p className="text-red-400 mb-4">{error}</p>
          <Link href="/" className="text-emerald-400 hover:text-emerald-300">
            Back to Home
          </Link>
        </div>
      </div>
    );
  }

  return <SplitList initialSplits={splits} error={error} />;
}

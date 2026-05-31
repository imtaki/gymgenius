import { getWorkoutById } from "@/app/api/workoutSessionService";
import WorkoutSession from "@/components/features/workouts/WorkoutSession";
import Link from "next/dist/client/link";

export const metadata = {
  title: "Workout Session",
  description: "Log your workout session",
};

interface WorkoutPageProps {
  params: { id: string };
}

export default async function WorkoutPage({ params }: WorkoutPageProps) {
  let workout = null;
  let error = null;

  try {
    workout = await getWorkoutById(params.id);
  } catch (err) {
    error = "Failed to load workout";
    console.error(err);
  }

  if (!workout) {
    return (
      <div className="min-h-screen bg-zinc-950 flex items-center justify-center p-6">
        <div className="text-center">
          <p className={`mb-4 ${error ? "text-red-400" : "text-zinc-400"}`}>
            {error || "Workout not found"}
          </p>
          <Link href="/workouts" className="text-emerald-400 hover:text-emerald-300">
            Back to Workouts
          </Link>
        </div>
      </div>
    );
  }

  return <WorkoutSession initialWorkout={workout} />;
}

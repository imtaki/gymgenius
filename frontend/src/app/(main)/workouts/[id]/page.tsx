import { getWorkoutById } from "@/app/api/workoutSessionService";
import WorkoutSession from "@/components/features/workouts/WorkoutSession";

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
          <p className="text-zinc-400 mb-4">{error || "Workout not found"}</p>
          <a href="/workouts" className="text-lime-400 hover:text-lime-300">
            Back to Workouts
          </a>
        </div>
      </div>
    );
  }

  return <WorkoutSession initialWorkout={workout} />;
}

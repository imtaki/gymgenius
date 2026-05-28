import { getWorkouts } from "@/app/api/workoutSessionService";
import WorkoutList from "@/components/features/workouts/WorkoutList";

export const metadata = {
  title: "Workouts",
  description: "View and manage your workout sessions",
};

export default async function WorkoutsPage() {
  let workouts = [];
  let error = null;

  try {
    workouts = await getWorkouts();
  } catch (err) {
    error = "Failed to load workouts";
    console.error(err);
  }

  return <WorkoutList initialWorkouts={workouts} error={error} />;
}

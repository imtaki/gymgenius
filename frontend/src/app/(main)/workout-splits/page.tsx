import { getWorkoutSplits } from "@/app/api/workoutSplitService";
import SplitList from "@/components/features/workout-splits/SplitList";

export const metadata = {
  title: "Workout Splits",
  description: "Manage your workout split templates",
};

export default async function WorkoutSplitsPage() {
  let splits = [];
  let error = null;

  try {
    splits = await getWorkoutSplits();
  } catch (err) {
    error = "Failed to load workout splits";
    console.error(err);
  }

  return <SplitList initialSplits={splits} error={error} />;
}

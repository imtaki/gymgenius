import { getWorkoutSplitById } from "@/app/api/workoutSplitService";
import SplitDetail from "@/components/features/workout-splits/SplitDetail";
import Link from "next/dist/client/link";

export const metadata = {
  title: "Workout Split Details",
  description: "Manage exercises in your workout split",
};

interface SplitPageProps {
  params: { id: string };
}

export default async function SplitPage({ params }: SplitPageProps) {
  let split = null;
  let error = null;

  try {
    split = await getWorkoutSplitById(params.id);
  } catch (err) {
    error = "Failed to load split";
    console.error(err);
  }

  if (!split) {
    return (
      <div className="min-h-screen bg-zinc-950 flex items-center justify-center p-6">
        <div className="text-center">
          <p className="text-zinc-400 mb-4">{error || "Split not found"}</p>
          <Link href="/workout-splits" className="text-lime-400 hover:text-lime-300">
            Back to Splits
          </Link>
        </div>
      </div>
    );
  }

  return <SplitDetail initialSplit={split} />;
}

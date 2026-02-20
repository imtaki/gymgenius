import BackButton from "../../../components/ui/backbutton";
import NutritionClient from "./nutritionClient";
import { UtensilsCrossed } from "lucide-react";

export default function NutritionPage() {
  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      <div className="max-w-7xl mx-auto">

       
        <div className="flex items-center gap-3 mb-8">
          <BackButton />

         
          <span className="w-px h-5 bg-zinc-800" />

          
          <div className="w-8 h-8 rounded-lg bg-lime-400/10 border border-lime-400/20 flex items-center justify-center">
            <UtensilsCrossed className="w-4 h-4 text-lime-400" />
          </div>

          
          <div className="flex items-center gap-2 text-xs uppercase tracking-widest">
            <span className="text-zinc-600">Meal plans</span>
            <span className="text-zinc-700">/</span>
            <span className="text-zinc-100 font-semibold">Nutrition Tracker</span>
          </div>
        </div>

        
        <NutritionClient />

      </div>
    </div>
  );
}
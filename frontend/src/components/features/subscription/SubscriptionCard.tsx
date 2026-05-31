'use client'; // Interactive subscription tier selection

import { Check } from "lucide-react";
import { SubscriptionTier, SUBSCRIPTION_TIERS } from "@/lib/subscriptionSchemas";
import { cn } from "@/lib/utils";

interface SubscriptionCardProps {
  tier: SubscriptionTier;
  isCurrentTier: boolean;
  onSelect: (tier: SubscriptionTier) => void;
  isLoading?: boolean;
}

export function SubscriptionCard({
  tier,
  isCurrentTier,
  onSelect,
  isLoading = false,
}: SubscriptionCardProps) {
  const tierData = SUBSCRIPTION_TIERS[tier];
  const isPro = tier === "pro" || tier === "pro_plus";

  return (
    <div
      className={cn(
        "relative flex flex-col rounded-lg border-2 p-6 transition-all",
        isCurrentTier
          ? "border-emerald-600/50 bg-emerald-950/20"
          : "border-zinc-800 bg-zinc-950 hover:border-zinc-700"
      )}
    >
      {isPro && !isCurrentTier && (
        <div className="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-1 text-xs font-semibold text-white">
          Popular
        </div>
      )}

      <div className="mb-6">
        <h3 className="text-2xl font-bold text-white">{tierData.name}</h3>
        <p className="mt-2 text-sm text-zinc-400">{tierData.description}</p>
        <div className="mt-4 flex items-baseline gap-1">
          <span className="text-4xl font-bold text-white">
            ${tierData.price}
          </span>
          {tierData.price > 0 && <span className="text-zinc-400">/month</span>}
        </div>
      </div>

      <ul className="mb-6 flex-1 space-y-3">
        {tierData.features.map((feature, idx) => (
          <li key={idx} className="flex items-start gap-3">
            <Check className="mt-1 h-5 w-5 flex-shrink-0 text-emerald-500" />
            <span className="text-sm text-zinc-300">{feature}</span>
          </li>
        ))}
      </ul>

      <button
        onClick={() => onSelect(tier)}
        disabled={isCurrentTier || isLoading}
        className={cn(
          "w-full rounded-lg px-4 py-2 font-semibold transition-colors",
          isCurrentTier
            ? "bg-zinc-800 text-zinc-400 cursor-default"
            : "bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
        )}
      >
        {isCurrentTier ? "Current Plan" : `Upgrade to ${tierData.name}`}
      </button>
    </div>
  );
}

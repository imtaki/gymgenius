'use client'; // Needs interactivity: subscription tier selection and upgrades

import { useCallback, useState } from "react";
import BackButton from "@/components/ui/backbutton";
import { SubscriptionCard } from "@/components/features/subscription/SubscriptionCard";
import { UpgradeConfirmDialog } from "@/components/features/subscription/UpgradeConfirmDialog";
import { updateSubscriptionTier } from "@/app/api/subscriptionService";
import { SubscriptionTier } from "@/lib/subscriptionSchemas";

interface SubscriptionPageClientProps {
  initialTier: SubscriptionTier;
}

export function SubscriptionPageClient({ initialTier }: SubscriptionPageClientProps) {
  const [currentTier, setCurrentTier] = useState<SubscriptionTier>(initialTier);
  const [selectedTier, setSelectedTier] = useState<SubscriptionTier | null>(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isUpdating, setIsUpdating] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const handleSelectTier = useCallback((tier: SubscriptionTier) => {
    setSelectedTier(tier);
    setIsDialogOpen(true);
  }, []);

  const handleConfirmUpgrade = useCallback(async () => {
    if (!selectedTier) return;
    setIsUpdating(true);
    setError(null);

    try {
      await updateSubscriptionTier(selectedTier);
      setCurrentTier(selectedTier);
      setSuccess('Successfully upgraded to ' + selectedTier + ' plan!');
      setIsDialogOpen(false);
      setSelectedTier(null);
      setTimeout(() => setSuccess(null), 3000);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to update subscription");
    } finally {
      setIsUpdating(false);
    }
  }, [selectedTier]);

  return (
    <div className="min-h-screen bg-black">
      <div className="mx-auto max-w-6xl px-4 py-8">
        <BackButton />
        <div className="mb-12 mt-8">
          <h1 className="text-4xl font-bold text-white">Subscription Plans</h1>
          <p className="mt-2 text-lg text-zinc-400">
            Choose the perfect plan for your fitness journey
          </p>
        </div>

        {error && <div className="mb-6 rounded-lg border border-red-900/50 bg-red-950/30 p-4 text-red-400">{error}</div>}
        {success && <div className="mb-6 rounded-lg border border-emerald-900/50 bg-emerald-950/30 p-4 text-emerald-400">{success}</div>}

        <div className="grid gap-8 md:grid-cols-3">
          <SubscriptionCard
            tier="free"
            isCurrentTier={currentTier === "free"}
            onSelect={handleSelectTier}
            isLoading={isUpdating}
          />
          <SubscriptionCard
            tier="pro"
            isCurrentTier={currentTier === "pro"}
            onSelect={handleSelectTier}
            isLoading={isUpdating}
          />
          <SubscriptionCard
            tier="pro_plus"
            isCurrentTier={currentTier === "pro_plus"}
            onSelect={handleSelectTier}
            isLoading={isUpdating}
          />
        </div>
      </div>

      <UpgradeConfirmDialog
        isOpen={isDialogOpen}
        selectedTier={selectedTier}
        currentTier={currentTier}
        isLoading={isUpdating}
        onConfirm={handleConfirmUpgrade}
        onCancel={() => {
          setIsDialogOpen(false);
          setSelectedTier(null);
        }}
      />
    </div>
  );
}

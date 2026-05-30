`use client`;

import { useEffect, useState } from "react";
import BackButton from "@/components/ui/backbutton";
import { SubscriptionCard } from "@/components/features/subscription/SubscriptionCard";
import { UpgradeConfirmDialog } from "@/components/features/subscription/UpgradeConfirmDialog";
import { updateSubscriptionTier } from "@/app/api/subscriptionService";
import { getUser } from "@/app/api/authService";
import { SubscriptionTier } from "@/lib/subscriptionSchemas";

export default function SubscriptionPage() {
  const [currentTier, setCurrentTier] = useState<SubscriptionTier>("free");
  const [selectedTier, setSelectedTier] = useState<SubscriptionTier | null>(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [isUpdating, setIsUpdating] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  useEffect(() => {
    try {
      const user = getUser();
      setCurrentTier("free");
      setIsLoading(false);
    } catch (err) {
      setError("Failed to load subscription information");
      setIsLoading(false);
    }
  }, []);

  const handleSelectTier = (tier: SubscriptionTier) => {
    setSelectedTier(tier);
    setIsDialogOpen(true);
  };

  const handleConfirmUpgrade = async () => {
    if (!selectedTier) return;
    setIsUpdating(true);
    setError(null);

    try {
      await updateSubscriptionTier(selectedTier);
      setCurrentTier(selectedTier);
      setSuccess(`Successfully upgraded to ${selectedTier} plan!`);
      setIsDialogOpen(false);
      setSelectedTier(null);
      setTimeout(() => setSuccess(null), 3000);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to update subscription");
    } finally {
      setIsUpdating(false);
    }
  };

  if (isLoading) {
    return (
      <div className="flex min-h-screen items-center justify-center">
        <p className="text-gray-600">Loading subscription information...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-gray-50 to-white">
      <div className="mx-auto max-w-6xl px-4 py-8">
        <BackButton />
        <div className="mb-12 mt-8">
          <h1 className="text-4xl font-bold text-gray-900">Subscription Plans</h1>
          <p className="mt-2 text-lg text-gray-600">
            Choose the perfect plan for your fitness journey
          </p>
        </div>

        {error && <div className="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">{error}</div>}
        {success && <div className="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">{success}</div>}

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

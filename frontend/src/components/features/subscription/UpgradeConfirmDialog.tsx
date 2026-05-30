`use client`;

import { SubscriptionTier, SUBSCRIPTION_TIERS } from "@/lib/subscriptionSchemas";

interface UpgradeConfirmDialogProps {
  isOpen: boolean;
  selectedTier: SubscriptionTier | null;
  currentTier: SubscriptionTier;
  isLoading: boolean;
  onConfirm: () => void;
  onCancel: () => void;
}

export function UpgradeConfirmDialog({
  isOpen,
  selectedTier,
  currentTier,
  isLoading,
  onConfirm,
  onCancel,
}: UpgradeConfirmDialogProps) {
  if (!isOpen || !selectedTier) return null;

  const selectedData = SUBSCRIPTION_TIERS[selectedTier];
  const currentData = SUBSCRIPTION_TIERS[currentTier];

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div className="max-w-sm rounded-lg bg-white p-6 shadow-xl">
        <h2 className="mb-4 text-xl font-bold">
          {selectedTier === currentTier ? "Already subscribed" : "Confirm upgrade"}
        </h2>
        <p className="mb-6 text-gray-600">
          {selectedTier === currentTier
            ? `You are already on the ${currentData.name} plan.`
            : `Upgrade from ${currentData.name} to ${selectedData.name} plan?`}
        </p>
        <div className="flex gap-3">
          <button
            onClick={onCancel}
            disabled={isLoading}
            className="flex-1 rounded-lg border-2 border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            onClick={onConfirm}
            disabled={isLoading || selectedTier === currentTier}
            className="flex-1 rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isLoading ? "Processing..." : "Confirm"}
          </button>
        </div>
      </div>
    </div>
  );
}

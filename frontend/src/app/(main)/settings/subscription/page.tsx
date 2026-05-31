import { getUser } from "@/app/api/authService";
import { SubscriptionPageClient } from "@/components/features/subscription/SubscriptionPageClient";
import { SubscriptionTier } from "@/lib/subscriptionSchemas";

export const metadata = {
  title: "Subscription Plans",
  description: "Choose the perfect subscription plan for your fitness journey",
};

export default async function SubscriptionPage() {
  let userTier: SubscriptionTier = "free";

  try {
    const user = getUser();
    // Update this line once getUser returns the full user object with subscription info
    // userTier = user.subscription?.tier || "free";
  } catch (err) {
    console.error("Failed to load user subscription info:", err);
    // Fallback to free tier
    userTier = "free";
  }

  return <SubscriptionPageClient initialTier={userTier} />;
}

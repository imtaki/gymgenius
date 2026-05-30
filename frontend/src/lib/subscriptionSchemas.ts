import { z } from "zod";

export const subscriptionTierSchema = z.enum(["free", "pro", "pro_plus"]);

export const updateSubscriptionSchema = z.object({
  tier: subscriptionTierSchema,
});

export type SubscriptionTier = z.infer<typeof subscriptionTierSchema>;
export type UpdateSubscriptionInput = z.infer<typeof updateSubscriptionSchema>;

export const SUBSCRIPTION_TIERS = {
  free: {
    name: "Free",
    price: 0,
    description: "Get started with basic features",
    features: [
      "Up to 3 active workout programs",
      "Basic meal logging",
      "Weekly progress reports",
      "Community support",
    ],
  },
  pro: {
    name: "Pro",
    price: 9.99,
    description: "Unlock advanced training features",
    features: [
      "Unlimited workout programs",
      "Advanced meal planning",
      "Daily progress reports",
      "Priority support",
      "Custom workout templates",
      "AI-powered recommendations",
    ],
  },
  pro_plus: {
    name: "Pro+",
    price: 19.99,
    description: "Everything you need for complete fitness mastery",
    features: [
      "All Pro features",
      "1-on-1 coaching sessions",
      "Custom nutrition plans",
      "Real-time performance analytics",
      "VIP support (24/7)",
      "Exclusive content & webinars",
      "Export to PDF reports",
    ],
  },
} as const;

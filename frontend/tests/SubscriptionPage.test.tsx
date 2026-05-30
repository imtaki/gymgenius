import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import SubscriptionPage from '@/app/(main)/settings/subscription/page';

vi.mock('@/app/api/subscriptionService', () => ({
  updateSubscriptionTier: vi.fn(),
}));

vi.mock('@/app/api/authService', () => ({
  getUser: vi.fn(() => ({ subscription_tier: 'free' })),
}));

describe('Subscription mock-checkout', () => {
  it('submits the correct tier when confirming an upgrade', async () => {
    const user = userEvent.setup();
    const { updateSubscriptionTier } = await import('@/app/api/subscriptionService');

    render(<SubscriptionPage />);

    // Wait for the page to finish loading and render buttons
    const upgradeButton = await screen.findByRole('button', { name: /Upgrade to Pro/i });

    await user.click(upgradeButton);

    const confirmButton = await screen.findByRole('button', { name: /Confirm/i });
    await user.click(confirmButton);

    await waitFor(() => {
      expect(updateSubscriptionTier).toHaveBeenCalledWith('pro');
    });
  });
});

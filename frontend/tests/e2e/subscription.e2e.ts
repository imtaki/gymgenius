import { test, expect } from '@playwright/test';

test.describe('Subscription flow', () => {
  test('User can log in and upgrade to Pro+', async ({ page }) => {
    await page.goto('/login');

    await page.fill('[name=email]', 'e2euser@mailinator.com');
    await page.fill('[name=password]', 'Password123!');
    await page.click('[type=submit]');

    await expect(page).toHaveURL('/dashboard');

    // Navigate to settings -> subscription
    await page.goto('/settings/subscription');

    // Click upgrade for Pro+
    await page.click('text=Upgrade to Pro+');
    await page.click('text=Confirm');

    // Expect success message
    await expect(page.locator('text=Successfully upgraded')).toBeVisible();
  });
});

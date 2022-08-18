import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";

test("Test the Account recharging page works @hipanel-module-finance @client @seller", async ({ clientPage, sellerPage }) => {
  const pageUrl = "/merchant/pay/deposit";
  for (let page of [clientPage, sellerPage]) {
    await page.goto(pageUrl);
    await expect(page).toHaveTitle("Account recharging");
    await expect(page.locator("//input[@id='depositform-amount']")).toBeVisible();
    await expect(page.locator("//button[@type='submit']")).toBeVisible();
    await expect(page.locator("h4:text('Important information')")).toBeVisible();
    await expect(page.locator("p:text('Remember to return to the site after successful payment!')")).toBeVisible();
  }
});

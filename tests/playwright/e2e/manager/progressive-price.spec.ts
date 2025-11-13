import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import PriceHelper from "@hipanel-module-finance/Helper/PriceHelper";
import ViewTableHelper from "@hipanel-module-finance/Helper/ViewTableHelper";

test("Test the Progressive Price feature works @hipanel-module-finance @manager", {
    tag: '@dedicated-server',
}, async ({ page }) => {
  const planName = "TEST-PROGRESSIVE-PRICE-TEMPLATE" + Math.random().toString(36).substring(7);
  const priceHelper = new PriceHelper(page);
  const viewTable = new ViewTableHelper(page);

  await priceHelper.createPlan(planName);

  await expect(page.locator("h1")).toContainText(planName);

  await viewTable.assertCellEquals('Name', planName);
  await viewTable.assertCellEquals('Type', 'template');
  await viewTable.assertCellEquals('Status', 'ok');

  await priceHelper.createProgressivePrice(planName);

  await expect(page.getByRole("cell", { name: "$30.00 per Item over 0 Item $0.0085 per Item over 1 Item $0.008 per Item over 2 Item $0.0075 per Item over 3 Item" })).toBeVisible();

  await expect(page.getByText("Number of IPs")).toBeVisible();

  page.on("dialog", async dialog => {
    await dialog.accept();
  });

  await priceHelper.deleteProgressivePriceItems();

  await page.locator('input[name="selection_all"]').check();
  await page.getByRole("button", { name: "Update" }).click();
  await expect( page.locator('input#templateprice-0-class')).toBeHidden();
  await page.getByRole("button", { name: "Cancel" }).click();

  await page.getByTestId("delete").click();
});

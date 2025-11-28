import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import PriceHelper from "@hipanel-module-finance/Helper/PriceHelper";
import ViewTableHelper from "@hipanel-module-finance/Helper/ViewTableHelper";
import { faker } from "@faker-js/faker";

test("the Progressive Price feature works @hipanel-module-finance @manager", {
  tag: "@dedicated-server",
}, async ({ page }) => {
  const planName = "TEST-PROGRESSIVE-PRICE-TEMPLATE-" + faker.string.nanoid(10).toUpperCase();
  const priceHelper = new PriceHelper(page);
  const viewTable = new ViewTableHelper(page);

  await priceHelper.createPlan(planName);

  await expect(page.locator("h1")).toContainText(planName);

  await viewTable.assertCellEquals("Name", planName);
  await viewTable.assertCellEquals("Type", "template");
  await viewTable.assertCellEquals("Status", "ok");

  await priceHelper.createProgressivePrice(planName);

  await expect(page.getByText("Number of IPs")).toBeVisible();
  await expect(page.getByRole("cell", {
    name: "First 1 Item $30.00 "
      + "Next 1 Item $0.0085 (-100%) "
      + "Next 1 Item $0.008 (-6%) "
      + "Over 3 Item $0.0075 (-6%) ",
  })).toBeVisible();


  page.on("dialog", async dialog => {
    await dialog.accept();
  });

  await priceHelper.deleteProgressivePriceItems();

  await expect(page.getByText("Number of IPs")).toBeVisible();
  await expect(page.getByText("$30.00 per Item")).toBeVisible();
});

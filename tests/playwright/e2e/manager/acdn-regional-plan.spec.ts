import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import PriceHelper from "@hipanel-module-finance/Helper/PriceHelper";
import { faker } from "@faker-js/faker";

test("Anycast CDN plan lists regional prices of each metric together @hipanel-module-finance @manager @anycast-cdn", async ({ page }) => {
  const planName = "TEST-ACDN-REGIONAL-" + faker.string.nanoid(10).toUpperCase();
  const priceHelper = new PriceHelper(page);

  await priceHelper.createPlan(planName);
  await page.getByRole("link", { name: "Create prices" }).click();
  await page.locator("a").filter({ hasText: /^Create prices$/ }).click();
  await page.locator("#select2-type-container").click();
  await page.getByRole("option", { name: "Anycast CDN" }).click();
  await page.getByRole("button", { name: "Proceed to creation" }).click();
  await expect(page.getByRole("heading", { name: "Create suggested prices" })).toBeVisible();
  await page.getByRole("button", { name: "Save" }).click();

  await expect(page.locator("h1")).toContainText(planName);

  // Grouped rows hold a table of their own, one row per region: count the grid's rows only.
  const gridRows = page.locator("div[role=grid] tbody > tr[data-key]");
  const regionalRows = gridRows.filter({ hasText: "ASIA" });
  await expect(regionalRows, "monthly and overuse of traffic and of 95th, one row each").toHaveCount(4);
  for (const row of await regionalRows.all()) {
    await expect(row).toContainText("EUROPE");
    await expect(row).toContainText("USA");
  }
  await expect(regionalRows.filter({ hasText: "CDN traffic overuse" })).toHaveCount(1);
  await expect(regionalRows.filter({ hasText: "CDN bandwidth overuse" })).toHaveCount(1);

  const types = (await gridRows.locator(":scope > td:nth-child(4)").allInnerTexts())
    .map(text => text.replace(/\s*(monthly|overuse|feature)\s*$/, "").trim());
  expect(types, "Total prices and RPS keep their own rows").toEqual(expect.arrayContaining([
    "CDN traffic overuse", "CDN bandwidth overuse", "RPS overuse",
  ]));
});

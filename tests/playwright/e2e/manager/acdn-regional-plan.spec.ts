import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import PriceHelper from "@hipanel-module-finance/Helper/PriceHelper";
import { faker } from "@faker-js/faker";

test("Anycast CDN plan lists regional prices of each metric together @hipanel-module-finance @manager", async ({ page }) => {
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
  const types = (await page.locator("table tbody tr td:nth-child(4)").allInnerTexts())
    .map(text => text.replace(/\s*(monthly|overuse|feature)\s*$/, "").trim());

  const expectedOrder = [
    "CDN traffic overuse",
    "CDN traffic overuse (Asia)",
    "CDN traffic overuse (Europe)",
    "CDN traffic overuse (USA)",
    "CDN bandwidth overuse",
    "CDN bandwidth overuse (Asia)",
    "CDN bandwidth overuse (Europe)",
    "CDN bandwidth overuse (USA)",
    "RPS overuse",
  ];
  const positions = expectedOrder.map(name => types.indexOf(name));

  expect(positions, `types found: ${types.join(" | ")}`).not.toContain(-1);
  expect(positions).toEqual([...positions].sort((a, b) => a - b));
});

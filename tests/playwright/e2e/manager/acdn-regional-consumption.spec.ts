import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";

test("Anycast CDN consumption lists regional traffic columns in a group @hipanel-module-finance @manager @anycast-cdn", async ({ page }) => {
  await page.goto("/finance/consumption/index?ConsumptionSearch[class]=anycastcdn");

  const headers = await page.locator("table thead th").allInnerTexts();
  const names = headers.map(text => text.trim());
  const expectedOrder = [
    "Traffic ASIA", "Traffic EUROPE", "Traffic USA",
    "95th ASIA", "95th EUROPE", "95th USA",
    "RPS",
  ];

  const positions = expectedOrder.map(name => names.indexOf(name));
  expect(positions, `columns found: ${names.join(" | ")}`).not.toContain(-1);
  expect(positions).toEqual([...positions].sort((a, b) => a - b));
  expect(names).toContain("CDN Traffic Total");
});

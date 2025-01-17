import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";

test("Test the Progressive Price feature works @hipanel-module-finance @manager", async ({ page }) => {

  const planName = "TEST-PROGRESSIVE-PRICE-TEMPLATE" + Math.random().toString(36).substring(7);

  await page.goto("/finance/plan/index");
  await page.getByRole("link", { name: "Create" }).click();
  await page.getByLabel("Name").fill(planName);
  await page.getByLabel("Type").selectOption("template");
  await page.locator("#select2-plan-client-container").click();
  await page.getByRole("option", { name: "hipanel_test_reseller" }).click();
  await page.locator("#select2-plan-currency-container").click();
  await page.getByRole("option", { name: "USD" }).click();
  await page.getByRole("button", { name: "Save" }).click();
  await expect(page.locator("h1")).toContainText(planName);
  await expect(page.getByRole("cell", { name: "template", exact: true })).toBeVisible();
  await expect(page.getByRole("cell", { name: "ok" })).toBeVisible();
  await page.getByRole("link", { name: "Create prices" }).click();
  await page.locator("a").filter({ hasText: /^Create prices$/ }).click();
  await page.locator("#select2-type-container").click();
  await expect(page.getByRole("heading", { name: "Create prices" })).toBeVisible();
  await page.getByRole("option", { name: "Dedicated Server" }).click();
  await page.getByRole("button", { name: "Proceed to creation" }).click();
  await expect(page.getByRole("heading", { name: "Create suggested prices" })).toBeVisible();
  await expect(page.getByRole("heading", { name: `Tariff: ${planName}` })).toBeVisible();

  await page.locator(".remove-item").first().click();
  await page.locator(".remove-item").first().click();
  let pricesCount = await page.locator(".form-instance").count();

  for (let i = 0; i < pricesCount - 1; i++) {
    await page.locator("div:nth-child(2) > div > .form-instance > .col-md-1 > .remove-item").click();
  }

  await page.getByTestId("add progression").click();
  await page.locator("#threshold-0-1-quantity").fill("1");
  await page.locator("#threshold-0-1-price").fill("0.0085");
  await page.getByTestId("add progression").click();
  await page.locator("#threshold-0-2-quantity").fill("2");
  await page.locator("#threshold-0-2-price").fill("0.0080");
  await page.getByTestId("add progression").click();
  await page.locator("#threshold-0-3-quantity").fill("3");
  await page.locator("#threshold-0-3-price").fill("0.0075");

  await page.getByRole("button", { name: "Save" }).click();

  await expect(page.getByRole("cell", { name: "$30.00 per Item over 0 Item $0.0085 per Item over 1 Item $0.008 per Item over 2 Item $0.0075 per Item over 3 Item" })).toBeVisible();

  await expect(page.getByText("Number of IPs")).toBeVisible();
  await page.locator("input[name=\"selection_all\"]").check();
  await page.getByRole("button", { name: "Update" }).click();
  await page.getByRole("button", { name: "" }).nth(2).click();
  await page.locator("#threshold-0-2-price").fill("0.0075");
  await page.getByRole("button", { name: "Save" }).click();
  await expect(page.getByRole("grid")).toContainText("$30.00 per Item over 0 Item $0.0085 per Item over 1 Item $0.0075 per Item over 2 Item");

  page.on("dialog", async dialog => {
    await dialog.accept();
  });
  await page.getByTestId("delete").click();
  await expect(page.getByRole("heading", { name: "Tariff plans" })).toBeVisible();
});

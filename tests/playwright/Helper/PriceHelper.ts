import { expect, Page } from "@playwright/test";

export default class PriceHelper {
  constructor(readonly page: Page) {
  }

  async createPlan(planName: string) {
    await this.page.goto("/finance/plan/index");
    await this.page.getByRole("link", { name: "Create" }).click();
    await this.page.getByLabel("Name").fill(planName);
    await this.page.getByLabel("Type").selectOption("template");
    await this.page.locator("#select2-plan-client-container").click();
    await this.page.getByRole("option", { name: "hipanel_test_reseller" }).click();
    await this.page.locator("#select2-plan-currency-container").click();
    await this.page.getByRole("option", { name: "USD", exact: true }).click();
    await this.page.getByRole("button", { name: "Save" }).click();
  }

  async createProgressivePrice(planName: string) {
    await this.page.getByRole("link", { name: "Create prices" }).click();
    await this.page.locator("a").filter({ hasText: /^Create prices$/ }).click();
    await this.page.locator("#select2-type-container").click();
    await expect(this.page.getByRole("heading", { name: "Create prices" })).toBeVisible();
    await this.page.getByRole("option", { name: "Dedicated Server" }).click();
    await this.page.getByRole("button", { name: "Proceed to creation" }).click();
    await expect(this.page.getByRole("heading", { name: "Create suggested prices" })).toBeVisible();
    await expect(this.page.getByRole("heading", { name: `Tariff: ${planName}` })).toBeVisible();
    await this.page.locator(".remove-item").first().click();
    await this.page.locator(".remove-item").first().click();
    let pricesCount = await this.page.locator(".form-instance").count();

    for (let i = 0; i < pricesCount - 1; i++) {
      await this.page.locator("div:nth-child(2) > div > .form-instance > .col-md-1 > .remove-item").click();
    }

    await this.page.getByTestId("add progression").click();
    await this.page.locator("#threshold-0-1-quantity").fill("1");
    await this.page.locator("#threshold-0-1-price").fill("0.0085");
    await this.page.getByTestId("add progression").click();
    await this.page.locator("#threshold-0-2-quantity").fill("2");
    await this.page.locator("#threshold-0-2-price").fill("0.0080");
    await this.page.getByTestId("add progression").click();
    await this.page.locator("#threshold-0-3-quantity").fill("3");
    await this.page.locator("#threshold-0-3-price").fill("0.0075");

    await this.page.getByRole("button", { name: "Save" }).click();
  }

  async deleteProgressivePriceItems() {
    await this.page.locator("input[name=\"selection_all\"]").check();
    await this.page.getByRole("button", { name: "Update" }).click();
    await expect(this.page.locator("input#progressiveprice-0-class")).toBeHidden();
    await this.page.locator(".remove-threshold").first().click();
    await this.page.locator(".remove-threshold").first().click();
    await this.page.locator(".remove-threshold").click();
    await this.page.getByRole("button", { name: "Save" }).click();
  }
}

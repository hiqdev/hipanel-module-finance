import { expect, Page } from "@playwright/test";
import Charge from "@hipanel-module-finance/tests/models/Charge";
import Bill from "@hipanel-module-finance/tests/models/Bill";

export default class BillView {
  private page: Page;

  public constructor(page: Page) {
    this.page = page;
  }

  public async checkCharge(charge: Charge) {
    await expect(this.page.locator(`tr td a:text("${charge.object}")`).first()).toBeVisible();
    await expect(this.page.locator(`tr td b:text("${charge.type}")`).first()).toBeVisible();
    await expect(this.page.locator(`tr td span >> text=/.*${charge.sum}.*/i`).first()).toBeVisible();
  }

}

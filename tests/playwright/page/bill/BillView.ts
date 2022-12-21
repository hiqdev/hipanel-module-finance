import { expect, Locator, Page } from "@playwright/test";
import Charge from "@hipanel-module-finance/model/Charge";
import Bill from "@hipanel-module-finance/model/Bill";


export default class BillView {
  private page: Page;
  private detailMenuFunctionsLocator: Locator;

  public constructor(page: Page) {
    this.page = page;
    this.detailMenuFunctionsLocator = page.locator(".widget-user-2 .nav");
  }

  public async checkCharge(charge: Charge) {
    await expect(this.page.locator(`tr td a:text("${charge.object}")`).first()).toBeVisible();
    await expect(this.page.locator(`tr td b:text("${charge.type}")`).first()).toBeVisible();
    await expect(this.page.locator(`tr td a >> text=/.*${charge.sum}.*/i`).first()).toBeVisible();
  }

  public detailMenuItem(item: string, withAcceptDialog: boolean = false): Locator {
    if (withAcceptDialog) {
      this.page.on("dialog", dialog => dialog.accept());
    }

    return this.detailMenuFunctionsLocator.locator(`:scope a:text("${item}")`);
  }

  public async checkBillData(bill: Bill) {
    // TODO: Implement
  }
}

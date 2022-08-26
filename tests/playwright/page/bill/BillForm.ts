import { expect, Locator, Page } from "@playwright/test";
import Select2 from "@hipanel-core/input/Select2";
import SumWithCurrency from "@hipanel-core/input/SumWithCurrency";
import Bill from "@hipanel-module-finance/model/Bill";
import Charge from "@hipanel-module-finance/model/Charge";

export default class BillForm {
  private page: Page;
  private submitBtn: Locator;
  private addChargeBtn: Locator;

  constructor(page: Page) {
    this.page = page;
    this.submitBtn = this.page.locator("button.btn-success:has-text(\"Save\")");
    this.addChargeBtn = this.page.locator("button:has-text(\"Detalization\")");
  }

  async fill(bills: Bill[]) {
    for (const bill of bills) {
      let k = bills.indexOf(bill);
      await this.fillBill(bill, k);
    }
  }

  async fillBill(bill: Bill, k: number = 0) {
    await Select2.field(this.page, `#billform-${k}-client_id`).setValue(bill.client);
    await this.page.locator(`#billform-${k}-type`).selectOption({ label: bill.type });
    await SumWithCurrency.field(this.page, "billform", k).setSumAndCurrency(bill.sum, bill.currency);
    await this.page.locator(`#billform-${k}-quantity`).fill(bill.quantity.toString());

    if (bill.charges !== null) {
      for (const charge of bill.charges) {
        let j = bill.charges.indexOf(charge) + 1;
        await this.addChargeBtn.click();
        await this.fillCharge(charge, k, j);
      }
    }
  }

  async fillCharge(charge: Charge, k: number = 0, j: number = 1) {
    await this.page.locator(`#charge-${k}-${j}-class`).selectOption({ label: charge.class });
    await Select2.field(this.page, `#charge-${k}-${j}-object_id`).setValue(charge.object);
    await this.page.locator(`#charge-${k}-${j}-type`).selectOption({ label: charge.type });
    await this.page.locator(`#charge-${k}-${j}-quantity`).fill(charge.quantity.toString());
    await this.page.locator(`#charge-${k}-${j}-sum`).fill(charge.sum.toString());
  }

  async saveBill(): Promise<string> {
    await this.submit();
    await expect(this.page).toHaveTitle("Bills");

    return await this.getSavedBillId();
  }

  async submit() {
    await this.submitBtn.click();
  }

  async addDetalizationForm() {
    await this.addChargeBtn.click();
  }

  async getSavedBillId(nth = 0): Promise<string> {
    await expect(this.page.url()).toContain("finance/bill?id_in");

    return await this.page.locator("div[role=grid]").first().locator(":scope tbody > tr").nth(nth).getAttribute("data-key");
  }

  async hasValidationError(msg: string) {
    await expect(this.page.locator(`.help-block-error:text-is("${msg}")`).first()).toBeVisible();
  }

  async toggleSign(nth: number = 0) {
    await this.page.locator(".bill-item >> text=\"Toggle sign\"").nth(nth).click();
  }
}

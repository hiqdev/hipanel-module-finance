import { Page } from "@playwright/test";
import Sale from "@hipanel-module-finance/model/Sale";
import Select2 from "@hipanel-core/input/Select2";

export default class SaleUpdate {
  private page: Page;

  constructor(page: Page) {
    this.page = page;
  }

  async changeTariff(sales: Array<Sale>) {
    for (let i = 0; i < sales.length; i++) {
      await Select2.field(this.page, `select[id*='sale-${i}-tariff_id']`).setValue(sales[i].tariff);
    }
    await this.page.locator("button:has-text(\"Save\")").click();
  }

}

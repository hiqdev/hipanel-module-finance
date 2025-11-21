import { Page } from "@playwright/test";
import Sale from "@hipanel-module-finance/model/Sale";
import Select2 from "@hipanel-core/input/Select2";


export default class ServerView {
  private page: Page;

  constructor(page: Page) {
    this.page = page;
  }

  async changeTariff(sale: Sale) {
    await this.page.locator("a:has-text(\"Change tariff\")").click();
    await Select2.field(this.page, "select[id*='server-client_id']").setValue(sale.client);
    await Select2.field(this.page, "select[id*='server-tariff_id']").setValue(sale.tariff);
    await this.page.locator("button:has-text('Sell')").first().click();
  }

}

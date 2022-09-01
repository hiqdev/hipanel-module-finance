import { expect, Locator, Page } from "@playwright/test";
import Bill from "@hipanel-module-finance/model/Bill";
import BillView from "@hipanel-module-finance/page/bill/BillView";
import Select2 from "@hipanel-core/input/Select2";

export default class BillHelper {
    private page: Page;
    private view: BillView;

    public constructor(page: Page) {
        this.page = page;
        this.view = new BillView(page);
    }

    async gotoIndexBill() {
        await this.page.goto('/finance/bill');
        await expect(this.page).toHaveTitle("Bills");
    }

    async gotoCreateBill() {
        await this.page.goto("/finance/bill/create");
        await expect(this.page).toHaveTitle("Create payment");
    }

    async copyBill() {
        await this.page.locator("button:has-text(\"Copy\")").click();
        await this.page.locator("text=Save").click();
    }

    async ensureBillDidntChange(billData: Bill, billId: string) {
        await this.page.goto(`/finance/bill/view?id=${billId}`);

        await billData.charges.forEach(charge => this.view.checkCharge(charge));
    }

    async filterByClient(client: string) {
        await Select2.filterBy(this.page, 'Client').setValue(client);
    }

    async getTotalSum() {
        let totalSum = await this.page.locator('//div[@class="summary"]//tbody//tr[4]//td//span').first().innerText();
        totalSum = totalSum.replace(/[$€UAH]/, '').replace(',', '');

        return parseInt(totalSum);
    }

}

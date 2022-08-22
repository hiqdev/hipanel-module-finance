import { expect, Locator, Page } from "@playwright/test";
import Bill from "@hipanel-module-finance/models/Bill";
import BillView from "@hipanel-module-finance/pages/bill/BillView";


export default class BillBase {
    private page: Page;
    private view: BillView;

    public constructor(page: Page) {
        this.page = page;
        this.view = new BillView(page);
    }

    async gotoCreateBill() {
        await this.page.goto("/finance/bill/create");
        await expect(this.page).toHaveTitle("Create payment");
    }

    async copyBill(billId: string) {
        await this.page.goto(`/finance/bill/copy?id=${billId}`);
        await this.page.locator("text=Save").click();
    }

    async ensureBillDidntChange(billData: Bill, billId: string) {
        await this.page.goto(`/finance/bill/view?id=${billId}`);

        billData.charges.forEach(charge => this.view.checkCharge(charge));
    }

}

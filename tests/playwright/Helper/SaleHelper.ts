import { expect, Locator, Page } from "@playwright/test";
import Select2 from "@hipanel-core/input/Select2";
import Sale from "@hipanel-module-finance/model/Sale";
import Index from "@hipanel-core/page/Index";

export default class SaleHelper {
    private page: Page;
    private indexPage: Index

    public constructor(page: Page) {
        this.page = page;
        this.indexPage = new Index(page);
    }

    async gotoIndexSale() {
        await this.page.goto('/finance/sale/index');
        await expect(this.page).toHaveTitle("Sales");
    }

    async filterByTariff(tariff: string) {
        await this.page.locator('span[role="textbox"]:has-text("Tariff")').click();
        await this.page.locator('.select2-container--open input.select2-search__field').fill(tariff);
        await this.page.locator(`li[role="option"]:has-text("${tariff}")`).click();
        await this.page.locator('button:has-text("Search")').click();
        await this.page.waitForLoadState('networkidle');
    }

    async filterByBuyer(buyer: string) {
        await Select2.filterBy(this.page, 'Buyer').setValue(buyer);
        await this.page.waitForLoadState('networkidle');
    }

    async checkDetailViewData(sale: Sale) {
        await expect(this.page.locator('//div[@class="box box-widget"]')).toContainText(sale.server);
        await expect(this.page.locator('//div[@class="box box-widget"]')).toContainText(sale.client);
        await expect(this.page.locator('//div[@class="box box-widget"]')).toContainText(sale.tariff);
    }

    async deleteSales() {
        this.page.on('dialog', dialog => dialog.accept());
        await this.indexPage.clickBulkButton('Delete');
    }

    async checkDataOnTable(sales: Array<Sale>) {
        for (let i = 0; i < sales.length; i++) {
            let receivedServer = await this.indexPage.getValueInColumnByNumberRow('Object', i + 1);
            expect(receivedServer).toEqual(sales[i].server);
            let receivedClient = await this.indexPage.getValueInColumnByNumberRow('Buyer', i + 1);
            expect(receivedClient).toEqual(sales[i].client);
            let receivedTariff = await this.indexPage.getValueInColumnByNumberRow('Tariff', i + 1)
            expect(receivedTariff).toContain(sales[i].tariff.split('@')[0]);
        }
    }
}

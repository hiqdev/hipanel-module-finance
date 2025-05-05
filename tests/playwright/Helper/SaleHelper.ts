import { expect, Locator, Page } from "@playwright/test";
import Select2 from "@hipanel-core/input/Select2";
import Sale from "@hipanel-module-finance/model/Sale";
import Index from "@hipanel-core/page/Index";
import Input from "@hipanel-core/input/Input";
import DateHelper from "@hipanel-core/helper/DateHelper";

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
        await this.page.goto(this.page.url() + '&sort=-time');
    }

    async filterByType(type: string) {
        await Select2.filterBy(this.page, 'Type').setValue(type);
        await this.page.waitForLoadState('networkidle');
        await this.page.goto(this.page.url() + '&sort=-time');
    }

    async filterByObject(object: string) {
        await Input.filterBy(this.page, 'Inilike').setValue(object);
        await this.page.goto(this.page.url() + '&sort=-time');
    }

    async assertSaleDetails(sale: Sale) {
        const box = this.page.locator('div.box.box-widget');

        await this.assertCellContains(box, 'Servers', sale.server);
        await this.assertCellContains(box, 'Buyer', sale.client);
        await this.assertCellContains(box, 'Tariff', sale.tariff);
    }

    private async assertCellContains(box: Locator, header: string, substring: string) {
        const cell = box.locator(`xpath=//tr[th[normalize-space()="${header}"]]/td`);
        await expect(cell, `Expected '${header}' to contain '${substring}'`).toContainText(substring);
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

    async changeBuyer(buyer: string, date: string) {
        await Select2.field(this.page, '#sale-client-0-buyer_id').setValue(buyer);
        await this.page.locator('.glyphicon >> nth=0').click();
        await this.page.locator('input[name="Sale[client][0][time]"]').first().fill(date);
        await this.page.locator('button:has-text("Submit")').first().click();
    }

    async checkOldBuyer(buyer: string, date: Date) {
        await this.filterByBuyer(buyer);
        await this.indexPage.hasRowsOnTable(1);
        const closeTime = await this.indexPage.getValueInColumnByNumberRow('Close time', 1);
        const expectedDate = DateHelper.date(date).formatDate('MMM d, yyyy, h:mm:ss TT');
        expect(closeTime).toEqual(expectedDate);
    }

    async checkNewBuyer(buyer: string, date: Date) {
        await this.filterByBuyer(buyer);
        await this.page.waitForTimeout(3000);
        const time = await this.indexPage.getValueInColumnByNumberRow('Time', 1);
        const expectedDate = DateHelper.date(date).formatDate('MMM d, yyyy, h:mm:ss TT');
        expect(time).toEqual(expectedDate);
        await this.checkEmptyCloseTimeByRow(1);
    }

    async checkEmptyCloseTimeByRow(row: number) {
        const closeTime = await this.indexPage.getValueInColumnByNumberRow('Close time', row);
        expect(closeTime).toEqual("");
    }
}

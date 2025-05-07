import {expect, Locator, Page} from "@playwright/test";

export default class ViewTableHelper {
    private page: Page;
    private box: Locator;

    public constructor(page: Page) {
        this.page = page;
        this.box = this.page.locator('div.box');
    }

    public async assertCellEquals(header: string, expectedText: string): Promise<void> {
        const cell = await this.getRowByLabel(header);
        await expect(cell, `Expected '${header}' to equal '${expectedText}'`).toHaveText(expectedText);
    }

    public async assertCellContains(header: string, substring: string): Promise<void> {
        const cell = await this.getRowByLabel(header)
        await expect(cell, `Expected '${header}' to contain '${substring}'`).toContainText(substring);
    }

    async getRowByLabel(label: string): Locator {
        return this.box.locator(`xpath=//tr[th[normalize-space()="${label}"]]/td`);
    }
}

import { expect, Locator, Page } from "@playwright/test";
import Sale from "@hipanel-module-finance/model/Sale";

export default class ServerHelper {
    private page: Page;

    public constructor(page: Page) {
        this.page = page;
    }

    async gotoIndexServer() {
        await this.page.goto('/server/server/index');
        await expect(this.page).toHaveTitle("Servers");
    }

    async gotoServerView(index: number) {
        await this.page.locator('tr td button').nth(index).click();
        await this.page.locator('div[role="tooltip"] >> text=View').click();
    }
}

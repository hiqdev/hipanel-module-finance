import { test } from "@hipanel-core/fixtures";
import {expect, Page} from "@playwright/test";
import ServerHelper from "@hipanel-module-finance/Helper/ServerHelper";
import Sale from "@hipanel-module-finance/model/Sale";
import ServerView from "@hipanel-module-finance/page/bill/ServerView";
import SaleHelper from "@hipanel-module-finance/Helper/SaleHelper";
import Index from "@hipanel-core/page/Index";
import SaleUpdate from "@hipanel-module-finance/page/bill/SaleUpdate";
import Alert from "@hipanel-core/ui/Alert";

const sales: Array<Sale> = [
    {
        client: 'hipanel_test_user2',
        tariff: 'PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller',
        column: 'DC',
        server: 'TEST-DS-01'
    },
    {
        client: 'hipanel_test_user2',
        tariff: 'PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller',
        column: 'DC',
        server: 'TEST-DS-02'
    }
];

sales.forEach((sale, index) => {
    test(`Ensure I can create several sales ${sale.server}`, async ({ managerPage }) => {

        const serverHelper = new ServerHelper(managerPage);
        const serverView = new ServerView(managerPage);
        const indexPage = new Index(managerPage);

        await serverHelper.gotoIndexServer();
        const rowNumber = await indexPage.getRowNumberInColumnByValue(sale.column, sale.server);
        await serverHelper.gotoServerView(rowNumber);
        await serverView.changeTariff(sale);

        await Alert.on(managerPage).hasText('Servers were sold');
    })
});

test(`Ensure I can edit several sales`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);
    const saleUpdate = new SaleUpdate(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByTariff(sales[0].tariff);
    await indexPage.chooseRangeOfRowsOnTable(1, 2);
    await indexPage.clickBulkButton('Edit');
    await saleUpdate.changeTariff(sales);

    await Alert.on(sellerPage).hasText('Sale has been successfully changed');
});

test(`Ensure sale detail view is correct`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByTariff(sales[0].tariff);
    await indexPage.clickColumnOnTable('Time', 1);
    await sellerPage.locator('text=Tariff information').waitFor();
    await saleHelper.checkDetailViewData(sales[0]);
});

test(`Ensure I can delete several sales`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByTariff(sales[0].tariff);
    await indexPage.chooseRangeOfRowsOnTable(1, 2);
    await saleHelper.deleteSales();

    await Alert.on(sellerPage).hasText('Sale was successfully deleted.');
});

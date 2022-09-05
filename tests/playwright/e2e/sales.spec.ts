import { test } from "@hipanel-core/fixtures";
import {expect, Page} from "@playwright/test";
import ServerHelper from "@hipanel-module-finance/Helper/ServerHelper";
import Sale from "@hipanel-module-finance/model/Sale";
import ServerView from "@hipanel-module-finance/page/bill/ServerView";
import SaleHelper from "@hipanel-module-finance/Helper/SaleHelper";
import Index from "@hipanel-core/page/Index";
import SaleUpdate from "@hipanel-module-finance/page/bill/SaleUpdate";
import Alert from "@hipanel-core/ui/Alert";
import Input from "@hipanel-core/input/Input";
import Select2 from "@hipanel-core/input/Select2";
import DateHelper from "@hipanel-core/helper/DateHelper";

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

const changeBuyerData = {
    oldClient: 'hipanel_test_user',
    newClient: 'hipanel_test_user2',
    server: 'BTEST001',
    date: null,
};

sales.forEach((sale, index) => {
    test(`Ensure I can create several sales ${sale.server} @hipanel-module-finance @manager`, async ({ managerPage }) => {

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

test(`Ensure I can edit several sales @hipanel-module-finance @seller`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);
    const saleUpdate = new SaleUpdate(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByBuyer(sales[0].client);
    await indexPage.hasRowsOnTable(sales.length);
    await saleHelper.checkDataOnTable(sales);

    await indexPage.chooseRangeOfRowsOnTable(1, 2);
    await indexPage.clickBulkButton('Edit');
    await saleUpdate.changeTariff(sales);

    await Alert.on(sellerPage).hasText('Sale has been successfully changed');
});

test(`Ensure sale detail view is correct @hipanel-module-finance @seller`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByBuyer(sales[0].client);

    await indexPage.clickColumnOnTable('Time', 1);
    await sellerPage.locator('text=Tariff information').waitFor();
    await saleHelper.checkDetailViewData(sales[0]);
});

test(`Ensure I can delete several sales @hipanel-module-finance @seller`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByBuyer(sales[0].client);

    await indexPage.chooseRangeOfRowsOnTable(1, 2);
    await saleHelper.deleteSales();

    await Alert.on(sellerPage).hasText('Sale was successfully deleted.');
});

test(`Ensure I can change buyer @hipanel-module-finance @seller`, async ({ sellerPage }) => {

    const saleHelper = new SaleHelper(sellerPage);
    const indexPage = new Index(sellerPage);

    await saleHelper.gotoIndexSale();
    await saleHelper.filterByObject(changeBuyerData.server);
    await saleHelper.checkEmptyCloseTimeByRow(1);

    await indexPage.chooseNumberRowOnTable(1);
    await indexPage.clickDropdownBulkButton('Change buyer', 'Change buyer by one');

    let timestamp = new Date().getTime();
    // // round up to a minute
    let dateNow = new Date(Math.floor(new Date(timestamp).getTime()/(60*1000)) *(60*1000));
    let formatedDate = DateHelper.date(dateNow).formatDate('yyyy-MM-dd HH:mm:ss');
    changeBuyerData.date = dateNow;
    await saleHelper.changeBuyer(changeBuyerData.newClient, formatedDate);

    await Alert.on(sellerPage).hasText("Object's buyer has been changed");

    await saleHelper.filterByObject(changeBuyerData.server);
    await saleHelper.checkOldBuyer(changeBuyerData.oldClient, changeBuyerData.date);
    await saleHelper.checkNewBuyer(changeBuyerData.newClient, changeBuyerData.date);
});

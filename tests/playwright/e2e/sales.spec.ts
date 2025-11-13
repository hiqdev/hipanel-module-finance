import { test, expect } from "@hipanel-core/fixtures";
import ServerHelper from "@hipanel-module-finance/Helper/ServerHelper";
import Sale from "@hipanel-module-finance/model/Sale";
import ServerView from "@hipanel-module-finance/page/bill/ServerView";
import SaleHelper from "@hipanel-module-finance/Helper/SaleHelper";
import Index from "@hipanel-core/page/Index";
import SaleUpdate from "@hipanel-module-finance/page/bill/SaleUpdate";
import Alert from "@hipanel-core/ui/Alert";
import DateHelper from "@hipanel-core/helper/DateHelper";

const sales: Array<Sale> = [
  {
    client: "hipanel_test_user2",
    tariff: "PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller",
    column: "DC",
    server: "TEST-DS-01",
    type: "Device",
  },
  {
    client: "hipanel_test_user2",
    tariff: "PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller",
    column: "DC",
    server: "TEST-DS-02",
    type: "Device",
  },
];

const changeBuyerData = {
  oldClient: "hipanel_test_admin",
  newClient: "hipanel_test_user2",
  object: "hipanel_test_admin",
  date: null,
};

sales.forEach((sale) => {
  test(`Ensure I can create several sales ${sale.server} @hipanel-module-finance @manager`, {
    tag: '@server-sale',
  }, async ({ page }) => {

    const serverHelper = new ServerHelper(page);
    const serverView = new ServerView(page);
    const indexPage = new Index(page);

    await serverHelper.gotoIndexServer();
    const rowNumber = await indexPage.getRowNumberInColumnByValue(sale.column, sale.server);
    await serverHelper.gotoServerView(rowNumber);
    await serverView.changeTariff(sale);

    await Alert.on(page).hasText("Servers were sold");
  });
});

test(`Ensure I can edit several sales @hipanel-module-finance @seller`, {
  tag: '@multi-sales',
}, async ({ page }) => {

  const saleHelper = new SaleHelper(page);
  const indexPage = new Index(page);
  const saleUpdate = new SaleUpdate(page);

  await saleHelper.gotoIndexSale();
  await saleHelper.filterByBuyer(sales[0].client);
  await saleHelper.filterByType(sales[0].type);
  await indexPage.hasRowsOnTable(sales.length);
  await saleHelper.checkDataOnTable(sales);

  await indexPage.chooseRangeOfRowsOnTable(1, 2);
  await indexPage.clickBulkButton("Edit");
  await saleUpdate.changeTariff(sales);

  await Alert.on(page).hasText("Sale has been successfully changed");
});

test(`Ensure sale detail view is correct @hipanel-module-finance @seller`, {
  tag: '@multi-sales',
}, async ({ page }) => {
  const saleHelper = new SaleHelper(page);
  const indexPage = new Index(page);
  const sale = sales[0];

  await saleHelper.gotoIndexSale();
  await saleHelper.filterByBuyer(sale.client);
  await saleHelper.filterByType(sale.type);
  await saleHelper.filterByTariff(sale.tariff);

  await indexPage.clickColumnOnTable("Time", 1);
  await page.locator("text=Tariff information").waitFor();
  await saleHelper.assertSaleDetails(sale);
});

test(`Ensure I can delete several sales @hipanel-module-finance @seller`, {
  tag: '@multi-sales',
},async ({ page }) => {

  const saleHelper = new SaleHelper(page);
  const indexPage = new Index(page);

  await saleHelper.gotoIndexSale();
  await saleHelper.filterByBuyer(sales[0].client);
  await saleHelper.filterByType(sales[0].type);

  await indexPage.chooseRangeOfRowsOnTable(1, 2);
  await saleHelper.deleteSales();
  const closeTime1 = await indexPage.getValueInColumnByNumberRow('Close time', 1);
  const closeTime2 = await indexPage.getValueInColumnByNumberRow('Close time', 2);

  const date = new Date();
  const year= String(date.getFullYear());

  expect(closeTime1).toContain(year);
  expect(closeTime2).toContain(year);
});

test('Ensure I can change buyer @hipanel-module-finance @seller', {
  tag: '@multi-sales',
}, async ({ page }) => {

  test.fixme(true, 'The problem is with time compare (Expected: "Nov 30, 2022, 8:48:00 PM Received: "Nov 30, 2022, 6:48:00 PM")');
  const saleHelper = new SaleHelper(page);
  const indexPage = new Index(page);

  await saleHelper.gotoIndexSale();
  await saleHelper.filterByObject(changeBuyerData.object);
  await saleHelper.checkEmptyCloseTimeByRow(1);

  await indexPage.chooseNumberRowOnTable(1);
  await indexPage.clickDropdownBulkButton("Change buyer", "Change buyer by one");

  // round up to a minute
  changeBuyerData.date = new Date(Math.floor(new Date().getTime() / (60 * 1000)) * (60 * 1000));
  const formattedDate = DateHelper.date(changeBuyerData.date).formatDate("yyyy-MM-dd HH:mm:ss");
  await saleHelper.changeBuyer(changeBuyerData.newClient, formattedDate);

  await Alert.on(page).hasText("Object's buyer has been changed");
  await saleHelper.filterByObject(changeBuyerData.object);
  await saleHelper.checkOldBuyer(changeBuyerData.oldClient, changeBuyerData.date);
  await saleHelper.checkNewBuyer(changeBuyerData.newClient, changeBuyerData.date);
});

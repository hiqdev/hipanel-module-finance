import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import Select2 from "@hipanel-core/input/Select2";
import Alert from "@hipanel-core/ui/Alert";

test("Test the currency exchange operation are works and creates a bill @hipanel-module-finance @manager", async ({ managerPage }) => {
  await managerPage.goto("/finance/bill/index");
  await expect(managerPage).toHaveTitle("Bills");

  await managerPage.locator("a:has-text(\"Currency exchange\")").click();
  await expect(managerPage).toHaveTitle("Create currency exchange");

  await Select2.field(managerPage, "#currencyexchangeform-client_id").setValue("hipanel_test_user");
  await Select2.field(managerPage, "#currencyexchangeform-from").setValue("USD");
  await Select2.field(managerPage, "#currencyexchangeform-to").setValue("UAH");
  await managerPage.locator("input[name=\"CurrencyExchangeForm\\[sum\\]\"]").fill("200");

  await managerPage.locator("button:has-text(\"Create\")").click();

  await Alert.on(managerPage).hasText("Currency was exchanged successfully");

  await Select2.field(managerPage, "#billsearch-client_id").setValue("hipanel_test_user");

  await managerPage.goto(managerPage.url() + "&sort=-time");

  await managerPage.locator("div[role=grid] a:has-text(\"-$200.00\")").first().click();

  await expect(managerPage).toHaveTitle(/^hipanel_test_user: -200.00 usd Exchanging 200.00 USD.*/);
});

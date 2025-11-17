import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import Select2 from "@hipanel-core/input/Select2";
import Alert from "@hipanel-core/ui/Alert";
import AdvancedSearch from "@hipanel-core/helper/AdvancedSearch";

test("the currency exchange operation works and creates a bill @hipanel-module-finance @manager", async ({ page }) => {
  const advancedSearch = new AdvancedSearch(page);

  await page.goto("/finance/bill/index");
  await expect(page).toHaveTitle("Bills");

  await page.locator("a:has-text(\"Currency exchange\")").click();
  await expect(page).toHaveTitle("Create currency exchange");

  await Promise.all([
    page.waitForResponse(response => response.status() === 200 && response.url().includes("get-exchange-rates")),
    Select2.field(page, "#currencyexchangeform-client_id").setValue("hipanel_test_user"),
  ]);

  await Select2.field(page, "#currencyexchangeform-from").setValue("USD");
  await Select2.field(page, "#currencyexchangeform-to").setValue("UAH");
  await page.locator("input[name=\"CurrencyExchangeForm\\[sum\\]\"]").fill("200");

  await page.locator("button:has-text(\"Create\")").click();

  await Alert.on(page).hasText("Currency was exchanged successfully");

  await advancedSearch.setFilter("client_id", "hipanel_test_user");
  await advancedSearch.setFilter("descr", "Exchanging 200.00 USD");
  await advancedSearch.submitButton();

  await page.locator("div[role=grid] a:has-text(\"-$200.00\")").first().click();

  await expect(page).toHaveTitle(/^hipanel_test_user: -200.00 usd Exchanging 200.00 USD.*/);
});

import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import Index from "@hipanel-core/page/Index";

test("Test bills index page works @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill");
  await expect(sellerPage).toHaveTitle("Bills");

  await expect(sellerPage.locator(".content-sidebar a.btn:has-text(\"Recharge account\")")).toBeVisible();
  await expect(sellerPage.locator(".content-sidebar a.btn:has-text(\"Add payment\")")).toBeVisible();
  await expect(sellerPage.locator(".content-sidebar a.btn:has-text(\"Currency exchange\")")).toBeVisible();

  await sellerPage.locator("text=Import payments").first().click();
  await expect(sellerPage.locator("text=Import payments").first()).toBeVisible();
  await expect(sellerPage.locator("text=Import from a file").first()).toBeVisible();

  const indexPage = new Index(sellerPage);
  await indexPage.hasAdvancedSearchInputs([
    "BillSearch[client_id]",
    "BillSearch[requisite_id]",
    "BillSearch[currency_in][]",
    "BillSearch[servers]",
    "BillSearch[descr]",
    "BillSearch[tariff_id]",
    "BillSearch[seller_id]",
  ]);
  await indexPage.hasBulkButtons(["Copy", "Generate invoice", "Update", "Delete"]);
  await indexPage.hasColumns(["Client", "Time", "Sum", "Balance", "Type", "Description"]);

  await sellerPage.waitForTimeout(3000);
});

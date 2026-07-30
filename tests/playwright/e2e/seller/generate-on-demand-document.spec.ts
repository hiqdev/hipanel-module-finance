import { expect, test } from "@hipanel-core/fixtures";
import { Page } from "@playwright/test";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import BillView from "@hipanel-module-finance/page/bill/BillView";
import { Alert } from "@hipanel-core/shared/ui/components";
import Index from "@hipanel-core/page/Index";
import Select2 from "@hipanel-core/input/Select2";
import Bill from "@hipanel-module-finance/model/Bill";

const bill: Bill = {
  client: "hipanel_test_user",
  type: "Positive balance correction",
  requisite: "Test Reseller",
  currency: "$",
  sum: 100,
  quantity: 1,
};

async function createBill(page: Page): Promise<number | null> {
  await page.goto("/finance/bill/create");
  const form = new BillForm(page);
  await form.fill([bill]);
  await form.submit();
  await Alert.on(page).hasText("Bill was created successfully");
  return await form.getSavedBillId();
}

async function deleteBill(page: Page, billId: number) {
  await page.goto("/finance/bill/view?id=" + billId);
  const viewPage = new BillView(page);
  await viewPage.detailMenuItem("Delete", true).click();
  await Alert.on(page).hasText("Payment was deleted successfully");
}

test(
  "Generate on-demand document form opens from bill index @hipanel-module-finance @seller",
  { tag: "@missing-requisites" },
  async ({ page }) => {
    const billId = await createBill(page);

    await page.goto("/finance/bill/index");
    const index = new Index(page);
    await Select2.fieldByName(page, "BillSearch[requisite_id]").setValue(bill.requisite);
    await index.advancedSearch.search();

    const rowNumber = await index.getRowNumberInColumnByValue("Description", bill.requisite);
    await index.chooseNumberRowOnTable(rowNumber);
    await index.clickBulkButton("Generate on-demand document");

    await expect(page).toHaveTitle("Generate on-demand document");
    await expect(page.locator("select[name=\"PrepareOnDemandDocumentForm[type]\"]")).toBeVisible();
    await expect(page.getByRole("textbox", { name: "Date" })).toBeVisible();
    await expect(page.getByRole("button", { name: "Prepare document" })).toBeVisible();

    if (billId) {
      await deleteBill(page, billId);
    }
  },
);

test(
  "Generate on-demand document form opens from charge index @hipanel-module-finance @seller",
  { tag: "@missing-requisites" },
  async ({ page }) => {
    const billId = await createBill(page);

    await page.goto("/finance/charge/index");
    const index = new Index(page);
    await index.columnFilters.applyFilter("client_id", bill.client);
    await index.chooseNumberRowOnTable(1);
    await index.clickBulkButton("Generate on-demand document");

    await expect(page).toHaveTitle("Generate on-demand document");
    await expect(page.locator("select[name=\"PrepareOnDemandDocumentForm[type]\"]")).toBeVisible();
    await expect(page.getByRole("textbox", { name: "Date" })).toBeVisible();
    await expect(page.getByRole("button", { name: "Prepare document" })).toBeVisible();

    if (billId) {
      await deleteBill(page, billId);
    }
  },
);

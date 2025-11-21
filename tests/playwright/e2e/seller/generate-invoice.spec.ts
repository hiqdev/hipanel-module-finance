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
  sum: 250,
  quantity: 1,
};

async function createBill(page: Page) {
  await page.goto("/finance/bill/create");
  const form = new BillForm(page);
  await form.fill([bill]);
  await form.submit();
  await Alert.on(page).hasText("Bill was created successfully");

  return await form.getSavedBillId();
}

async function deleteBill(page: Page, billId) {
  await page.goto("/finance/bill/view?id=" + billId);
  const viewPage = new BillView(page);
  await viewPage.detailMenuItem("Delete", true).click();

  // Handle the confirmation alert
  page.once("dialog", async (dialog) => {
    await dialog.accept();
  });

  await Alert.on(page).hasText("Payment was deleted successfully");
}

test("Test 'Generate invoice' button is work and the form opens @hipanel-module-finance @seller", {
  tag: "@missing-requisites",
}, async ({ page }) => {
  const billId = await createBill(page);
  const action = "/finance/bill/index";

  await page.goto(action);
  const index = new Index(page);
  await Select2.fieldByName(page, `BillSearch[requisite_id]`).setValue(bill.requisite);
  await index.advancedSearch.submitButton();

  const rowNumber = await index.getRowNumberInColumnByValue("Description", bill.requisite);
  await index.chooseNumberRowOnTable(rowNumber);
  await index.clickBulkButton("Generate invoice");
  await expect(page).toHaveTitle("Generate invoice");

  if (billId) {
    await deleteBill(page, billId);
  }
});

import { test, expect } from "@hipanel-core/fixtures";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import BillView from "@hipanel-module-finance/page/bill/BillView";
import Alert from "@hipanel-core/ui/Alert";
import Index from "@hipanel-core/page/Index";
import Select2 from "@hipanel-core/input/Select2";

const bill = {
  client: "hipanel_test_user",
  type: "Monthly fee",
  requisite: "Test Reseller",
  currency: "$",
  sum: 250,
  quantity: 1,
};

async function createBill(page) {
  await page.goto("/finance/bill/create");
  const form = new BillForm(page);
  await form.fill([bill]);
  await form.submit();
  await Alert.on(page).hasText("Bill was created successfully");

  return await form.getSavedBillId();
}

async function deleteBill(page, billId) {
  await page.goto("/finance/bill/view?id=" + billId);
  const viewPage = await new BillView(page);
  await viewPage.detailMenuItem("Delete", true).click();
  await Alert.on(page).hasText("Payment was deleted successfully");
}

test("Test 'Generate invoice' button is work and the form opens @hipanel-module-finance @seller", async ({ page }) => {
  const billId = await createBill(page);
  await page.goto("/finance/bill/index");
  const index = new Index(page);
  await Select2.fieldByName(page, `BillSearch[requisite_id]`).setValue(bill.requisite);
  await index.advancedSearch.submitButton.click();
  await page.waitForLoadState("networkidle");
  const rowNumber = await index.getRowNumberInColumnByValue("Description", bill.requisite);
  await index.chooseNumberRowOnTable(rowNumber);
  await index.clickBulkButton("Generate invoice");
  await expect(page).toHaveTitle("Generate invoice");
  if (billId) {
    await deleteBill(page, billId);
  }
});

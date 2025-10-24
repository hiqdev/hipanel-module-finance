import { test, expect } from "@hipanel-core/fixtures";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import Alert from "@hipanel-core/ui/Alert";

let billId;

const bill = {
  client: "hipanel_test_user",
  type: "Premium purchase",
  currency: "$",
  sum: 250,
  quantity: 1,
  time: "",
  description: null,
  object: null,
  class: null,
  charges: null,
};

const charge = {
  class: "Client",
  object: "hipanel_test_user",
  type: "Positive balance correction",
  sum: -250,
  quantity: 1,
  description: null,
  time: null,
};

test.describe("Bill creation", () => {

  test.beforeEach(async ({ page }) => {
    await page.goto("/finance/bill/create");
    await expect(page).toHaveTitle("Create payment");
  });

  test("Test I can`t create bill without required fields @hipanel-module-finance @seller", async ({ page }) => {
    const form = new BillForm(page);
    await form.submit();
    await form.hasValidationError("Client cannot be blank.");
    await form.hasValidationError("Type cannot be blank.");
    await form.hasValidationError("Sum cannot be blank.");
    await form.hasValidationError("Currency cannot be blank.");
    await form.hasValidationError("Quantity cannot be blank.");
  });

  test("Test I can create bill without charges @hipanel-module-finance @seller", async ({ page }) => {
    const billForm = new BillForm(page);
    await billForm.fill([bill]);
    await billForm.submit();
    await expect(page).toHaveTitle("Bills");
    await Alert.on(page).hasText("Bill was created successfully");
  });

  test("Test I can`t create bill with charges without completing all the required fields @hipanel-module-finance @seller", async ({ page }) => {
    const billForm = new BillForm(page);
    await billForm.fill([bill]);
    await billForm.addDetalizationForm();
    await billForm.submit();

    await billForm.hasValidationError("Object Id cannot be blank.");
    await billForm.hasValidationError("Qty. cannot be blank.");
    await billForm.hasValidationError("Bill sum must match charges sum: 0");
    await billForm.hasValidationError("Sum cannot be blank.");
  });

  test("Test I can create bill with charges @hipanel-module-finance @seller", async ({ page }) => {
    const billForm = new BillForm(page);
    await billForm.fill([bill]);
    await billForm.addDetalizationForm();
    await billForm.fillCharge(charge, 0, 1);
    await billForm.submit();
    await Alert.on(page).hasText("Bill was created successfully");

    billId = await billForm.getSavedBillId();
  });

  test("Test I can update bill @hipanel-module-finance @seller", async ({ page }) => {
    const billDescr = "Test bill description", chargeDescr = "Test charge description";
    await page.goto("/finance/bill/update?id=" + billId);
    await expect(page).toHaveTitle("Update payments");

    const billForm = new BillForm(page);
    await page.locator("#billform-0-label").fill(billDescr);
    await page.locator("#charge-0-1-label").fill(chargeDescr);
    await billForm.submit();
    await Alert.on(page).hasText("Bill was updated successfully");

    await page.goto("/finance/bill/view?id=" + billId);

    await expect(page.locator(`table >> text=${billDescr}`)).toBeVisible();
    await expect(page.locator(`table >> text=${chargeDescr}`)).toBeVisible();
  });
});


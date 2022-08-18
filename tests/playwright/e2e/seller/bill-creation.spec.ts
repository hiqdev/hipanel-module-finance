import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import Alert from "@hipanel-core/ui/Alert";

let billId;

const bill = {
  client: "hipanel_test_user",
  type: "Premium renewal",
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
  type: "Monthly fee",
  sum: -250,
  quantity: 1,
  description: null,
  time: null,
};

test.afterEach(async ({ sellerPage }) => {
  await sellerPage.close();
});

test("Test I can`t create bill without required fields @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill/create");
  await expect(sellerPage).toHaveTitle("Create payment");

  const form = new BillForm(sellerPage);
  await form.submit();
  await form.hasValidationError("Client cannot be blank.");
  await form.hasValidationError("Type cannot be blank.");
  await form.hasValidationError("Sum cannot be blank.");
  await form.hasValidationError("Currency cannot be blank.");
  await form.hasValidationError("Quantity cannot be blank.");
});

test("Test I can create bill without charges @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill/create");
  await expect(sellerPage).toHaveTitle("Create payment");

  const billForm = new BillForm(sellerPage);
  await billForm.fill([bill]);
  await billForm.submit();
  await expect(sellerPage).toHaveTitle("Bills");
  await Alert.on(sellerPage).hasText("Bill was created successfully");
});

test("Test I can`t create bill with charges without completing all the required fields @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill/create");
  await expect(sellerPage).toHaveTitle("Create payment");

  const billForm = new BillForm(sellerPage);
  await billForm.fill([bill]);
  await billForm.addCharge();
  await billForm.submit();

  await billForm.hasValidationError("Object Id cannot be blank.");
  await billForm.hasValidationError("Qty. cannot be blank.");
  await billForm.hasValidationError("Bill sum must match charges sum: 0");
  await billForm.hasValidationError("Sum cannot be blank.");
});

test("Test I can create bill with charges @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill/create");
  await expect(sellerPage).toHaveTitle("Create payment");

  const billForm = new BillForm(sellerPage);
  await billForm.fill([bill]);
  await billForm.addCharge();
  await billForm.fillCharge(charge, 0, 1);
  await billForm.submit();
  await Alert.on(sellerPage).hasText("Bill was created successfully");

  billId = await billForm.getSavedBillId();
});

test("Test I can update bill @hipanel-module-finance @seller", async ({ sellerPage }) => {
  const billDescr = "Test bill description", chargeDescr = "Test charge description";
  await sellerPage.goto("/finance/bill/update?id=" + billId);
  await expect(sellerPage).toHaveTitle("Update payments");

  const billForm = new BillForm(sellerPage);
  await sellerPage.locator("#billform-0-label").fill(billDescr);
  await sellerPage.locator("#charge-0-0-label").fill(chargeDescr);
  await billForm.submit();
  await Alert.on(sellerPage).hasText("Bill was updated successfully");

  await sellerPage.goto("/finance/bill/view?id=" + billId);

  await expect(sellerPage.locator(`table >> text=${billDescr}`)).toBeVisible();
  await expect(sellerPage.locator(`table >> text=${chargeDescr}`)).toBeVisible();
});

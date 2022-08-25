import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import Alert from "@hipanel-core/ui/Alert";
import BillView from "@hipanel-module-finance/page/bill/BillView";

test("Test we add the charges to created bill @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill/create");
  await expect(sellerPage).toHaveTitle("Create payment");

  const billForm = new BillForm(sellerPage);
  await billForm.fill([bill]);
  await billForm.submit();
  await expect(sellerPage).toHaveTitle("Bills");
  const createdBillId = await billForm.getSavedBillId();

  expect(Number(createdBillId)).toBeGreaterThan(0);

  await sellerPage.goto(`/finance/bill/view?id=${createdBillId}`);
  await expect(sellerPage).toHaveTitle("hipanel_test_user: -1050.00 usd");

  await sellerPage.locator("a:has-text(\"Update\")").click();

  for (const charge of charges) {
    let j = charges.indexOf(charge) + 1;
    await billForm.addDetalizationForm();
    await billForm.fillCharge(charge, 0, j);
  }
  await billForm.submit();

  await Alert.on(sellerPage).hasText("Bill was updated successfully");

  await sellerPage.goto(`/finance/bill/view?id=${createdBillId}`);

  const billView = new BillView(sellerPage);
  for (const charge of charges) {
    await billView.checkCharge(charge);
  }
});

const bill = {
  client: "hipanel_test_user",
  type: "Monthly fee",
  currency: "$",
  sum: -1050,
  quantity: 1,
  time: "2020-01-01",
  description: null,
  object: null,
  class: null,
  charges: null,
};


const charges = [
  {
    class: "Client",
    object: "hipanel_test_user",
    type: "Cash",
    sum: 250,
    quantity: 1,
    description: null,
    time: null,
  },
  {
    class: "Client",
    object: "hipanel_test_user1",
    type: "Certificate purchase",
    sum: 350,
    quantity: 1,
    description: null,
    time: null,
  },
  {
    class: "Client",
    object: "hipanel_test_user1",
    type: "Negative balance correction",
    sum: 450,
    quantity: 1,
    description: null,
    time: null,
  },
];

import { test, expect } from "@hipanel-core/fixtures";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import Alert from "@hipanel-core/ui/Alert";
import BillView from "@hipanel-module-finance/page/bill/BillView";

test("Test we add the charges to created bill @hipanel-module-finance @seller", async ({ page }) => {
  await page.goto("/finance/bill/create");
  await expect(page).toHaveTitle("Create payment");

  const billForm = new BillForm(page);
  await billForm.fill([bill]);
  await billForm.submit();
  await expect(page).toHaveTitle("Bills");
  const createdBillId = await billForm.getSavedBillId();

  expect(Number(createdBillId)).toBeGreaterThan(0);

  await page.goto(`/finance/bill/view?id=${createdBillId}`);
  await expect(page).toHaveTitle("hipanel_test_user: -1050.00 usd");

  await page.locator("a:has-text(\"Update\")").click();
  await page.locator(".remove-charge").click();

  for (const charge of charges) {
    let j = charges.indexOf(charge) + 1;
    await billForm.addDetalizationForm();
    await billForm.fillCharge(charge, 0, j);
  }
  await billForm.submit();

  await Alert.on(page).hasText("Bill was updated successfully");

  await page.goto(`/finance/bill/view?id=${createdBillId}`);

  const billView = new BillView(page);
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

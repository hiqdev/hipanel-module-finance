import { test } from "@hipanel-core/fixtures";
import { expect } from "@playwright/test";
import BillForm from "@hipanel-module-finance/pages/bill/BillForm";
import Alert from "@hipanel-core/ui/Alert";

const bill = {
  client: "hipanel_test_user",
  type: "PayPal",
  currency: "$",
  sum: -777,
  quantity: 1,
  time: "",
  description: null,
  object: null,
  class: null,
  charges: [
    {
      class: "Client",
      object: "hipanel_test_admin",
      type: "PayPal",
      sum: 777,
      quantity: 1,
      description: null,
      time: null,
    },
  ],
};

test("Test I can create bill after pressing Toggle sign button and sums will be inverted @hipanel-module-finance @seller", async ({ sellerPage }) => {
  await sellerPage.goto("/finance/bill/create");
  await expect(sellerPage).toHaveTitle("Create payment");

  const billForm = new BillForm(sellerPage);
  await billForm.fill([bill]);
  await billForm.toggleSign();
  await billForm.submit();
  await Alert.on(sellerPage).hasText("Bill was created successfully");
  const billId = await billForm.getSavedBillId();

  await sellerPage.goto("/finance/bill/view?id=" + billId);
  await expect(sellerPage).toHaveTitle("hipanel_test_user: 777.00 usd");
  await expect(sellerPage.locator("td >> text=-$777.00")).toBeVisible();
});

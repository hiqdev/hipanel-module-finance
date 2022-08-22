import { test } from "@hipanel-core/fixtures";
import {expect, Page} from "@playwright/test";
import BillForm from "@hipanel-module-finance/pages/bill/BillForm";
import BillBase from "@hipanel-module-finance/pages/bill/BillBase";
import Bill from "@hipanel-module-finance/models/Bill";
import Charge from "@hipanel-module-finance/models/Charge";
import BillView from "@hipanel-module-finance/pages/bill/BillView";

let billId;

const bill: Bill = {
  client: "hipanel_test_user",
  type: "Block Storage HDD L2",
  currency: "$",
  sum: -762.7,
  quantity: 1,
  charges: [],
};

const charge1: Charge = {
  class: "Client",
  object: "hipanel_test_user1",
  type: "Cash",
  sum: 712.80,
  quantity: 1,
};

const charge2: Charge = {
  class: "Client",
  object: "hipanel_test_user2",
  type: "PayPal",
  sum: 49.90,
  quantity: 1,
};

test("Create and copy bill with charges @hipanel-module-finance @seller", async ({ sellerPage }) => {

  const billBase = new BillBase(sellerPage);
  const billForm = new BillForm(sellerPage);

  billBase.gotoCreateBill();
  bill.charges.push(charge1);
  bill.charges.push(charge2);
  await billForm.fillBill(bill);
  const billId = await billForm.createBill();

  await billBase.copyBill(billId);
  // await sellerPage.waitForTimeout(2000);
  const copyBillId = await billForm.getSavedBillId();

  await billBase.ensureBillDidntChange(bill, billId);

});


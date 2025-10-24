import { test } from "@hipanel-core/fixtures";
import BillForm from "@hipanel-module-finance/page/bill/BillForm";
import BillHelper from "@hipanel-module-finance/Helper/BillHelper";
import Bill from "@hipanel-module-finance/model/Bill";
import TransferForm from "@hipanel-module-finance/page/bill/TransferForm";
import Transfer from "@hipanel-module-finance/model/Transfer";
import Alert from "@hipanel-core/ui/Alert";

const transfer: Transfer = {
  sum: 100,
  client: 'hipanel_test_user2',
  receiverId: 'hipanel_test_user1',
  currency: '$'
};
const bill: Bill = {
  client: 'hipanel_test_user2',
  type: 'Positive balance correction',
  currency: '$',
  sum: 100,
  quantity: 1,
  charges: null
};

/**
 * IMPORTANT: this bill is needed because transfer can not be created when client has low balance
 */
test("Recharge account @hipanel-module-finance @seller", async ({ sellerPage }) => {

  const billHelper = new BillHelper(sellerPage);
  const billForm = new BillForm(sellerPage);

  await billHelper.gotoIndexBill();
  await billHelper.filterByClient(transfer.client);
  const totalSum = await billHelper.getTotalSum();

  if (totalSum <= 0) {
    bill.sum += -totalSum;
  }

  await billHelper.gotoCreateBill();
  await billForm.fillBill(bill);

});

test("Ensure transfer is working correctly @hipanel-module-finance @seller", async ({ sellerPage }) => {

  const transferForm = new TransferForm(sellerPage);

  await transferForm.gotoCreateTransfer();
  await transferForm.ensureICantCreateTransferWithoutRequiredData();
  await transferForm.fillTransfer(transfer);
  await transferForm.submit();
  await Alert.on(sellerPage).hasText('Transfer was completed');
});


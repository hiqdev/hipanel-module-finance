import {expect, Locator, Page} from "@playwright/test";
import Select2 from "@hipanel-core/input/Select2";
import Transfer from "@hipanel-module-finance/model/Transfer";
import SumWithCurrency from "@hipanel-core/input/SumWithCurrency";

export default class TransferForm {
  private page: Page;
  private submitBtn: Locator;

  private notBeBlankFields: Array<string> = [
    'Sum',
    'Client',
    'Receiver ID',
    'Currency',
  ];

  public constructor(page: Page) {
    this.page = page;
    this.submitBtn = this.page.locator("button.btn-success:has-text(\"Save\")");
  }

  async gotoCreateTransfer() {
    await this.page.goto("/finance/bill/create-transfer");
    await expect(this.page).toHaveTitle("Add internal transfer");
  }

  async ensureICantCreateTransferWithoutRequiredData() {
    await this.gotoCreateTransfer();
    await this.submit();
    this.notBeBlankFields.forEach(field => this.hasValidationError(field + ' cannot be blank.'));
  }

  async hasValidationError(msg: string) {
    await expect(this.page.locator(`.help-block-error:text-is("${msg}")`).first()).toBeVisible();
  }

  async fillTransfer(transferData: Transfer) {
    await SumWithCurrency.field(this.page, "bill", 0).setSumAndCurrency(transferData.sum, transferData.currency);
    await Select2.field(this.page, '#bill-0-client_id').setValue(transferData.client);
    await Select2.field(this.page, '#bill-0-receiver_id').setValue(transferData.receiverId);
  }

  async submit() {
    await this.submitBtn.click();
  }
}

<script lang="ts">
  import type { Purse } from "../types";
  import { useI18n } from "../i18n";

  let { purse, onRecharge }: {
      purse: Purse;
      onRecharge: () => void;
  } = $props();

  const t = useI18n();

  const getCurrencySymbol = (locale: string, currency: string) => (0).toLocaleString(locale, {
      style: "currency",
      currency,
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
  }).replace(/\d/g, "").trim();

</script>

<div class="purse-summary">
  <div class="purse-summary-left">
    <h4 class="purse-title">{purse.currency} {t('account')}</h4>
    <div class="purse-balance">
      <div class="balance">
        <span class="balance-label">{t('Balance')}</span>
        <a
            class="balance-value is-link"
            href="/finance/bill/index?BillSearch[currency_in][0]={purse.currency}&BillSearch[purse_id]={purse.id}&BillSearch[client_id]={purse.client_id}"
            title="View balance history"
        >
          <span class="currency">{getCurrencySymbol('en-US', purse.currency)}</span>
            {Number(purse.balance).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                useGrouping: true,
            })}
        </a>
      </div>
    </div>
  </div>
  <div class="purse-summary-right">
    <a href="/merchant/pay/deposit?currency={purse.currency.toLocaleLowerCase()}"
       class="btn btn-flat btn-success btn-recharge"
       onclick={onRecharge}>
      <i class="fa fa-plus-circle"></i> {t('Top-up account balance')}
    </a>
  </div>
</div>

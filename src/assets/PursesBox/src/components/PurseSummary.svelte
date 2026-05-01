<script lang="ts">
  import type { Purse } from "../types";
  import { useI18n } from "../i18n";

  let { purse, onRecharge, onKpiClick }: {
      purse: Purse;
      onRecharge: () => void;
      onKpiClick: (which: string) => void;
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
    <div class="purse-kpis">
      <div class="kpi">
        <span class="kpi-label">{t('Balance')}</span>
        <span
            class="kpi-value is-link"
            onclick={() => onKpiClick('balance')}
            onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); onKpiClick('balance'); } }}
            title="View balance history"
            role="button"
            tabindex="0"
        >
          <span class="currency">{getCurrencySymbol('en-US', purse.currency)}</span>
            {Number(purse.balance).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                useGrouping: true,
            })}
        </span>
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

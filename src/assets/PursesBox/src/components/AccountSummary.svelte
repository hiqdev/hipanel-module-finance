<script lang="ts">
  import type { Purse } from "../types";

  let { account, onRecharge, onKpiClick }: {
      account: Purse;
      onRecharge: () => void;
      onKpiClick: (which: string) => void;
  } = $props();

  const getCurrencySymbol = (locale: string, currency: string) => (0).toLocaleString(locale, {
      style: "currency",
      currency,
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
  }).replace(/\d/g, "").trim();

</script>

<div class="acct-summary">
  <div class="acct-summary-left">
    <h4 class="acct-title">{account.title}</h4>
    <div class="acct-kpis">
      <div class="kpi">
        <span class="kpi-label">Balance</span>
        <span
            class="kpi-value is-link"
            onclick={() => onKpiClick('balance')}
            onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); onKpiClick('balance'); } }}
            title="View balance history"
            role="button"
            tabindex="0"
        >
          <span class="currency">{getCurrencySymbol('en-US', account.currency)}</span>
            {Number(account.balance).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                useGrouping: true,
            })}
        </span>
      </div>
    </div>
  </div>
  <div class="acct-summary-right">
    <a href="/merchant/pay/deposit?currency={account.currency.toLocaleLowerCase()}"
       class="btn btn-flat btn-success btn-recharge"
       onclick={onRecharge}>
      <i class="fa fa-plus-circle"></i> Top-up account balance
    </a>
  </div>
</div>

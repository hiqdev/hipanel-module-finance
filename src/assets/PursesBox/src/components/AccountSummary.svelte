<script lang="ts">
  import type { Account } from '../types';

  let { account, onRecharge, onKpiClick }: {
    account: Account;
    onRecharge: () => void;
    onKpiClick: (which: string) => void;
  } = $props();
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
          <span class="currency">{account.symbol}</span>{account.balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </span>
      </div>
    </div>
  </div>
  <div class="acct-summary-right">
    <a href="#" class="btn btn-flat btn-success btn-recharge" onclick={onRecharge}>
      <i class="fa fa-plus-circle"></i> Top-up account balance
    </a>
  </div>
</div>

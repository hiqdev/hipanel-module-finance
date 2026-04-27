<script lang="ts">
  import type { Account } from "../types";
  import { fmtMoney } from "../data";

  let { accounts, activeId, onChange }: {
      accounts: Account[];
      activeId: string;
      onChange: (id: string) => void;
  } = $props();
</script>

<ul class="acct-tabs nav nav-tabs" role="tablist">
  {#each accounts as a}
      <li class="{a.id === activeId ? 'active' : ''}">
            <button
                role="tab"
                class="acct-tab {a.id === activeId ? 'is-active active' : ''}"
                onclick={(e) => {
                e.preventDefault();
                onChange(a.id)
            }}
            >
              <span>{a.code}</span>
              <span class="balance-mini">{fmtMoney(a.balance, a.symbol)}</span>
            </button>
      </li>
  {/each}
    <div class="acct-tabs-spacer"></div>
</ul>

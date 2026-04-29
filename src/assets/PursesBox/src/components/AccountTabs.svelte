<script lang="ts">
  import type { Purse } from "../types";
  import { fmtMoney } from "../data";

  let { purses, activeId, onChange }: {
      purses: Purse[];
      activeId: string;
      onChange: (id: string) => void;
  } = $props();
</script>

<ul class="acct-tabs nav nav-tabs" role="tablist">
  {#each purses as p}
      <li class:active={p.id === activeId}>
            <a
                href="#{p.id}"
                role="tab"
                class="acct-tab {p.id === activeId ? 'is-active active' : ''}"
                onclick={(e) => {
                e.preventDefault();
                onChange(p.id)
            }}
            >
              <span>{p.currency.toLocaleUpperCase()}</span>
              <span class="balance-mini">{fmtMoney(p.balance, p.currency)}</span>
            </a>
      </li>
  {/each}
    <div class="acct-tabs-spacer"></div>
</ul>

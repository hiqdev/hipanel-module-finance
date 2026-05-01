<script lang="ts">
  import type { Purse } from "../types";
  import { fmtMoney } from "../data";

  let { purses, activeId, onChange }: {
      purses: Purse[];
      activeId: string;
      onChange: (id: string) => void;
  } = $props();
</script>

<ul class="purse-tabs nav nav-tabs" role="tablist">
  {#each purses as p}
      <li class:active={p.id === activeId}>
            <a
                href="#{p.id}"
                role="tab"
                class="purse-tab {p.id === activeId ? 'is-active active' : ''}"
                onclick={(e) => {
                e.preventDefault();
                onChange(p.id)
            }}
            >
              <span>{p.currency.toLocaleUpperCase()}</span>
              <span class="balance-mini"
                    class:red-balance={p.balance < 0}
                    class:green-balance={p.balance > 0}
              >
                  {fmtMoney(p.balance, p.currency)}
              </span>
            </a>
      </li>
  {/each}
    <div class="purse-tabs-spacer"></div>
</ul>

<style>
    .red-balance {
        color: red;
    }

    .green-balance {
        color: green;
    }
</style>

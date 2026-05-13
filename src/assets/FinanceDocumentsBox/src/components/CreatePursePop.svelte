<script lang="ts">
  import type { Currency } from "../types";

  let { currencies, busy, error, onSubmit }: {
      currencies: Currency[];
      busy: boolean;
      error: string | null;
      onSubmit: (currency: string) => void;
  } = $props();

  let open = $state(false);
  let defaultCurrency = $derived(currencies[0]?.id ?? "");
  let currency = $state("");
  let canSubmit = $derived(!busy && !!currency);
  let wrapEl: HTMLElement;

  $effect(() => {
      if (!currencies.some((c) => c.id === currency)) {
          currency = defaultCurrency;
      }
  });

  $effect(() => {
      if (!open) return;
      const handler = (e: MouseEvent) => {
          if (wrapEl && !wrapEl.contains(e.target as Node)) open = false;
      };
      document.addEventListener("mousedown", handler);
      return () => document.removeEventListener("mousedown", handler);
  });

  function handleSubmit() {
      if (!canSubmit) return;
      onSubmit(currency);
  }
</script>

<li class="purse-add-wrap" bind:this={wrapEl}>
  <button
      type="button"
      class="purse-tab purse-tab-add"
      onclick={() => (open = !open)}
      title="Create new purse"
  >
    <i class="fa fa-plus"></i> Add new
  </button>

  {#if open}
    <div class="popover bottom in" role="tooltip" style="display:block; min-width:240px; left:0; top:100%; margin-top:2px">
      <div class="arrow" style="left:18px"></div>
      <h3 class="popover-title">Add new purse</h3>
      <div class="popover-content">
        <div class="form-group" style="margin-bottom:8px">
          <label class="control-label" for="new-purse-currency" style="font-size:12px; margin-bottom:3px">Currency</label>
          <select
              id="new-purse-currency"
              class="form-control input-sm"
              bind:value={currency}
              disabled={busy}
          >
            {#each currencies as c}
              <option value={c.id}>{c.label}</option>
            {/each}
          </select>
        </div>

        {#if error}
          <div class="text-danger" style="font-size:12px; margin-bottom:6px">
            <i class="fa fa-exclamation-circle"></i> {error}
          </div>
        {/if}

          <div style="display:flex; gap:6px; justify-content:flex-end">
          <button
              type="button"
              class="btn btn-default btn-xs"
              onclick={() => (open = false)}
              disabled={busy}
          >Cancel</button>
          <button
              type="button"
              class="btn btn-primary btn-xs"
              onclick={handleSubmit}
              disabled={!canSubmit}
          >
            {#if busy}
              <span class="spinner spinner-on-btn"></span> Creating…
            {:else}
              <i class="fa fa-plus"></i> Create
            {/if}
          </button>
        </div>
      </div>
    </div>
  {/if}
</li>

<style>
</style>

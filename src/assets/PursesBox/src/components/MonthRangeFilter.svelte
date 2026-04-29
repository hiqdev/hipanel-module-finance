<script lang="ts">
  import type { DateRange } from "../types";
  import { currentMonthKey, MONTH_NAMES, monthOptions } from "../data";

  let { value, onChange, monthsBack = 24 }: {
      value: DateRange;
      onChange: (range: DateRange) => void;
      monthsBack?: number;
  } = $props();

  let open = $state(false);
  let hover = $state<string | null>(null);
  let pickingFrom = $state(true);
  let ref: HTMLElement;

  function onDocClick(e: MouseEvent) {
      if (ref && !ref.contains(e.target as Node)) open = false;
  }

  $effect(() => {
      if (!open) return;
      document.addEventListener("mousedown", onDocClick);
      return () => document.removeEventListener("mousedown", onDocClick);
  });

  // oldest → newest
  let months = $derived(monthOptions(monthsBack).reverse());

  function formatLabel(key: string): string {
      const [y, m] = key.split("-");
      return `${MONTH_NAMES[parseInt(m, 10) - 1]} ${y}`;
  }

  let isActive = $derived(!!(value.from || value.to));

  let displayText = $derived.by(() => {
      if (!isActive) return null;
      const { from, to } = value;
      if (from && to) return from === to ? formatLabel(from) : `${formatLabel(from)} – ${formatLabel(to)}`;
      return from ? `From ${formatLabel(from)}` : `Until ${formatLabel(to!)}`;
  });

  function cmp(a: string, b: string) {
      return a < b ? -1 : a > b ? 1 : 0;
  }

  function inRange(k: string): boolean {
      const { from, to } = value;
      if (from && to) return cmp(k, from) >= 0 && cmp(k, to) <= 0;
      if (!pickingFrom && from && hover) {
          const lo = cmp(from, hover) <= 0 ? from : hover;
          const hi = cmp(from, hover) <= 0 ? hover : from;
          return cmp(k, lo) >= 0 && cmp(k, hi) <= 0;
      }
      return false;
  }

  function isEdge(k: string) {
      return k === value.from || k === value.to;
  }

  function onPick(key: string) {
      if (pickingFrom) {
          onChange({ from: key, to: null });
          pickingFrom = false;
      } else {
          const f = value.from;
          if (!f) {
              onChange({ from: key, to: null });
              pickingFrom = false;
          } else if (cmp(key, f) < 0) {
              onChange({ from: key, to: f });
              pickingFrom = true;
              open = false;
          } else {
              onChange({ from: f, to: key });
              pickingFrom = true;
              open = false;
          }
      }
  }

  let byYear = $derived.by(() => {
      const map: Record<string, typeof months> = {};
      months.forEach(m => {
          const [y] = m.key.split("-");
          if (!map[y]) map[y] = [];
          map[y].push(m);
      });
      return map;
  });

  let years = $derived(Object.keys(byYear).sort());

  function setThisMonth() {
      const c = currentMonthKey();
      onChange({ from: c, to: c });
      open = false;
  }

  function setLast3Months() {
      const c = currentMonthKey();
      const [y, m] = c.split("-");
      const d = new Date(+y, +m - 2, 1);
      const k = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
      onChange({ from: k, to: c });
      open = false;
  }

  function setThisYear() {
      const c = currentMonthKey();
      const y = c.split("-")[0];
      onChange({ from: `${y}-01`, to: c });
      open = false;
  }
</script>

<div class="filter-dd" bind:this={ref}>
  <button class="filter-btn {isActive ? 'is-active' : ''}" onclick={() => (open = !open)}>
    <i class="fa fa-calendar"></i>
      {#if displayText}
      <span style="max-width: 180px; overflow: hidden; text-overflow: ellipsis">{displayText}</span>
    {:else}
      <span class="label-muted">Date</span>
    {/if}
      <i class="fa fa-caret-down caret-i"></i>
  </button>

    {#if open}
    <div class="filter-menu month-range-menu">
      <div class="filter-menu-hd">
        <span>Date range</span>
          {#if isActive}
          <button type="button"
                  class="link-action"
                  onclick={() => { onChange({ from: null, to: null }); pickingFrom = true; }}>Clear</button>
        {/if}
      </div>

      <div class="mr-fields">
        <div
            class="mr-field {pickingFrom ? 'is-active' : ''}"
            role="button"
            tabindex="0"
            onclick={() => (pickingFrom = true)}
            onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pickingFrom = true; } }}
        >
          <span class="mr-field-label">From</span>
          <span class="mr-field-value">{value.from ? formatLabel(value.from) : 'Any'}</span>
        </div>
        <i class="fa fa-arrow-right mr-arrow"></i>
        <div
            class="mr-field {!pickingFrom ? 'is-active' : ''}"
            role="button"
            tabindex="0"
            onclick={() => (pickingFrom = false)}
            onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pickingFrom = false; } }}
        >
          <span class="mr-field-label">To</span>
          <span class="mr-field-value">{value.to ? formatLabel(value.to) : 'Any'}</span>
        </div>
      </div>

      <div class="mr-grid">
        {#each years as y}
          <div class="mr-year">
            <div class="mr-year-label">{y}</div>
            <div class="mr-months">
              {#each byYear[y] as m}
                {@const active = isEdge(m.key)}
                  {@const ranged = inRange(m.key)}
                  <button
                      class="mr-month {active ? 'is-edge' : ''} {ranged ? 'is-in-range' : ''}"
                      onclick={() => onPick(m.key)}
                      onmouseenter={() => (hover = m.key)}
                      onmouseleave={() => (hover = null)}
                  >
                  {MONTH_NAMES[parseInt(m.key.split('-')[1], 10) - 1]}
                </button>
              {/each}
            </div>
          </div>
        {/each}
      </div>

      <div class="mr-presets">
        <button type="button" class="link-action" onclick={setThisMonth}>This month</button>
        <button type="button" class="link-action" onclick={setLast3Months}>Last 3 months</button>
        <button type="button" class="link-action" onclick={setThisYear}>This year</button>
      </div>
    </div>
  {/if}
</div>

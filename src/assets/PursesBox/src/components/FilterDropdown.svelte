<script lang="ts">
  import type { FilterOption } from '../types';

  let { label, icon, options, selected, onChange, multi = true }: {
    label: string;
    icon: string;
    options: FilterOption[];
    selected: string[];
    onChange: (ids: string[]) => void;
    multi?: boolean;
  } = $props();

  let open = $state(false);
  let ref: HTMLElement;

  function onDocClick(e: MouseEvent) {
    if (ref && !ref.contains(e.target as Node)) open = false;
  }

  $effect(() => {
    if (!open) return;
    document.addEventListener('mousedown', onDocClick);
    return () => document.removeEventListener('mousedown', onDocClick);
  });

  let isActive = $derived(selected.length > 0 && selected.length < options.length);

  function toggle(id: string) {
    if (multi) {
      onChange(selected.includes(id) ? selected.filter(x => x !== id) : [...selected, id]);
    } else {
      onChange([id]);
      open = false;
    }
  }

  function onOptKeydown(e: KeyboardEvent, id: string) {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(id); }
  }
</script>

<div class="filter-dd" bind:this={ref}>
  <button class="filter-btn {isActive ? 'is-active' : ''}" onclick={() => (open = !open)}>
    <i class="fa {icon}"></i>
    {#if selected.length === 0 || selected.length === options.length}
      <span class="label-muted">{label}</span>
    {:else if selected.length === 1}
      {options.find(o => o.id === selected[0])?.label}
    {:else}
      {label} <span class="count-chip">{selected.length}</span>
    {/if}
    <i class="fa fa-caret-down caret-i"></i>
  </button>

  {#if open}
    <div class="filter-menu">
      <div class="filter-menu-hd">
        <span>{label}</span>
        {#if multi}
          <button type="button" class="link-action" onclick={() => onChange(options.map(o => o.id))}>Select all</button>
        {/if}
      </div>
      {#if options.length === 0}
        <div style="padding: 12px; font-size: 12px; color: var(--fg-5); text-align: center">No options available</div>
      {/if}
      {#each options as o}
        <div
          class="filter-opt"
          role="option"
          aria-selected={selected.includes(o.id)}
          tabindex="0"
          onclick={() => toggle(o.id)}
          onkeydown={(e) => onOptKeydown(e, o.id)}
        >
          {#if multi}
            <input type="checkbox" readonly checked={selected.includes(o.id)} tabindex="-1" />
          {/if}
          {#if o.dot}
            <span style="width: 8px; height: 8px; border-radius: 50%; background: {o.dot}; display: inline-block; flex-shrink: 0"></span>
          {/if}
          <span>{o.label}</span>
          {#if o.count != null}
            <span class="count">{o.count}</span>
          {/if}
        </div>
      {/each}
    </div>
  {/if}
</div>

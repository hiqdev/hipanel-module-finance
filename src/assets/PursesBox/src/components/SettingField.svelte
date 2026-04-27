<script lang="ts">
  import type { SelectOption } from '../types';

  let { label, value, options, icon, onSave }: {
    label: string;
    value: string;
    options: SelectOption[];
    icon?: string;
    onSave: (v: string) => void;
  } = $props();

  let open = $state(false);
  let wrapEl: HTMLElement;

  function onDocClick(e: MouseEvent) {
    if (wrapEl && !wrapEl.contains(e.target as Node)) open = false;
  }

  $effect(() => {
    if (!open) return;
    document.addEventListener('mousedown', onDocClick);
    return () => document.removeEventListener('mousedown', onDocClick);
  });

  function selectOpt(v: string) { onSave(v); open = false; }
  function onOptKeydown(e: KeyboardEvent, v: string) {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectOpt(v); }
  }
</script>

<div class="set-field">
  <span class="set-label">{label}</span>
  <div class="set-field-wrap" bind:this={wrapEl}>
    <div
      class="set-value {open ? 'is-editing' : ''}"
      role="button"
      tabindex="0"
      onclick={() => (open = !open)}
      onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open = !open; } }}
    >
      {#if icon}
        <i class="fa {icon}" style="color: var(--fg-4); font-size: 12px"></i>
      {/if}
      <span>{value}</span>
      <i class="fa fa-caret-down caret"></i>
      <span class="edit-hint">click to edit</span>
    </div>

    {#if open}
      <div class="set-dropdown" role="listbox">
        {#each options as opt}
          <div
            class="set-option {opt.value === value ? 'is-selected' : ''}"
            role="option"
            aria-selected={opt.value === value}
            tabindex="0"
            onclick={() => selectOpt(opt.value)}
            onkeydown={(e) => onOptKeydown(e, opt.value)}
          >
            {#if opt.icon}
              <i class="fa {opt.icon}" style="color: var(--fg-4); width: 14px"></i>
            {/if}
            <span>
              {opt.label}
              {#if opt.sub}<span class="set-option-sub">{opt.sub}</span>{/if}
            </span>
            {#if opt.value === value}
              <i class="fa fa-check"></i>
            {/if}
          </div>
        {/each}
        <div
          class="set-option"
          role="option"
          aria-selected="false"
          tabindex="0"
          style="color: var(--link); border-top: 1px solid var(--border-subtle)"
        >
          <i class="fa fa-plus" style="width: 14px"></i>
          <span>Add new…</span>
        </div>
      </div>
    {/if}
  </div>
</div>

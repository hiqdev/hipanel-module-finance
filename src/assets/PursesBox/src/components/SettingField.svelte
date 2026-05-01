<script lang="ts">
  import type { SelectOption } from "../types";

  let { label, value, options, createNewUrl, icon, loading = false, searchable = false, onOpen, onSearch, onSave }: {
      label: string;
      value: string;
      options: SelectOption[];
      createNewUrl: string;
      icon?: string;
      loading?: boolean;
      searchable?: boolean;
      onOpen?: () => void;
      onSearch?: (q: string) => void;
      onSave: (v: string) => void;
  } = $props();

  let open = $state(false);
  let query = $state("");
  let wrapEl: HTMLElement;
  let searchInput = $state<HTMLInputElement | undefined>(undefined);
  let debounceTimer: ReturnType<typeof setTimeout>;

  function onDocClick(e: MouseEvent) {
      if (wrapEl && !wrapEl.contains(e.target as Node)) open = false;
  }

  $effect(() => {
      if (!open) return;
      document.addEventListener("mousedown", onDocClick);
      return () => document.removeEventListener("mousedown", onDocClick);
  });

  // Auto-focus search input when dropdown opens
  $effect(() => {
      if (!open || !searchable) return;
      const t = setTimeout(() => searchInput?.focus(), 0);
      return () => clearTimeout(t);
  });

  // Reset query on close; clear debounce timer on close or unmount
  $effect(() => {
      if (!open) {
          clearTimeout(debounceTimer);
          query = "";
      }
      return () => clearTimeout(debounceTimer);
  });

  function toggle() {
      if (loading) return;
      if (!open) onOpen?.();
      open = !open;
  }

  function selectOpt(v: string) {
      onSave(v);
      open = false;
  }

  function onOptKeydown(e: KeyboardEvent, v: string) {
      if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          selectOpt(v);
      }
  }

  function handleSearch(e: Event) {
      const q = (e.target as HTMLInputElement).value;
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => onSearch?.(q), 300);
  }
</script>

<div class="set-field">
  <span class="set-label">{label}</span>
  <div class="set-field-wrap" bind:this={wrapEl}>
    <div
        class="set-value"
        class:is-loading={loading}
        class:is-editing={open}
        role="button"
        tabindex={loading ? -1 : 0}
        onclick={toggle}
        onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); } }}
    >
      {#if icon}
        <i class="fa {icon}" style="color: var(--fg-4); font-size: 12px"></i>
      {/if}
        <span>{value}</span>
        {#if loading && !open}
        <i class="fa fa-spinner fa-spin" style="color: var(--fg-4); font-size: 12px; margin-left: auto"></i>
      {:else}
        <i class="caret"></i>
        <span class="edit-hint">click to edit</span>
      {/if}
    </div>

      {#if open}
      <div class="set-dropdown" role="listbox">
        {#if searchable}
          <div class="set-search">
            <input
                bind:this={searchInput}
                type="text"
                class="form-control input-sm"
                placeholder="Search…"
                bind:value={query}
                oninput={handleSearch}
                onkeydown={(e) => { if (e.key === 'Escape') open = false; }}
            />
              {#if loading}
              <i class="fa fa-spinner fa-spin set-search-spin"></i>
            {/if}
          </div>
        {/if}

          {#if options.length === 0 && !loading && query}
          <div class="set-option" style="color: var(--fg-4); cursor: default">
            <i class="fa fa-search" style="width: 14px; color: var(--fg-5)"></i>
            <span>No results found</span>
          </div>
        {:else if options.length === 0 && loading}
          <div class="set-option" style="color: var(--fg-4); cursor: default">
            <i class="fa fa-spinner fa-spin" style="width: 14px"></i>
            <span>Loading…</span>
          </div>
        {:else}
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
        {/if}

          <a
              class="set-option"
              role="option"
              aria-selected="false"
              tabindex="0"
              style="color: var(--link); border-top: 1px solid var(--border-subtle)"
              href="{createNewUrl}"
          >
          <i class="fa fa-plus" style="width: 14px"></i>
          <span>Add new…</span>
        </a>
      </div>
    {/if}
  </div>
</div>

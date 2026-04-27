<script lang="ts">
  import { untrack } from 'svelte';
  import type { ModalKind } from '../types';
  import { DOC_TYPES, monthOptions, currentMonthKey } from '../data';

  let { mode, initial, existingMonths, busy, progress, onClose, onSubmit }: {
    mode: ModalKind;
    initial?: { type: string; month: string };
    existingMonths: Record<string, string[]>;
    busy: boolean;
    progress: number;
    onClose: () => void;
    onSubmit: (args: { type: string; month: string; willReplace: boolean; mode: ModalKind }) => void;
  } = $props();

  // untrack: deliberately read initial prop only once — the modal remounts on each open.
  let type = $state(untrack(() => initial?.type ?? DOC_TYPES[0].id));
  let month = $state(untrack(() => initial?.month ?? currentMonthKey()));

  let locked = $derived(mode === 'update-replace' || mode === 'preview-updated');

  let willReplace = $derived.by(() => {
    if (mode === 'preview' || mode === 'preview-updated') return false;
    return !!(existingMonths[type]?.includes(month));
  });

  const titles: Record<ModalKind, { title: string; btn: string; icon: string; btnBusy: string }> = {
    'generate':        { title: 'Generate document',           btn: 'Generate',           icon: 'fa-cog',         btnBusy: 'Generating…' },
    'preview':         { title: 'Preview document',            btn: 'Preview',            icon: 'fa-eye',         btnBusy: 'Building preview…' },
    'update-replace':  { title: 'Update and replace document', btn: 'Generate & replace', icon: 'fa-refresh',     btnBusy: 'Generating…' },
    'preview-updated': { title: 'Preview updated document',    btn: 'Preview updated',    icon: 'fa-search-plus', btnBusy: 'Building preview…' },
  };

  let t = $derived(titles[mode]);
  let months = $derived(monthOptions(12));
  let existingForType = $derived(existingMonths[type] ?? []);
</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div class="modal-backdrop" onclick={busy ? undefined : onClose} onkeydown={() => {}}>
  <div class="modal" role="dialog" aria-modal="true" tabindex="-1" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-hd">
      <h3><i class="fa {t.icon}"></i> {t.title}</h3>
      {#if !busy}
        <button class="modal-x" onclick={onClose} aria-label="Close"><i class="fa fa-times"></i></button>
      {/if}
    </div>

    <div class="modal-bd">
      {#if locked}
        <div class="modal-note">
          <i class="fa fa-info-circle"></i>
          Regenerating the document for the same type and month as the original.
        </div>
      {/if}

      <div class="form-row">
        <label for="gen-doc-type">Document type</label>
        <div class="select-wrap">
          <select
            id="gen-doc-type"
            class="form-select"
            value={type}
            onchange={(e) => (type = (e.target as HTMLSelectElement).value)}
            disabled={locked}
          >
            {#each DOC_TYPES as dt}
              <option value={dt.id}>{dt.label}</option>
            {/each}
          </select>
          <i class="fa fa-caret-down select-caret"></i>
        </div>
      </div>

      <div class="form-row">
        <p class="form-row-label">Period</p>
        <div class="month-picker">
          {#each months as m}
            {@const exists = existingForType.includes(m.key)}
            {@const isOn = month === m.key}
            <button
              class="month-opt {isOn ? 'is-on' : ''} {exists ? 'has-existing' : ''}"
              onclick={() => { if (!locked) month = m.key; }}
              disabled={locked}
              title={exists ? 'A document already exists for this month' : ''}
            >
              <span class="m-label">{m.label}</span>
              {#if exists}<span class="m-dot" title="Document exists"></span>{/if}
            </button>
          {/each}
        </div>
      </div>

      {#if willReplace}
        <div class="modal-warn">
          <i class="fa fa-exclamation-triangle"></i>
          <div>
            <strong>A document already exists for this type and period.</strong>
            <div>Proceeding will <strong>replace</strong> the existing document. This action cannot be undone.</div>
          </div>
        </div>
      {/if}

      {#if mode === 'preview' || mode === 'preview-updated'}
        <div class="modal-note modal-note-info">
          <i class="fa fa-eye"></i>
          Preview mode — the generated document will be shown but <strong>not saved</strong>.
        </div>
      {/if}

      {#if busy}
        <div class="modal-progress">
          <div class="modal-progress-text">
            <span class="spinner spinner-lg"></span>
            <div>
              <strong>{t.btnBusy}</strong>
              <div class="progress-sub">This can take up to 30 seconds. Please don't close this window.</div>
            </div>
          </div>
          <div class="progress-bar-wrap">
            <div class="progress-bar-inner" style="width: {progress}%"></div>
          </div>
        </div>
      {/if}
    </div>

    <div class="modal-ft">
      <button class="btn-ghost" onclick={onClose} disabled={busy}>
        {busy ? 'Working…' : 'Cancel'}
      </button>
      <button
        class="btn-act {willReplace ? 'is-warn' : 'is-primary'}"
        onclick={() => onSubmit({ type, month, willReplace, mode })}
        disabled={busy}
      >
        {#if busy}
          <span class="spinner spinner-on-btn"></span> {t.btnBusy}
        {:else}
          <i class="fa {t.icon}"></i> {t.btn}
        {/if}
      </button>
    </div>
  </div>
</div>

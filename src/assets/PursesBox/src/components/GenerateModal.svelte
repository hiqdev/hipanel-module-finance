<script lang="ts">
  import { untrack } from "svelte";
  import type { DocType, ModalKind } from "../types";
  import { currentMonthKey, monthsForYear } from "../data";

  let { mode, initial, types, existingMonths, busy, progress, onClose, onSubmit, language }: {
      mode: ModalKind;
      initial?: { type: string; month: string };
      types: DocType[];
      existingMonths: Record<string, string[]>;
      busy: boolean;
      progress: number;
      onClose: () => void;
      onSubmit: (args: { type: string; month: string; willReplace: boolean; mode: ModalKind }) => void;
      language: string;
  } = $props();

  // untrack: deliberately read initial prop only once — the modal remounts on each open.
  let type = $state(untrack(() => initial?.type ?? types[0]?.id ?? ""));
  let selectedYear = $state(untrack(() => +(initial?.month ?? currentMonthKey()).split("-")[0]));
  let selectedMonthNum = $state(untrack(() => +(initial?.month ?? currentMonthKey()).split("-")[1]));
  let month = $derived(`${selectedYear}-${String(selectedMonthNum).padStart(2, "0")}`);

  let locked = $derived(mode === "update-replace" || mode === "preview-updated");

  let willReplace = $derived.by(() => {
      if (mode === "preview" || mode === "preview-updated") return false;
      return !!(existingMonths[type]?.includes(month));
  });

  const titles: Record<ModalKind, { title: string; btn: string; icon: string; btnBusy: string }> = {
      "generate": { title: "Generate document", btn: "Generate", icon: "fa-cog", btnBusy: "Generating…" },
      "preview": { title: "Preview document", btn: "Preview", icon: "fa-eye", btnBusy: "Building preview…" },
      "update-replace": { title: "Update and replace document", btn: "Generate & replace", icon: "fa-refresh", btnBusy: "Generating…" },
      "preview-updated": {
          title: "Preview updated document",
          btn: "Preview updated",
          icon: "fa-search-plus",
          btnBusy: "Building preview…",
      },
  };

  let t = $derived(titles[mode]);
  let minYear = $derived.by(() => {
      const allKeys = Object.values(existingMonths).flat();
      const cur = new Date().getFullYear();
      return allKeys.length ? Math.min(...allKeys.map(k => +k.split("-")[0])) : cur - 5;
  });
  let yearOptions = $derived(
      Array.from({ length: new Date().getFullYear() - minYear + 1 }, (_, i) => minYear + i).reverse(),
  );
  let months12 = $derived(monthsForYear(selectedYear, language));
  let existingForType = $derived(existingMonths[type] ?? []);
</script>

<div class="modal-backdrop fade in"></div>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div
    class="modal fade in"
    style="display:block"
    role="dialog"
    aria-modal="true"
    tabindex="-1"
    onclick={busy ? undefined : onClose}
    onkeydown={() => {}}
>
  <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
  <div class="modal-dialog" role="document" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-content">
      <div class="modal-header">
        {#if !busy}
          <button type="button" class="close" onclick={onClose} aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        {/if}
          <h4 class="modal-title"><i class="fa {t.icon}"></i> {t.title}</h4>
      </div>

      <div class="modal-body">
        {#if locked}
          <div class="modal-note">
            <i class="fa fa-info-circle"></i>
            Regenerating the document for the same type and month as the original.
          </div>
        {/if}

          <div class="form-group">
          <label class="control-label" for="gen-doc-type">Document type</label>
          <select
              id="gen-doc-type"
              class="form-control"
              value={type}
              onchange={(e) => (type = (e.target as HTMLSelectElement).value)}
              disabled={locked}
          >
            {#each types as dt}
              <option value={dt.id}>{dt.label}</option>
            {/each}
          </select>
        </div>

        <div class="form-group">
          <p class="form-row-label">Period</p>
          <select
              class="form-control"
              style="width:auto;display:inline-block;margin-bottom:8px"
              value={selectedYear}
              onchange={(e) => (selectedYear = +(e.target as HTMLSelectElement).value)}
              disabled={locked}
          >
            {#each yearOptions as y}
              <option value={y}>{y}</option>
            {/each}
          </select>
          <div class="month-picker">
            {#each months12 as m}
              {@const exists = existingForType.includes(m.key)}
              {@const isOn = month === m.key}
              <button
                  type="button"
                  class="month-opt {isOn ? 'is-on' : ''} {exists ? 'has-existing' : ''}"
                  onclick={() => { if (!locked) selectedMonthNum = +m.key.split("-")[1]; }}
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

      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick={onClose} disabled={busy}>
          {busy ? 'Working…' : 'Cancel'}
        </button>
        <button
            type="button"
            class="btn {willReplace ? 'btn-warning' : 'btn-primary'}"
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
</div>

<script lang="ts">
  import type { Doc } from "../types";
  import { fmtDate, typeMeta } from "../data";

  let { doc, files, onClose, onApply, language }: {
      doc?: Doc;
      files: string[];
      onClose: () => void;
      onApply?: (() => void) | null;
      language: string;
  } = $props();

  let t = $derived(doc ? typeMeta(doc.type) : null);
  let date = $derived(doc ? fmtDate(doc.date, language) : null);
</script>

<div class="modal-backdrop fade in"></div>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div
    class="modal fade in"
    style="display:block"
    role="dialog"
    aria-modal="true"
    tabindex="-1"
    onclick={onClose}
    onkeydown={() => {}}
>
  <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
  <div class="modal-dialog modal-lg" role="document" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick={onClose} aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
          <i class="fa fa-file-text-o"></i>
          {#if t && date}
            Preview — {t.label}
            <span class="modal-title-sub">{doc?.number} · {date.short} {date.year}</span>
          {:else}
            Preview
          {/if}
        </h4>
      </div>
      <div class="modal-body preview-modal-body">
        {#if files.length > 0}
          {#each files as url (url)}
            <iframe src={url} class="preview-iframe" title="Document preview"></iframe>
          {/each}
        {:else}
          <div class="preview-no-files">
            <i class="fa fa-file-text-o"></i>
            <span>No preview available</span>
          </div>
        {/if}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick={onClose}>
          <i class="fa fa-times"></i> Discard
        </button>
          {#if onApply}
          <button type="button" class="btn btn-primary" onclick={onApply}>
            <i class="fa fa-save"></i> Save this version
          </button>
        {/if}
      </div>
    </div>
  </div>
</div>

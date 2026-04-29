<script lang="ts">
  import type { Doc } from "../types";
  import { fmtDate, typeMeta } from "../data";

  let { doc, onClose, onSave }: {
      doc: Doc;
      onClose: () => void;
      onSave?: (() => void) | null;
  } = $props();

  let t = $derived(typeMeta(doc.type));
  let date = $derived(fmtDate(doc.date));
  let lines1 = $derived(Array.from({ length: 8 }, (_, i) => 90 - (i % 3) * 12));
  let lines2 = $derived(Array.from({ length: 4 }, (_, i) => 70 - i * 8));
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
        <h4 class="modal-title"><i class="fa fa-file-text-o"></i> Preview — {t.label}</h4>
      </div>
      <div class="modal-body" style="background: #ecf0f5; padding: 20px;">
        <div class="preview-sheet">
          <div class="preview-sheet-hd">
            <div>
              <div class="ps-kind">{t.label}</div>
              <div class="ps-ref">{doc.number}</div>
            </div>
            <div class="ps-date">{date.short} {date.year}</div>
          </div>
          <div class="preview-lines">
            {#each lines1 as w}
              <div class="ps-line" style="width: {w}%"></div>
            {/each}
          </div>
          <div class="preview-lines" style="margin-top: 24px">
            {#each lines2 as w}
              <div class="ps-line" style="width: {w}%"></div>
            {/each}
          </div>
          <div class="preview-watermark">PREVIEW — NOT SAVED</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick={onClose}>
          <i class="fa fa-times"></i> Discard
        </button>
          {#if onSave}
          <button type="button" class="btn btn-primary" onclick={onSave}>
            <i class="fa fa-save"></i> Save this version
          </button>
        {/if}
      </div>
    </div>
  </div>
</div>

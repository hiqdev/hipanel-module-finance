<script lang="ts">
  import type { Doc } from '../types';
  import { typeMeta, fmtDate } from '../data';

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

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div class="modal-backdrop" onclick={onClose} onkeydown={() => {}}>
  <div class="modal modal-lg" role="dialog" aria-modal="true" tabindex="-1" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-hd">
      <h3><i class="fa fa-file-text-o"></i> Preview — {t.label}</h3>
      <button class="modal-x" onclick={onClose} aria-label="Close"><i class="fa fa-times"></i></button>
    </div>

    <div class="modal-bd modal-bd-preview">
      <div class="preview-sheet">
        <div class="preview-sheet-hd">
          <div>
            <div class="ps-kind">{t.label}</div>
            <div class="ps-ref">{doc.ref}</div>
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

    <div class="modal-ft">
      <button class="btn-ghost" onclick={onClose}>
        <i class="fa fa-times"></i> Discard
      </button>
      {#if onSave}
        <button class="btn-act is-primary" onclick={onSave}>
          <i class="fa fa-save"></i> Save this version
        </button>
      {/if}
    </div>
  </div>
</div>

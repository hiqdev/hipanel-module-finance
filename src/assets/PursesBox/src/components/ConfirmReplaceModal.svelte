<script lang="ts">
  import type { Doc } from '../types';
  import { typeMeta, fmtDate } from '../data';

  let { doc, onClose, onConfirm }: {
    doc: Doc;
    onClose: () => void;
    onConfirm: () => void;
  } = $props();

  let t = $derived(typeMeta(doc.type));
  let date = $derived(fmtDate(doc.date));
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
  <div class="modal-dialog modal-sm" role="document" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick={onClose} aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
          <i class="fa fa-exclamation-triangle text-warning"></i> Replace document?
        </h4>
      </div>
      <div class="modal-body">
        <p>
          The existing document <strong>{doc.ref}</strong> ({t.label}, {date.short} {date.year})
          will be regenerated and <strong>replaced</strong>.
        </p>
        <p class="text-muted">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick={onClose}>Cancel</button>
        <button type="button" class="btn btn-warning" onclick={onConfirm}>
          <i class="fa fa-refresh"></i> Yes, replace
        </button>
      </div>
    </div>
  </div>
</div>

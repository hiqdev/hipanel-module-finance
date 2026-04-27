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

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div class="modal-backdrop" onclick={onClose} onkeydown={() => {}}>
  <div class="modal modal-sm" role="dialog" aria-modal="true" tabindex="-1" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-hd">
      <h3><i class="fa fa-exclamation-triangle" style="color: var(--warning)"></i> Replace document?</h3>
      <button class="modal-x" onclick={onClose} aria-label="Close"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-bd">
      <p style="margin-top: 0">
        The existing document <strong>{doc.ref}</strong> ({t.label}, {date.short} {date.year})
        will be regenerated and <strong>replaced</strong>.
      </p>
      <p style="color: var(--fg-3)">This action cannot be undone.</p>
    </div>
    <div class="modal-ft">
      <button class="btn-ghost" onclick={onClose}>Cancel</button>
      <button class="btn-act is-warn" onclick={onConfirm}>
        <i class="fa fa-refresh"></i> Yes, replace
      </button>
    </div>
  </div>
</div>

<script lang="ts">
  import type { Doc } from "../types";
  import { docTypeColor, fmtDate } from "../data";

  let { doc, density, busy, onAction }: {
      doc: Doc;
      density: string;
      busy: boolean;
      onAction: (kind: string, doc: Doc) => void;
  } = $props();

  let date = $derived(fmtDate(doc.date));
  let rowPad = $derived(density === "compact" ? "8px 14px" : "12px 14px");
</script>

<tr class:isNew={doc.isNew} class="{busy ? 'is-busy' : ''}">
  <td style="padding: {rowPad}">
    <span class="type-pill">
      <span class="dot" style:background-color={docTypeColor(doc.type)}></span>
        {doc.type_label}
    </span>
  </td>
  <td style="padding: {rowPad}">
    <div class="doc-name">
        <i class="fa fa-file-text-o"></i>
        <span class="doc-ref">{doc.number.length ? doc.number : '--'}</span>
        {#if doc.isNew}<span class="label label-warning doc-new-badge">NEW</span>{/if}
        <span class="doc-sub">{doc.filename}</span>
    </div>
  </td>
  <td style="padding: {rowPad}">
    <span class="doc-date">
      {date.short} <span class="year">{date.year}</span>
    </span>
  </td>
  <td style="padding: {rowPad}" class="row-actions-cell">
    {#if busy}
      <div class="row-busy">
        <span class="spinner"></span> Generating…
      </div>
    {:else}
      <div class="row-actions">
        <button class="ra-btn" onclick={() => onAction('download', doc)} title="Download file">
          <i class="fa fa-download"></i>
        </button>
        <a
            class="ra-btn"
            href="document/view?id={doc.id}"
            onclick={(e) => { e.preventDefault(); onAction('view', doc); }}
            title="View document"
        >
          <i class="fa fa-eye"></i>
        </a>
        <button class="ra-btn" onclick={() => onAction('preview-updated', doc)} title="Preview updated version (do not save)">
          <i class="fa fa-search-plus"></i>
        </button>
        <button class="ra-btn ra-btn-warn" onclick={() => onAction('update-replace', doc)} title="Regenerate and replace this document">
          <i class="fa fa-refresh"></i>
        </button>
      </div>
    {/if}
  </td>
</tr>

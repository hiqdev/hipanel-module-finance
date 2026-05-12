<script lang="ts">
  import type { Doc } from "../types";
  import { docTypeColor, fmtDate } from "../data";

  let { doc, busy, onAction, language, canPreviewAndGenerate }: {
      doc: Doc;
      busy: boolean;
      onAction: (kind: string, doc: Doc) => void;
      language: string;
      canPreviewAndGenerate: boolean;
  } = $props();

  let date = $derived(fmtDate(doc.date, language));
</script>

<tr class:isNew={doc.isNew} class="{busy ? 'is-busy' : ''}">
  <td>
    <span class="type-pill">
      <span class="dot" style:background-color={docTypeColor(doc.type)}></span>
        {doc.type_label}
    </span>
  </td>
  <td>
    <div class="doc-name">
        <i class="fa fa-file-text-o"></i>
        <a href={`/document/document/view?id=${doc.id}`}
           target="_blank"
           class="doc-ref">
            {doc.number.length ? doc.number : '- not set -'}
        </a>
        {#if doc.isNew}<span class="label label-warning doc-new-badge">NEW</span>{/if}
        <span class="doc-sub">{doc.filename}</span>
    </div>
  </td>
  <td>
    <span class="doc-date">
      {date.short} <span class="year">{date.year}</span>
    </span>
  </td>
  <td class="row-actions-cell">
    {#if busy}
      <div class="row-busy">
        <span class="spinner"></span> Generating…
      </div>
    {:else}
      <div class="row-actions">
        <a class="ra-btn"
           target="_blank"
           href={`/file/get?id=${doc.file_id}&downloadname=${encodeURIComponent(doc.filename)}&nocache=1`}
           title={"Download file"}
        >
          <i class="fa fa-download"></i>
        </a>
        <a class="ra-btn"
           target="_blank"
           href={`/file/${doc.file_id}/${encodeURIComponent(doc.filename)}?nocache=1`}
           title={"View document"}
        >
          <i class="fa fa-eye"></i>
        </a>

          {#if canPreviewAndGenerate && doc.type !== 'internal_invoice'}
          <button class="ra-btn" onclick={() => onAction('preview', doc)} title="Preview updated version (do not save)">
            <i class="fa fa-search-plus"></i>
          </button>
        {/if}
          {#if canPreviewAndGenerate}
          <button class="ra-btn ra-btn-warn" onclick={() => onAction('update', doc)} title="Regenerate and replace this document">
            <i class="fa fa-refresh"></i>
          </button>
        {/if}
      </div>
    {/if}
  </td>
</tr>

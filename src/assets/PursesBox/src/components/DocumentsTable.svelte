<script lang="ts">
  import type { Doc, SortState } from "../types";
  import DocumentRow from "./DocumentRow.svelte";

  let { docs, sort, onSort, density, busyIds, onRowAction, language }: {
      docs: Doc[];
      sort: SortState;
      onSort: (key: SortState["key"]) => void;
      density: string;
      busyIds: string[];
      onRowAction: (kind: string, doc: Doc) => void;
      language: string;
  } = $props();
</script>

<div class="docs-table-wrap">
  <table class="table table-hover table-condensed docs-table">
    <thead>
      <tr>
        <th
            class="type-col {sort.key === 'type' ? 'sorted' : ''}"
            onclick={() => onSort('type')}
        >
          Type
            {#if sort.key === 'type'}
            <i class="fa sort-i fa-caret-{sort.dir === 'asc' ? 'up' : 'down'}"></i>
          {/if}
        </th>
        <th
            class="{sort.key === 'ref' ? 'sorted' : ''}"
            onclick={() => onSort('ref')}
        >
          Document no.
            {#if sort.key === 'ref'}
            <i class="fa sort-i fa-caret-{sort.dir === 'asc' ? 'up' : 'down'}"></i>
          {/if}
        </th>
        <th
            class="date-col {sort.key === 'date' ? 'sorted' : ''}"
            onclick={() => onSort('date')}
        >
          Date
            {#if sort.key === 'date'}
            <i class="fa sort-i fa-caret-{sort.dir === 'asc' ? 'up' : 'down'}"></i>
          {/if}
        </th>
        <th class="act-col">Actions</th>
      </tr>
    </thead>
    <tbody>
      {#if docs.length === 0}
        <tr>
          <td colspan="4">
            <div class="docs-empty">
              <i class="fa fa-folder-open-o"></i>
              <h4>No documents match your filters</h4>
              <p>Try clearing filters or generate a new document.</p>
            </div>
          </td>
        </tr>
      {:else}
        {#each docs as doc (doc.id)}
          <DocumentRow
              {doc}
              {density}
              busy={busyIds.includes(doc.id)}
              onAction={onRowAction}
              {language}
          />
        {/each}
      {/if}
    </tbody>
  </table>
</div>

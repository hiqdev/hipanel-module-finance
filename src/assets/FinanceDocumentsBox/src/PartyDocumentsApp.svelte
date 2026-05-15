<script lang="ts">
  import { untrack } from "svelte";
  import type { PartyDocumentsProps } from "./types";
  import { initI18n } from "./i18n";
  import { permissions } from "./permissions";
  import { useDocumentFilters } from "./composables/useDocumentFilters.svelte";
  import DocumentsPanel from "./components/DocumentsPanel.svelte";

  let { language, permissions: permKeys = [], documents }: PartyDocumentsProps = $props();

  initI18n(() => language);
  untrack(() => permissions.init(permKeys));

  const docFilters = useDocumentFilters(() => documents, () => "0", () => language);
</script>

<DocumentsPanel
    pagedDocs={docFilters.pagedDocs}
    filters={docFilters.filters}
    typeOptions={docFilters.typeOptions}
    pageCount={docFilters.pageCount}
    totalFiltered={docFilters.filtered.length}
    totalBase={docFilters.totalDocs}
    activeChips={docFilters.activeChips}
    {language}
    busyIds={[]}
    canPreviewAndGenerate={false}
    onSearch={docFilters.setSearch}
    onTypeFilter={docFilters.setTypeFilter}
    onDateRange={docFilters.setDateRange}
    onPage={docFilters.setPage}
    onSort={docFilters.handleSort}
    onClearFilters={docFilters.handleClearFilters}
/>

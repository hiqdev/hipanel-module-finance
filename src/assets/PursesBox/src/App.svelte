<script lang="ts">
  import { untrack } from "svelte";
  import type { Doc, Purse, PursesBoxProps } from "./types";
  import { docMonthKey } from "./data";
  import { initI18n, useI18n } from "./i18n";
  import { permissions } from "./permissions";
  import { useToast } from "./composables/useToast.svelte";
  import { PAGE_SIZE, useDocumentFilters } from "./composables/useDocumentFilters.svelte";
  import { useDocumentGeneration } from "./composables/useDocumentGeneration.svelte";
  import PurseTabs from "./components/PurseTabs.svelte";
  import PurseSummary from "./components/PurseSummary.svelte";
  import PurseSettings from "./components/PurseSettings.svelte";
  import FilterDropdown from "./components/FilterDropdown.svelte";
  import MonthRangeFilter from "./components/MonthRangeFilter.svelte";
  import DocumentsTable from "./components/DocumentsTable.svelte";
  import DocumentsPagination from "./components/DocumentsPagination.svelte";
  import GenerateModal from "./components/GenerateModal.svelte";
  import PreviewResultModal from "./components/PreviewResultModal.svelte";
  import ConfirmReplaceModal from "./components/ConfirmReplaceModal.svelte";

  let { language, permissions: permKeys = [], purses: initialPurses = [] }: PursesBoxProps = $props();

  initI18n(() => language);
  const t = useI18n();

  untrack(() => permissions.init(permKeys));
  let canPreviewAndGenerate = permissions.can("document.generate") && !permissions.can("has-own-seller");

  // State is scoped per purse so tab switching preserves filters/sort/page.
  const seedPurses = untrack(() => initialPurses.length ? initialPurses : []);
  let activeId = $state<string>(seedPurses[0].id);
  let purses = $state<Purse[]>([...seedPurses]);
  let docsByPurse = $state<Record<string, Doc[]>>(
      Object.fromEntries(seedPurses.map(p => [p.id, [...p.documents]])),
  );

  let purse = $derived(purses.find(p => p.id === activeId)!);
  let minDocYear = $derived.by(() => {
      const years = Object.values(docsByPurse).flat().map(d => +docMonthKey(d.date).split("-")[0]);
      return years.length ? Math.min(...years) : new Date().getFullYear() - 3;
  });

  const toast = useToast();
  const docFilters = useDocumentFilters(
      () => docsByPurse[activeId] ?? [],
      () => activeId,
      () => language,
  );
  const generation = useDocumentGeneration(
      () => docsByPurse[activeId] ?? [],
      (docs) => {
          docsByPurse[activeId] = docs;
      },
      toast.show,
      () => purse,
  );

  function handleSettingChange(field: string, value: string) {
      purses = purses.map(p => {
          if (p.id !== activeId) return p;
          if (field === "contact") return { ...p, contact: { ...p.contact, name: value } };
          return { ...p, [field]: value };
      });
      toast.show(`Updated ${field === "requisite" ? "requisite" : "contact"}`);
  }

  function handleRecharge() {
      toast.show(`Opening top-up flow for ${purse.currency.toUpperCase()}`);
  }

</script>

<div class="accounts-block nav-tabs-custom">

  <PurseTabs {purses} {activeId} onChange={(id) => (activeId = id)} {language}/>

  <PurseSummary {purse} onRecharge={handleRecharge}/>

  <PurseSettings {purse} onChange={handleSettingChange}/>

  <div class="purse-docs">
    <div class="docs-head">
      <div class="left">
        <div class="search-input">
          <i class="fa fa-search"></i>
          <input
              type="text"
              placeholder="Search by document no…"
              value={docFilters.filters.search}
              oninput={(e) => docFilters.setSearch((e.target as HTMLInputElement).value)}
          />
            {#if docFilters.filters.search}
            <button class="clear" type="button" onclick={() => docFilters.setSearch('')} aria-label="Clear search">
              <i class="fa fa-times"></i>
            </button>
          {/if}
        </div>

        <FilterDropdown
            label="Type"
            icon="fa-filter"
            options={docFilters.typeOptions}
            selected={docFilters.filters.typeFilter}
            onChange={docFilters.setTypeFilter}
        />

        <MonthRangeFilter value={docFilters.filters.dateRange} onChange={docFilters.setDateRange} {language} minYear={minDocYear}/>
      </div>

        {#if canPreviewAndGenerate}
        <div class="right">
          <button class="btn btn-default" onclick={generation.openPreview}>
            <i class="fa fa-fw fa-eye"></i> Preview
          </button>
          <button class="btn btn-primary" onclick={generation.openUpdate}>
            <i class="fa fa-fw fa-plus"></i> Generate
          </button>
        </div>
      {/if}
    </div>

      {#if docFilters.activeChips.length > 0}
      <div class="active-filters">
        <span>{t('Filtered:')}</span>
          {#each docFilters.activeChips as chip (chip.k)}
          <span class="afilter-chip">
            {chip.label}
              <button type="button" class="x" onclick={chip.onX} aria-label="Remove filter">
              <i class="fa fa-times"></i>
            </button>
          </span>
        {/each}
          <button type="button" class="afilter-clear" onclick={docFilters.handleClearFilters}>{t('Clear all')}</button>
      </div>
    {/if}

      <DocumentsTable
          docs={docFilters.pagedDocs}
          sort={docFilters.filters.sort}
          onSort={docFilters.handleSort}
          density="compact"
          busyIds={generation.busyRowIds}
          onRowAction={generation.handleRowAction}
          {language}
          {canPreviewAndGenerate}
      />

    <DocumentsPagination
        page={docFilters.filters.page}
        pageCount={docFilters.pageCount}
        totalFiltered={docFilters.filtered.length}
        totalBase={docFilters.totalDocs}
        pageSize={PAGE_SIZE}
        onPage={docFilters.setPage}
    />
  </div>
</div>

{#if generation.modal}
  <GenerateModal
      mode={generation.modal.kind}
      initial={generation.modal.initial}
      types={docFilters.availableTypes}
      existingMonths={docFilters.existingMonths}
      busy={!!generation.modal.busy}
      progress={generation.modal.progress ?? 0}
      onClose={generation.closeModal}
      onSubmit={generation.handleSubmit}
      {language}
      {purse}
  />
{/if}

{#if generation.pendingUpdate}
  <ConfirmReplaceModal
      doc={generation.pendingUpdate}
      onClose={generation.cancelUpdate}
      onConfirm={generation.applyUpdate}
      {language}
  />
{/if}

{#if generation.previewResult}
  <PreviewResultModal
      doc={generation.previewResult.doc}
      onClose={generation.closePreview}
      onApply={generation.previewResult.canSave ? generation.applyPreview : null}
      {language}
  />
{/if}

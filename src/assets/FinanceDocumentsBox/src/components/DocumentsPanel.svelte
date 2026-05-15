<script lang="ts">
  import type { AccountFilters, DateRange, Doc, FilterOption, SortState } from "../types";
  import { useI18n } from "../i18n";
  import FilterDropdown from "./FilterDropdown.svelte";
  import MonthRangeFilter from "./MonthRangeFilter.svelte";
  import DocumentsTable from "./DocumentsTable.svelte";
  import DocumentsPagination from "./DocumentsPagination.svelte";
  import { PAGE_SIZE } from "../composables/useDocumentFilters.svelte";

  let {
    pagedDocs,
    filters,
    typeOptions,
    pageCount,
    totalFiltered,
    totalBase,
    activeChips,
    language,
    minDocYear = new Date().getFullYear() - 3,
    busyIds = [],
    canPreviewAndGenerate = false,
    onRowAction = () => {},
    onSearch,
    onTypeFilter,
    onDateRange,
    onPage,
    onSort,
    onClearFilters,
    onOpenPreview,
    onOpenUpdate,
  }: {
    pagedDocs: Doc[];
    filters: AccountFilters;
    typeOptions: FilterOption[];
    pageCount: number;
    totalFiltered: number;
    totalBase: number;
    activeChips: Array<{ k: string; label: string; onX: () => void }>;
    language: string;
    minDocYear?: number;
    busyIds?: string[];
    canPreviewAndGenerate?: boolean;
    onRowAction?: (kind: string, doc: Doc) => void;
    onSearch: (v: string) => void;
    onTypeFilter: (v: string[]) => void;
    onDateRange: (v: DateRange) => void;
    onPage: (p: number) => void;
    onSort: (key: SortState["key"]) => void;
    onClearFilters: () => void;
    onOpenPreview?: () => void;
    onOpenUpdate?: () => void;
  } = $props();

  const t = useI18n();
</script>

<div class="purse-docs">
  <div class="docs-head">
    <div class="left">
      <div class="search-input">
        <i class="fa fa-search"></i>
        <input
            type="text"
            placeholder="Search by document no…"
            value={filters.search}
            oninput={(e) => onSearch((e.target as HTMLInputElement).value)}
        />
        {#if filters.search}
          <button class="clear" type="button" onclick={() => onSearch('')} aria-label="Clear search">
            <i class="fa fa-times"></i>
          </button>
        {/if}
      </div>

      <FilterDropdown
          label="Type"
          icon="fa-filter"
          options={typeOptions}
          selected={filters.typeFilter}
          onChange={onTypeFilter}
      />

      <MonthRangeFilter value={filters.dateRange} onChange={onDateRange} {language} minYear={minDocYear}/>
    </div>

    {#if canPreviewAndGenerate && (onOpenPreview || onOpenUpdate)}
      <div class="right">
        {#if onOpenPreview}
          <button class="btn btn-default" onclick={onOpenPreview}>
            <i class="fa fa-fw fa-eye"></i> Preview
          </button>
        {/if}
        {#if onOpenUpdate}
          <button class="btn btn-primary" onclick={onOpenUpdate}>
            <i class="fa fa-fw fa-plus"></i> Generate
          </button>
        {/if}
      </div>
    {/if}
  </div>

  {#if activeChips.length > 0}
    <div class="active-filters">
      <span>{t('Filtered:')}</span>
      {#each activeChips as chip (chip.k)}
        <span class="afilter-chip">
          {chip.label}
          <button type="button" class="x" onclick={chip.onX} aria-label="Remove filter">
            <i class="fa fa-times"></i>
          </button>
        </span>
      {/each}
      <button type="button" class="afilter-clear" onclick={onClearFilters}>{t('Clear all')}</button>
    </div>
  {/if}

  <DocumentsTable
      docs={pagedDocs}
      sort={filters.sort}
      onSort={onSort}
      busyIds={busyIds}
      onRowAction={onRowAction}
      {language}
      {canPreviewAndGenerate}
  />

  <DocumentsPagination
      page={filters.page}
      pageCount={pageCount}
      totalFiltered={totalFiltered}
      totalBase={totalBase}
      pageSize={PAGE_SIZE}
      onPage={onPage}
  />
</div>

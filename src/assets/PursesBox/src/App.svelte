<script lang="ts">
  import { untrack } from "svelte";
  import type { AccountFilters, DateRange, Doc, FilterOption, ModalState, Purse, PursesBoxProps, SortState, ToastState } from "./types";
  import { defaultFilters, docMonthKey, docTypeColor, docTypesFromDocs, fmtMonthKey, typeMeta } from "./data";
  import { initI18n } from "./i18n";
  import { permissions } from "./permissions";
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

  $effect(() => {
      initI18n(language);
      permissions.init(permKeys);
  });

  const PAGE_SIZE = 10;

  // State is scoped per purse so tab switching preserves filters/sort/page.
  const seedPurses = untrack(() => initialPurses.length ? initialPurses : []);
  let activeId = $state<string>(seedPurses[0].id);
  let purses = $state<Purse[]>([...seedPurses]);
  let docsByPurse = $state<Record<string, Doc[]>>(
      Object.fromEntries(seedPurses.map(purse => [purse.id, [...purse.documents]])),
  );
  let filtersByPurse = $state<Record<string, AccountFilters>>(
      Object.fromEntries(seedPurses.map(p => [p.id, defaultFilters()])),
  );

  let modal = $state<ModalState | null>(null);
  let confirmReplace = $state<Doc | null>(null);
  let previewResult = $state<{ doc: Doc; canSave: boolean } | null>(null);
  let busyRowIds = $state<string[]>([]);
  let toast = $state<ToastState | null>(null);
  let toastTimeout: ReturnType<typeof setTimeout> | undefined;

  $effect(() => () => clearTimeout(toastTimeout));

  let purse = $derived(purses.find(p => p.id === activeId)!);
  let baseDocs = $derived(docsByPurse[activeId] ?? []);
  let filters = $derived(filtersByPurse[activeId] ?? defaultFilters());

  let availableTypes = $derived(docTypesFromDocs(baseDocs));

  let typeCountMap = $derived(
      baseDocs.reduce<Record<string, number>>((m, d) => {
          m[d.type] = (m[d.type] ?? 0) + 1;
          return m;
      }, {}),
  );
  let typeOptions = $derived<FilterOption[]>(
      availableTypes.map(t => ({ id: t.id, label: t.label, dot: docTypeColor(t.id), count: typeCountMap[t.id] ?? 0 })),
  );

  let filtered = $derived.by(() => {
      const { search, typeFilter, dateRange, sort } = filters;
      let rows = baseDocs.filter(d => {
          if (typeFilter.length > 0 && !typeFilter.includes(d.type)) return false;
          const key = docMonthKey(d.date);
          if (dateRange.from && key < dateRange.from) return false;
          if (dateRange.to && key > dateRange.to) return false;
          if (search && !d.number.toLowerCase().includes(search.toLowerCase())) return false;
          return true;
      });
      rows.sort((a, b) => {
          const k = sort.key;
          const av = k === "type" ? a.type_label : a[k];
          const bv = k === "type" ? b.type_label : b[k];
          if (av < bv) return sort.dir === "asc" ? -1 : 1;
          if (av > bv) return sort.dir === "asc" ? 1 : -1;
          return 0;
      });
      return rows;
  });

  let pageCount = $derived(Math.max(1, Math.ceil(filtered.length / PAGE_SIZE)));
  let pagedDocs = $derived(filtered.slice((filters.page - 1) * PAGE_SIZE, filters.page * PAGE_SIZE));

  let existingMonths = $derived.by(() => {
      const m: Record<string, string[]> = {};
      baseDocs.forEach(d => {
          const key = docMonthKey(d.date);
          if (!m[d.type]) m[d.type] = [];
          if (!m[d.type].includes(key)) m[d.type].push(key);
      });
      return m;
  });

  let activeChips = $derived.by(() => {
      const chips: Array<{ k: string; label: string; onX: () => void }> = [];
      filters.typeFilter.forEach(t => chips.push({
          k: `t-${t}`,
          label: typeOptions.find(type => type.id === t)?.label ?? t,
          onX: () => setTypeFilter(filters.typeFilter.filter(x => x !== t)),
      }));
      const { from, to } = filters.dateRange;
      if (from || to) {
          const lbl = from && to
              ? (from === to ? fmtMonthKey(from) : `${fmtMonthKey(from)} – ${fmtMonthKey(to)}`)
              : from ? `From ${fmtMonthKey(from)}` : `Until ${fmtMonthKey(to!)}`;
          chips.push({ k: "date", label: lbl, onX: () => setDateRange({ from: null, to: null }) });
      }
      if (filters.search) {
          chips.push({ k: "search", label: `"${filters.search}"`, onX: () => setSearch("") });
      }
      return chips;
  });

  function updateFilters(patch: Partial<AccountFilters>) {
      const current = filtersByPurse[activeId] ?? defaultFilters();
      // Reset page whenever a filter other than page itself changes
      const resetPage = !("page" in patch);
      filtersByPurse[activeId] = { ...current, ...patch, ...(resetPage ? { page: 1 } : {}) };
  }

  function setSearch(v: string) {
      updateFilters({ search: v });
  }

  function setTypeFilter(v: string[]) {
      updateFilters({ typeFilter: v });
  }

  function setDateRange(v: DateRange) {
      updateFilters({ dateRange: v });
  }

  function setPage(p: number) {
      updateFilters({ page: p });
  }

  function handleSort(key: SortState["key"]) {
      const s = filters.sort;
      const dir: SortState["dir"] = s.key === key
          ? (s.dir === "asc" ? "desc" : "asc")
          : (key === "date" ? "desc" : "asc");
      updateFilters({ sort: { key, dir } });
  }

  function handleClearFilters() {
      filtersByPurse[activeId] = defaultFilters();
  }

  function showToast(msg: string, icon = "fa-check-circle") {
      clearTimeout(toastTimeout);
      toast = { msg, icon };
      toastTimeout = setTimeout(() => {
          toast = null;
      }, 2600);
  }

  function runGeneration({ durationMs = 2400, onProgress, onDone }: {
      durationMs?: number;
      onProgress: (p: number) => void;
      onDone: () => void;
  }) {
      const start = Date.now();
      const tick = () => {
          const p = Math.min(100, Math.round(((Date.now() - start) / durationMs) * 100));
          onProgress(p);
          if (p < 100) setTimeout(tick, 80);
          else onDone();
      };
      tick();
  }

  function excludeDocForMonth(docs: Doc[], type: string, monthKey: string): Doc[] {
      return docs.filter(d => d.type !== type || docMonthKey(d.date) !== monthKey);
  }

  function handleSubmitGenerate({ type, month, willReplace, mode }: {
      type: string; month: string; willReplace: boolean; mode: string;
  }) {
      if (!modal) return;
      modal = { ...modal, busy: true, progress: 0 };

      runGeneration({
          onProgress: p => {
              if (modal) modal = { ...modal, progress: p };
          },
          onDone: () => {
              const tm = typeMeta(type);
              const [yr, mo] = month.split("-");
              const monthLabel = fmtMonthKey(month);
              const newDoc: Doc = {
                  id: `gen-${Date.now()}`,
                  type,
                  type_label: tm.label,
                  filename: `${tm.label} ${monthLabel}`,
                  number: `${type.slice(0, 3).toUpperCase()}-${yr}-${String(Math.floor(Math.random() * 900) + 100)}`,
                  date: `${yr}-${mo}-15`,
                  isNew: true,
              };

              if (mode === "generate" || mode === "update-replace") {
                  docsByPurse[activeId] = [newDoc, ...excludeDocForMonth(docsByPurse[activeId] ?? [], type, month)];
                  modal = null;
                  showToast(willReplace ? `Document replaced — ${newDoc.number}` : `Document generated — ${newDoc.number}`);
              } else {
                  modal = null;
                  previewResult = { doc: newDoc, canSave: mode === "preview-updated" };
              }
          },
      });
  }

  function handleRowAction(kind: string, doc: Doc) {
      if (kind === "download") {
          showToast(`Downloading ${doc.number}…`, "fa-download");
          return;
      }
      if (kind === "view") {
          showToast(`Opening ${doc.number}`, "fa-eye");
          return;
      }

      const initial = { type: doc.type, month: docMonthKey(doc.date) };

      if (kind === "update-replace") {
          confirmReplace = doc;
          return;
      }
      if (kind === "preview-updated") {
          modal = { kind: "preview-updated", initial, doc };
      }
  }

  function confirmReplaceNow() {
      const doc = confirmReplace!;
      const monthKey = docMonthKey(doc.date);
      confirmReplace = null;

      busyRowIds = [...busyRowIds, doc.id];
      runGeneration({
          onProgress: () => {
          },
          onDone: () => {
              const tm = typeMeta(doc.type);
              const newDoc: Doc = {
                  ...doc,
                  id: `gen-${Date.now()}`,
                  number: `${doc.type.slice(0, 3).toUpperCase()}-${monthKey.replace("-", "")}-${String(Math.floor(Math.random() * 900) + 100)}`,
                  isNew: true,
              };
              const list = docsByPurse[activeId] ?? [];
              docsByPurse[activeId] = list.map(d => d.id === doc.id ? newDoc : d);
              busyRowIds = busyRowIds.filter(x => x !== doc.id);
              showToast(`${tm.label} replaced — ${newDoc.number}`, "fa-refresh");
          },
      });
  }

  function handleSettingChange(field: string, value: string) {
      purses = purses.map(p => {
          if (p.id !== activeId) return p;
          if (field === "contact") return { ...p, contact: { ...p.contact, name: value } };
          return { ...p, [field]: value };
      });
      showToast(`Updated ${field === "requisite" ? "requisite" : "contact"}`);
  }

  function handleRecharge() {
      showToast(`Opening top-up flow for ${purse.id}`, "fa-plus-circle");
  }

  function handleKpiClick(which: string) {
      showToast(`Opened ${which} history`, "fa-line-chart");
  }

  function savePreviewResult() {
      if (!previewResult) return;
      const doc = previewResult.doc;
      const monthKey = docMonthKey(doc.date);
      docsByPurse[activeId] = [doc, ...excludeDocForMonth(docsByPurse[activeId] ?? [], doc.type, monthKey)];
      previewResult = null;
      showToast(`Preview saved — ${doc.number}`);
  }
</script>

<div class="accounts-block nav-tabs-custom">

  <PurseTabs
      {purses}
      {activeId}
      onChange={(id) => (activeId = id)}
  />

  <PurseSummary
      {purse}
      onRecharge={handleRecharge}
      onKpiClick={handleKpiClick}
  />

  <PurseSettings
      {purse}
      onChange={handleSettingChange}
  />

  <div class="purse-docs">
    <div class="docs-head">
      <div class="left">
        <div class="search-input">
          <i class="fa fa-search"></i>
          <input
              type="text"
              placeholder="Search by document no…"
              value={filters.search}
              oninput={(e) => setSearch((e.target as HTMLInputElement).value)}
          />
            {#if filters.search}
            <button class="clear" type="button" onclick={() => setSearch('')} aria-label="Clear search"><i class="fa fa-times"></i></button>
          {/if}
        </div>

        <FilterDropdown
            label="Type"
            icon="fa-filter"
            options={typeOptions}
            selected={filters.typeFilter}
            onChange={setTypeFilter}
        />

        <MonthRangeFilter
            value={filters.dateRange}
            onChange={setDateRange}
        />
      </div>

      <div class="right">
        <button class="btn btn-default" onclick={() => (modal = { kind: 'preview' })}>
          <i class="fa fa-fw fa-eye"></i> Preview
        </button>
        <button class="btn btn-primary" onclick={() => (modal = { kind: 'generate' })}>
          <i class="fa fa-fw fa-plus"></i> Generate
        </button>
      </div>
    </div>

      {#if activeChips.length > 0}
      <div class="active-filters">
        <span>Filtered:</span>
          {#each activeChips as chip (chip.k)}
          <span class="afilter-chip">
            {chip.label}
              <button type="button" class="x" onclick={chip.onX} aria-label="Remove filter"><i class="fa fa-times"></i></button>
          </span>
        {/each}
          <button type="button" class="afilter-clear" onclick={handleClearFilters}>Clear all</button>
      </div>
    {/if}

      <DocumentsTable
          docs={pagedDocs}
          sort={filters.sort}
          onSort={handleSort}
          density="compact"
          busyIds={busyRowIds}
          onRowAction={handleRowAction}
      />

    <DocumentsPagination
        page={filters.page}
        {pageCount}
        totalFiltered={filtered.length}
        totalBase={baseDocs.length}
        pageSize={PAGE_SIZE}
        onPage={setPage}
    />
  </div>
</div>

{#if modal}
  <GenerateModal
      mode={modal.kind}
      initial={modal.initial}
      types={availableTypes}
      {existingMonths}
      busy={!!modal.busy}
      progress={modal.progress ?? 0}
      onClose={() => { if (!modal?.busy) modal = null; }}
      onSubmit={handleSubmitGenerate}
  />
{/if}

{#if confirmReplace}
  <ConfirmReplaceModal
      doc={confirmReplace}
      onClose={() => (confirmReplace = null)}
      onConfirm={confirmReplaceNow}
  />
{/if}

{#if previewResult}
  <PreviewResultModal
      doc={previewResult.doc}
      onClose={() => (previewResult = null)}
      onSave={previewResult.canSave ? savePreviewResult : null}
  />
{/if}

{#if toast}
  <div class="toast">
    <i class="fa {toast.icon}"></i>
      {toast.msg}
  </div>
{/if}

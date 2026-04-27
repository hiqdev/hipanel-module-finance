<script lang="ts">
  import type { Account, AccountFilters, DateRange, Doc, FilterOption, ModalState, SortState, ToastState } from "./types";
  import { ACCOUNTS, defaultFilters, DOC_TYPES, DOCS, fmtMonthKey, MONTH_NAMES, typeMeta } from "./data";
  import AccountTabs from "./components/AccountTabs.svelte";
  import AccountSummary from "./components/AccountSummary.svelte";
  import AccountSettings from "./components/AccountSettings.svelte";
  import FilterDropdown from "./components/FilterDropdown.svelte";
  import MonthRangeFilter from "./components/MonthRangeFilter.svelte";
  import DocumentsTable from "./components/DocumentsTable.svelte";
  import DocumentsPagination from "./components/DocumentsPagination.svelte";
  import GenerateModal from "./components/GenerateModal.svelte";
  import PreviewResultModal from "./components/PreviewResultModal.svelte";
  import ConfirmReplaceModal from "./components/ConfirmReplaceModal.svelte";

  // Optional data prop for real backend integration
  let { data = {} }: { data?: Record<string, any> } = $props();

  const PAGE_SIZE = 8;

  // ── Per-account state ─────────────────────────────────────────────────────
  // State is scoped per account so tab switching preserves filters/sort/page.
  let activeId = $state<string>(localStorage.getItem("acct.active") ?? ACCOUNTS[0].id);
  let accounts = $state<Account[]>([...ACCOUNTS]);
  let docsByAcct = $state<Record<string, Doc[]>>(
      Object.fromEntries(Object.entries(DOCS).map(([k, v]) => [k, [...v]])),
  );
  let filtersByAcct = $state<Record<string, AccountFilters>>(
      Object.fromEntries(ACCOUNTS.map(a => [a.id, defaultFilters()])),
  );

  // ── Global UI state ───────────────────────────────────────────────────────
  let modal = $state<ModalState | null>(null);
  let confirmReplace = $state<Doc | null>(null);
  let previewResult = $state<{ doc: Doc; canSave: boolean } | null>(null);
  let busyRowIds = $state<string[]>([]);
  let toast = $state<ToastState | null>(null);
  let toastTimeout: ReturnType<typeof setTimeout> | undefined;

  // ── Persist active tab ────────────────────────────────────────────────────
  $effect(() => {
      localStorage.setItem("acct.active", activeId);
  });

  // ── Current account + docs ────────────────────────────────────────────────
  let account = $derived(accounts.find(a => a.id === activeId)!);
  let baseDocs = $derived(docsByAcct[activeId] ?? []);
  let filters = $derived(filtersByAcct[activeId] ?? defaultFilters());

  // ── Type options — only types present in this account's docs ──────────────
  const TYPE_COLORS: Record<string, string> = {
      invoice: "#3c8dbc", service: "#39CCCC", payment: "#f39c12", installment: "#605ca8",
  };

  let availableTypes = $derived.by(() => {
      const set = new Set(baseDocs.map(d => d.type));
      return DOC_TYPES.filter(t => set.has(t.id));
  });

  let typeOptions = $derived<FilterOption[]>(availableTypes.map(t => ({
      id: t.id,
      label: t.label,
      dot: TYPE_COLORS[t.id],
      count: baseDocs.filter(d => d.type === t.id).length,
  })));

  // ── Filtered + sorted docs ────────────────────────────────────────────────
  let filtered = $derived.by(() => {
      const { search, typeFilter, dateRange, sort } = filters;
      let rows = baseDocs.filter(d => {
          if (typeFilter.length > 0 && !typeFilter.includes(d.type)) return false;
          const dt = new Date(d.date);
          const key = `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}`;
          if (dateRange.from && key < dateRange.from) return false;
          if (dateRange.to && key > dateRange.to) return false;
          if (search && !d.ref.toLowerCase().includes(search.toLowerCase())) return false;
          return true;
      });
      rows.sort((a, b) => {
          const k = sort.key;
          const av: string = k === "type" ? typeMeta(a.type).label : (a as any)[k];
          const bv: string = k === "type" ? typeMeta(b.type).label : (b as any)[k];
          if (av < bv) return sort.dir === "asc" ? -1 : 1;
          if (av > bv) return sort.dir === "asc" ? 1 : -1;
          return 0;
      });
      return rows;
  });

  let pageCount = $derived(Math.max(1, Math.ceil(filtered.length / PAGE_SIZE)));
  let pagedDocs = $derived(filtered.slice((filters.page - 1) * PAGE_SIZE, filters.page * PAGE_SIZE));

  // ── Existing month map for the modal ──────────────────────────────────────
  let existingMonths = $derived.by(() => {
      const m: Record<string, string[]> = {};
      baseDocs.forEach(d => {
          const dt = new Date(d.date);
          const key = `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}`;
          if (!m[d.type]) m[d.type] = [];
          if (!m[d.type].includes(key)) m[d.type].push(key);
      });
      return m;
  });

  // ── Active filter chips ───────────────────────────────────────────────────
  let activeChips = $derived.by(() => {
      const chips: Array<{ k: string; label: string; onX: () => void }> = [];
      filters.typeFilter.forEach(t => chips.push({
          k: `t-${t}`,
          label: typeMeta(t)?.label ?? t,
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

  // ── Per-account filter setters ────────────────────────────────────────────
  function updateFilters(patch: Partial<AccountFilters>) {
      const current = filtersByAcct[activeId] ?? defaultFilters();
      // Reset page whenever a filter other than page itself changes
      const resetPage = !("page" in patch);
      filtersByAcct[activeId] = { ...current, ...patch, ...(resetPage ? { page: 1 } : {}) };
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
      filtersByAcct[activeId] = defaultFilters();
  }

  // ── Toast helper ──────────────────────────────────────────────────────────
  function showToast(msg: string, icon = "fa-check-circle") {
      clearTimeout(toastTimeout);
      toast = { msg, icon };
      toastTimeout = setTimeout(() => {
          toast = null;
      }, 2600);
  }

  // ── Simulated generation progress ─────────────────────────────────────────
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

  // ── Modal submit ──────────────────────────────────────────────────────────
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
              const monthLabel = `${MONTH_NAMES[parseInt(mo, 10) - 1]} ${yr}`;
              const newDoc: Doc = {
                  id: `gen-${Date.now()}`,
                  type,
                  name: `${tm.label} ${monthLabel}`,
                  ref: `${type.slice(0, 3).toUpperCase()}-${yr}-${String(Math.floor(Math.random() * 900) + 100)}`,
                  date: `${yr}-${mo}-15`,
                  isNew: true,
              };

              if (mode === "generate" || mode === "update-replace") {
                  const list = docsByAcct[activeId] ?? [];
                  const kept = list.filter(d => {
                      if (d.type !== type) return true;
                      const dt = new Date(d.date);
                      const key = `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}`;
                      return key !== month;
                  });
                  docsByAcct[activeId] = [newDoc, ...kept];
                  modal = null;
                  showToast(willReplace ? `Document replaced — ${newDoc.ref}` : `Document generated — ${newDoc.ref}`);
              } else {
                  modal = null;
                  previewResult = { doc: newDoc, canSave: mode === "preview-updated" };
              }
          },
      });
  }

  // ── Row actions ───────────────────────────────────────────────────────────
  function handleRowAction(kind: string, doc: Doc) {
      if (kind === "download") {
          showToast(`Downloading ${doc.ref}…`, "fa-download");
          return;
      }
      if (kind === "view") {
          showToast(`Opening ${doc.ref}`, "fa-eye");
          return;
      }

      const dt = new Date(doc.date);
      const monthKey = `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}`;
      const initial = { type: doc.type, month: monthKey };

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
      const dt = new Date(doc.date);
      const monthKey = `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}`;
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
                  ref: `${doc.type.slice(0, 3).toUpperCase()}-${monthKey.replace("-", "")}-${String(Math.floor(Math.random() * 900) + 100)}`,
                  isNew: true,
              };
              const list = docsByAcct[activeId] ?? [];
              docsByAcct[activeId] = list.map(d => d.id === doc.id ? newDoc : d);
              busyRowIds = busyRowIds.filter(x => x !== doc.id);
              showToast(`${tm.label} replaced — ${newDoc.ref}`, "fa-refresh");
          },
      });
  }

  // ── Settings handlers ─────────────────────────────────────────────────────
  function handleSettingChange(field: string, value: string) {
      accounts = accounts.map(a => {
          if (a.id !== activeId) return a;
          if (field === "contact") return { ...a, contact: { ...a.contact, name: value } };
          return { ...a, [field]: value };
      });
      showToast(`Updated ${field === "paymentDetails" ? "payment details" : "contact"}`);
  }

  function handleRecharge() {
      showToast(`Opening top-up flow for ${account.title}`, "fa-plus-circle");
  }

  function handleKpiClick(which: string) {
      showToast(`Opened ${which} history`, "fa-line-chart");
  }

  // ── Save previewed doc ────────────────────────────────────────────────────
  function savePreviewResult() {
      if (!previewResult) return;
      const doc = previewResult.doc;
      const dt = new Date(doc.date);
      const monthKey = `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}`;
      const list = docsByAcct[activeId] ?? [];
      const kept = list.filter(d => {
          if (d.type !== doc.type) return true;
          const ddt = new Date(d.date);
          return `${ddt.getFullYear()}-${String(ddt.getMonth() + 1).padStart(2, "0")}` !== monthKey;
      });
      docsByAcct[activeId] = [doc, ...kept];
      previewResult = null;
      showToast(`Preview saved — ${doc.ref}`);
  }
</script>

<div class="accounts-block nav-tabs-custom">

  <AccountTabs
      {accounts}
      {activeId}
      onChange={(id) => (activeId = id)}
  />

  <AccountSummary
      {account}
      onRecharge={handleRecharge}
      onKpiClick={handleKpiClick}
  />

  <AccountSettings
      {account}
      onChange={handleSettingChange}
  />

  <div class="acct-docs">
    <!-- Toolbar: search + filters + actions -->
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
          <i class="fa fa-eye"></i> Preview
        </button>
        <button class="btn btn-primary" onclick={() => (modal = { kind: 'generate' })}>
          <i class="fa fa-plus"></i> Generate
        </button>
      </div>
    </div>

      <!-- Active filter chips -->
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

      <!-- Documents table -->
    <DocumentsTable
        docs={pagedDocs}
        sort={filters.sort}
        onSort={handleSort}
        density="compact"
        busyIds={busyRowIds}
        onRowAction={handleRowAction}
    />

      <!-- Footer: count + pagination -->
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

<!-- Modals -->
{#if modal}
  <GenerateModal
      mode={modal.kind}
      initial={modal.initial}
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

<!-- Toast notification -->
{#if toast}
  <div class="toast">
    <i class="fa {toast.icon}"></i>
      {toast.msg}
  </div>
{/if}

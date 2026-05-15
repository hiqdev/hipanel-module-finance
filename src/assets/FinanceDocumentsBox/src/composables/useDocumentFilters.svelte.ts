import type { AccountFilters, DateRange, Doc, DocType, FilterOption, SortState } from "../types";
import { defaultFilters, docMonthKey, docTypeColor, docTypesFromDocs, fmtMonthKey } from "../data";

export const PAGE_SIZE = 25;

export function useDocumentFilters(
  getBaseDocs: () => Doc[],
  getActiveId: () => string,
  getLocale: () => string,
) {
  let filtersByPurse = $state<Record<string, AccountFilters>>({});

  let baseDocs = $derived(getBaseDocs());
  let activeId = $derived(getActiveId());
  let locale = $derived(getLocale());
  let filters = $derived(filtersByPurse[activeId] ?? defaultFilters());

  let availableTypes: DocType[] = $derived(docTypesFromDocs(baseDocs));

  let typeCountMap = $derived(
    baseDocs.reduce<Record<string, number>>((m, d) => {
      m[d.type] = (m[d.type] ?? 0) + 1;
      return m;
    }, {}),
  );

  let typeOptions: FilterOption[] = $derived(
    availableTypes.map(t => ({ id: t.id, label: t.label, dot: docTypeColor(t.id), count: typeCountMap[t.id] ?? 0 })),
  );

  let filtered: Doc[] = $derived.by(() => {
    const { search, typeFilter, dateRange, sort } = filters;
    const pinnedNew = baseDocs.filter(d => d.isNew);
    const pinnedNewIds = new Set(pinnedNew.map(d => d.id));
    const rows = baseDocs.filter(d => {
      if (pinnedNewIds.has(d.id)) return false;
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
    return [...pinnedNew, ...rows];
  });

  let pageCount = $derived(Math.max(1, Math.ceil(filtered.length / PAGE_SIZE)));
  let pagedDocs: Doc[] = $derived(filtered.slice((filters.page - 1) * PAGE_SIZE, filters.page * PAGE_SIZE));

  let existingMonths: Record<string, string[]> = $derived.by(() => {
    const m: Record<string, string[]> = {};
    baseDocs.forEach(d => {
      const key = docMonthKey(d.date);
      if (!m[d.type]) m[d.type] = [];
      if (!m[d.type].includes(key)) m[d.type].push(key);
    });
    return m;
  });

  let activeChips: Array<{ k: string; label: string; onX: () => void }> = $derived.by(() => {
    const chips: Array<{ k: string; label: string; onX: () => void }> = [];
    filters.typeFilter.forEach(t => chips.push({
      k: `t-${t}`,
      label: typeOptions.find(opt => opt.id === t)?.label ?? t,
      onX: () => setTypeFilter(filters.typeFilter.filter(x => x !== t)),
    }));
    const { from, to } = filters.dateRange;
    if (from || to) {
      const lbl = from && to
        ? (from === to ? fmtMonthKey(from, locale) : `${fmtMonthKey(from, locale)} – ${fmtMonthKey(to, locale)}`)
        : from ? `From ${fmtMonthKey(from, locale)}` : `Until ${fmtMonthKey(to!, locale)}`;
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

  return {
    get filters() {
      return filters;
    },
    get typeOptions() {
      return typeOptions;
    },
    get availableTypes() {
      return availableTypes;
    },
    get filtered() {
      return filtered;
    },
    get pageCount() {
      return pageCount;
    },
    get pagedDocs() {
      return pagedDocs;
    },
    get totalDocs() {
      return baseDocs.length;
    },
    get existingMonths() {
      return existingMonths;
    },
    get activeChips() {
      return activeChips;
    },
    setSearch, setTypeFilter, setDateRange, setPage,
    handleSort, handleClearFilters,
  };
}

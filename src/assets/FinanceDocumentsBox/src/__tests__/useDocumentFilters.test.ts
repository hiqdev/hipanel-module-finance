import { describe, expect, it } from "vitest";
import { PAGE_SIZE, useDocumentFilters } from "../composables/useDocumentFilters.svelte";
import type { Doc } from "../types";

function makeDoc(overrides: Partial<Doc> & { type: string; number: string; date: string }): Doc {
  return {
    filename: "",
    file_id: "",
    isNew: false,
    ...overrides,
    id: overrides.number,
    type_label: overrides.type_label ?? overrides.type,
  };
}

const contractA = makeDoc({ type: "contract", number: "CNT-001", date: "2024-01-10" });
const contractB = makeDoc({ type: "contract", number: "CNT-002", date: "2024-02-05" });
const invoice = makeDoc({ type: "invoice", number: "INV-001", date: "2024-03-15" });

function makeHook(docs: Doc[] = [contractA, contractB, invoice]) {
  return useDocumentFilters(() => docs, () => "purse-1", () => "en");
}

describe("useDocumentFilters — no filters", () => {
  it("returns all docs when no filter is active", () => {
    const hook = makeHook();
    expect(hook.filtered).toHaveLength(3);
  });
});

describe("useDocumentFilters — type filter", () => {
  it("filters to a single type", () => {
    const hook = makeHook();
    hook.setTypeFilter(["contract"]);
    expect(hook.filtered).toHaveLength(2);
    expect(hook.filtered.every(d => d.type === "contract")).toBe(true);
  });

  it("shows all docs when filter is cleared", () => {
    const hook = makeHook();
    hook.setTypeFilter(["invoice"]);
    hook.setTypeFilter([]);
    expect(hook.filtered).toHaveLength(3);
  });
});

describe("useDocumentFilters — search", () => {
  it("matches by document number (case-insensitive)", () => {
    const hook = makeHook();
    hook.setSearch("inv-001");
    expect(hook.filtered).toHaveLength(1);
    expect(hook.filtered[0].number).toBe("INV-001");
  });

  it("returns empty when no number matches", () => {
    const hook = makeHook();
    hook.setSearch("NOMATCH");
    expect(hook.filtered).toHaveLength(0);
  });
});

describe("useDocumentFilters — date range", () => {
  it("excludes docs outside the range", () => {
    const hook = makeHook();
    hook.setDateRange({ from: "2024-02", to: "2024-02" });
    expect(hook.filtered).toHaveLength(1);
    expect(hook.filtered[0].number).toBe("CNT-002");
  });
});

describe("useDocumentFilters — sorting", () => {
  it("toggles sort direction on the same key", () => {
    const hook = makeHook();
    hook.handleSort("number");
    expect(hook.filters.sort).toEqual({ key: "number", dir: "asc" });
    hook.handleSort("number");
    expect(hook.filters.sort).toEqual({ key: "number", dir: "desc" });
  });

  it("defaults to desc when switching to date key", () => {
    const hook = makeHook();
    hook.handleSort("type");
    hook.handleSort("date");
    expect(hook.filters.sort).toEqual({ key: "date", dir: "desc" });
  });
});

describe("useDocumentFilters — pagination", () => {
  it("resets page to 1 when a filter changes", () => {
    const hook = makeHook();
    hook.setPage(3);
    expect(hook.filters.page).toBe(3);
    hook.setTypeFilter(["contract"]);
    expect(hook.filters.page).toBe(1);
  });

  it("pageCount is 1 for small doc list", () => {
    expect(makeHook().pageCount).toBe(1);
  });

  it("pageCount reflects total when docs exceed PAGE_SIZE", () => {
    const many = Array.from({ length: PAGE_SIZE + 1 }, (_, i) =>
      makeDoc({ type: "invoice", number: `INV-${i}`, date: "2024-01-01" }),
    );
    const hook = makeHook(many);
    expect(hook.pageCount).toBe(2);
  });
});

describe("useDocumentFilters — clearFilters", () => {
  it("resets all filters to defaults", () => {
    const hook = makeHook();
    hook.setSearch("x");
    hook.setTypeFilter(["contract"]);
    hook.handleClearFilters();
    expect(hook.filters.search).toBe("");
    expect(hook.filters.typeFilter).toEqual([]);
    expect(hook.filtered).toHaveLength(3);
  });
});

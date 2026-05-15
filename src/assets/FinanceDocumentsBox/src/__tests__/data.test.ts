import { describe, it, expect } from "vitest";
import {
  docMonthKey,
  defaultFilters,
  docTypesFromDocs,
  docTypeColor,
  monthOptionsBetween,
  monthsForYear,
  fmtDate,
  fmtMoney,
} from "../data";
import type { Doc } from "../types";

describe("docMonthKey", () => {
  it("extracts year-month from a mid-month date", () => {
    expect(docMonthKey("2024-03-15")).toBe("2024-03");
  });

  it("handles first day of month", () => {
    expect(docMonthKey("2024-01-01")).toBe("2024-01");
  });

  it("handles last day of month", () => {
    expect(docMonthKey("2024-01-31")).toBe("2024-01");
  });

  it("handles December", () => {
    expect(docMonthKey("2024-12-25")).toBe("2024-12");
  });
});

describe("defaultFilters", () => {
  it("returns a complete filter object with expected defaults", () => {
    const f = defaultFilters();
    expect(f.search).toBe("");
    expect(f.typeFilter).toEqual([]);
    expect(f.dateRange).toEqual({ from: null, to: null });
    expect(f.sort).toEqual({ key: "date", dir: "desc" });
    expect(f.page).toBe(1);
  });

  it("returns a fresh object each call", () => {
    const a = defaultFilters();
    const b = defaultFilters();
    expect(a).not.toBe(b);
  });
});

describe("docTypesFromDocs", () => {
  it("returns empty array for no docs", () => {
    expect(docTypesFromDocs([])).toEqual([]);
  });

  it("extracts unique types preserving first label", () => {
    const docs = [
      { type: "contract", type_label: "Contract" },
      { type: "invoice", type_label: "Invoice" },
      { type: "contract", type_label: "Contract (dup)" },
    ] as Doc[];
    const result = docTypesFromDocs(docs);
    expect(result).toHaveLength(2);
    expect(result[0]).toEqual({ id: "contract", label: "Contract" });
    expect(result[1]).toEqual({ id: "invoice", label: "Invoice" });
  });
});

describe("docTypeColor", () => {
  it("returns a hsl() string", () => {
    expect(docTypeColor("contract")).toMatch(/^hsl\(\d+ \d+% \d+%\)$/);
  });

  it("returns the same color for the same input", () => {
    expect(docTypeColor("invoice")).toBe(docTypeColor("invoice"));
  });

  it("returns different colors for different inputs", () => {
    expect(docTypeColor("contract")).not.toBe(docTypeColor("invoice"));
  });
});

describe("monthOptionsBetween", () => {
  it("returns months from start to end inclusive", () => {
    const result = monthOptionsBetween("2024-01", "2024-03", "en");
    expect(result).toHaveLength(3);
    expect(result[0].key).toBe("2024-01");
    expect(result[1].key).toBe("2024-02");
    expect(result[2].key).toBe("2024-03");
  });

  it("handles single-month range", () => {
    const result = monthOptionsBetween("2024-06", "2024-06", "en");
    expect(result).toHaveLength(1);
    expect(result[0].key).toBe("2024-06");
  });

  it("crosses year boundary correctly", () => {
    const result = monthOptionsBetween("2023-11", "2024-02", "en");
    expect(result).toHaveLength(4);
    expect(result[0].key).toBe("2023-11");
    expect(result[3].key).toBe("2024-02");
  });
});

describe("monthsForYear", () => {
  it("returns exactly 12 months", () => {
    expect(monthsForYear(2024, "en")).toHaveLength(12);
  });

  it("generates keys from 01 to 12", () => {
    const months = monthsForYear(2024, "en");
    expect(months[0].key).toBe("2024-01");
    expect(months[11].key).toBe("2024-12");
  });
});

describe("fmtDate", () => {
  it("extracts the year correctly", () => {
    expect(fmtDate("2024-03-15", "en").year).toBe(2024);
  });

  it("returns a non-empty short string", () => {
    expect(fmtDate("2024-03-15", "en").short.length).toBeGreaterThan(0);
  });
});

describe("fmtMoney", () => {
  it("formats standard ISO 4217 currency", () => {
    expect(fmtMoney(100, "usd", "en")).toContain("100");
  });

  it("falls back for crypto/non-ISO currency without throwing", () => {
    expect(() => fmtMoney(50, "usdt", "en")).not.toThrow();
    const result = fmtMoney(50, "usdt", "en");
    expect(result).toContain("USDT");
    expect(result).toContain("50");
  });

  it("fallback includes the currency code uppercased", () => {
    const result = fmtMoney(0, "btc", "en");
    expect(result).toContain("BTC");
  });
});

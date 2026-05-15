import type { AccountFilters, Doc, DocType, MonthOption } from "./types";

export type MonthFormat = "long" | "short" | "narrow";

function parseLocalDate(date: string): Date {
  const match = /^(\d{4})-(\d{2})-(\d{2})/.exec(date);
  if (!match) return new Date(date);

  const [, year, month, day] = match;
  return new Date(Number(year), Number(month) - 1, Number(day));
}

function monthDateFromKey(key: string): Date {
  const [year, month] = key.split("-").map(Number);
  return new Date(year, month - 1, 1);
}

export function formatMonth(
  date: Date,
  locale: string,
  format: MonthFormat = "short",
): string {
  return new Intl.DateTimeFormat(locale, { month: format }).format(date);
}

export function docType(id: string): DocType {
  return { id, label: id };
}

export function docTypesFromDocs(docs: Doc[]): DocType[] {
  const seen = new Set<string>();
  const types: DocType[] = [];

  docs.forEach(doc => {
    if (seen.has(doc.type)) return;
    seen.add(doc.type);
    types.push({ id: doc.type, label: doc.type_label });
  });

  return types;
}

export function docTypeColor(id: string): string {
  let hash = 0;

  for (let i = 0; i < id.length; i++) {
    hash = id.charCodeAt(i) + ((hash << 5) - hash);
    hash |= 0;
  }

  const hue = Math.abs(hash) % 360;
  const saturation = 58 + (Math.abs(hash >> 8) % 18);
  const lightness = 42 + (Math.abs(hash >> 16) % 12);

  return `hsl(${hue} ${saturation}% ${lightness}%)`;
}

export function typeMeta(id: string): DocType {
  return docType(id);
}

export function fmtMoney(amt: number, currency: string, locale: string): string {
  const formatter = new Intl.NumberFormat(locale, {
    style: "currency",
    currency: currency,
  });

  return formatter.format(amt);
}

export function fmtDate(iso: string, locale: string): { short: string; year: number } {
  const d = parseLocalDate(iso);
  return {
    short: new Intl.DateTimeFormat(locale, { month: "short", day: "numeric" }).format(d),
    year: d.getFullYear(),
  };
}

export function currentMonthKey(): string {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
}

export function docMonthKey(date: string): string {
  const d = parseLocalDate(date);
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
}

export function monthOptions(n: number, locale: string): MonthOption[] {
  const out: MonthOption[] = [];
  const start = new Date();
  for (let i = 0; i < n; i++) {
    const d = new Date(start.getFullYear(), start.getMonth() - i, 1);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
    out.push({
      key,
      label: new Intl.DateTimeFormat(locale, { month: "short", year: "2-digit" }).format(d),
    });
  }
  return out;
}

export function monthsForYear(year: number, locale: string): MonthOption[] {
  return Array.from({ length: 12 }, (_, i) => {
    const d = new Date(year, i, 1);
    return {
      key: `${year}-${String(i + 1).padStart(2, "0")}`,
      label: new Intl.DateTimeFormat(locale, { month: "short" }).format(d),
    };
  });
}

export function monthOptionsBetween(fromKey: string, toKey: string, locale: string): MonthOption[] {
  const out: MonthOption[] = [];
  const [fy, fm] = fromKey.split("-").map(Number);
  const [ty, tm] = toKey.split("-").map(Number);
  let y = fy, m = fm;
  while (y < ty || (y === ty && m <= tm)) {
    const d = new Date(y, m - 1, 1);
    out.push({
      key: `${y}-${String(m).padStart(2, "0")}`,
      label: new Intl.DateTimeFormat(locale, { month: "short", year: "2-digit" }).format(d),
    });
    m++; if (m > 12) { m = 1; y++; }
  }
  return out;
}

export function fmtMonthKey(
  key: string,
  locale: string,
  options: Intl.DateTimeFormatOptions = { month: "short", year: "numeric" },
): string {
  return new Intl.DateTimeFormat(locale, options).format(monthDateFromKey(key));
}

export function defaultFilters(): AccountFilters {
  return {
    search: "",
    typeFilter: [],
    dateRange: { from: null, to: null },
    sort: { key: "date", dir: "desc" },
    page: 1,
  };
}

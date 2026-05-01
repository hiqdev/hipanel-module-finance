import type { AccountFilters, Doc, DocType, MonthOption } from "./types";

// export const DOCS: Record<string, Doc[]> = {
//   usd: [
//     { id: "d1", type: "invoice", name: "Hosting March 2026", ref: "INV-2026-0312", date: "2026-04-15", isNew: true },
//     { id: "d2", type: "payment", name: "Request #123", ref: "PR-00123", date: "2026-04-15", isNew: true },
//     { id: "d3", type: "installment", name: "Installment April", ref: "INST-APR-01", date: "2026-04-09", isNew: false },
//     { id: "d4", type: "service", name: "Service invoice #445", ref: "SRV-00445", date: "2026-04-09", isNew: false },
//     { id: "d5", type: "invoice", name: "Hosting February 2026", ref: "INV-2026-0287", date: "2026-03-15", isNew: false },
//     { id: "d6", type: "service", name: "Consulting — Q1 wrap-up", ref: "SRV-00441", date: "2026-03-08", isNew: false },
//     { id: "d7", type: "payment", name: "Request #118", ref: "PR-00118", date: "2026-02-28", isNew: false },
//     { id: "d8", type: "installment", name: "Installment March", ref: "INST-MAR-01", date: "2026-03-01", isNew: false },
//     { id: "d9", type: "invoice", name: "Hosting January 2026", ref: "INV-2026-0241", date: "2026-02-15", isNew: false },
//     { id: "d10", type: "service", name: "Service invoice #438", ref: "SRV-00438", date: "2026-02-09", isNew: false },
//     { id: "d11", type: "payment", name: "Request #115", ref: "PR-00115", date: "2026-02-01", isNew: false },
//   ],
//   eur: [
//     { id: "e1", type: "invoice", name: "SaaS subscription — Q2", ref: "INV-EU-0412", date: "2026-04-10", isNew: true },
//     { id: "e2", type: "service", name: "Translation services", ref: "SRV-EU-0118", date: "2026-04-02", isNew: false },
//     { id: "e3", type: "payment", name: "Request #EU-042", ref: "PR-EU-0042", date: "2026-03-28", isNew: false },
//     { id: "e4", type: "installment", name: "Installment — March", ref: "INST-EU-03", date: "2026-03-01", isNew: false },
//     { id: "e5", type: "invoice", name: "SaaS subscription — Q1", ref: "INV-EU-0401", date: "2026-01-10", isNew: false },
//   ],
//   gbp: [
//     { id: "g1", type: "invoice", name: "Advisory retainer — April", ref: "INV-GB-0221", date: "2026-04-05", isNew: false },
//     { id: "g2", type: "payment", name: "Request #GB-011", ref: "PR-GB-0011", date: "2026-03-22", isNew: false },
//   ],
// };

export const MONTH_NAMES = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

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

export function fmtMoney(amt: number, currency: string): string {
  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: currency,
  });

  return formatter.format(amt);
}

export function fmtDate(iso: string): { short: string; year: number } {
  const d = new Date(iso);
  return { short: `${MONTH_NAMES[d.getMonth()]} ${d.getDate()}`, year: d.getFullYear() };
}

export function currentMonthKey(): string {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
}

export function docMonthKey(date: string): string {
  const d = new Date(date);
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
}

export function monthOptions(n: number): MonthOption[] {
  const out: MonthOption[] = [];
  const start = new Date();
  for (let i = 0; i < n; i++) {
    const d = new Date(start.getFullYear(), start.getMonth() - i, 1);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
    out.push({ key, label: `${MONTH_NAMES[d.getMonth()]} ${String(d.getFullYear()).slice(2)}` });
  }
  return out;
}

export function fmtMonthKey(key: string): string {
  const [y, m] = key.split("-");
  return `${MONTH_NAMES[parseInt(m, 10) - 1]} ${y}`;
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

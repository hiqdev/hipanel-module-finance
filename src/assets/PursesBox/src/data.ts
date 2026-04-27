import type { Account, AccountFilters, Doc, DocType, MonthOption } from "./types";

export const ACCOUNTS: Account[] = [
  {
    id: "usd", code: "USD", symbol: "$", title: "USD Account",
    balance: 12480.55, credit: 5000.00,
    contact: { name: "Olivia Martinez", email: "o.martinez@acme.co" },
    paymentDetails: "Chase Bank · 2847",
  },
  {
    id: "eur", code: "EUR", symbol: "€", title: "EUR Account",
    balance: 8234.10, credit: 3000.00,
    contact: { name: "Jonas Weber", email: "j.weber@acme.co" },
    paymentDetails: "Deutsche Bank · 9104",
  },
  {
    id: "gbp", code: "GBP", symbol: "£", title: "GBP Account",
    balance: 2150.00, credit: 1000.00,
    contact: { name: "Priya Shah", email: "p.shah@acme.co" },
    paymentDetails: "Barclays · 5521",
  },
];

export const DOC_TYPES: DocType[] = [
  { id: "invoice", label: "Invoice", className: "t-invoice" },
  { id: "service", label: "Service invoice", className: "t-service" },
  { id: "payment", label: "Payment request", className: "t-payment" },
  { id: "installment", label: "Installment invoice", className: "t-installment" },
];

export const DOCS: Record<string, Doc[]> = {
  usd: [
    { id: "d1", type: "invoice", name: "Hosting March 2026", ref: "INV-2026-0312", date: "2026-04-15", isNew: true },
    { id: "d2", type: "payment", name: "Request #123", ref: "PR-00123", date: "2026-04-15", isNew: true },
    { id: "d3", type: "installment", name: "Installment April", ref: "INST-APR-01", date: "2026-04-09", isNew: false },
    { id: "d4", type: "service", name: "Service invoice #445", ref: "SRV-00445", date: "2026-04-09", isNew: false },
    { id: "d5", type: "invoice", name: "Hosting February 2026", ref: "INV-2026-0287", date: "2026-03-15", isNew: false },
    { id: "d6", type: "service", name: "Consulting — Q1 wrap-up", ref: "SRV-00441", date: "2026-03-08", isNew: false },
    { id: "d7", type: "payment", name: "Request #118", ref: "PR-00118", date: "2026-02-28", isNew: false },
    { id: "d8", type: "installment", name: "Installment March", ref: "INST-MAR-01", date: "2026-03-01", isNew: false },
    { id: "d9", type: "invoice", name: "Hosting January 2026", ref: "INV-2026-0241", date: "2026-02-15", isNew: false },
    { id: "d10", type: "service", name: "Service invoice #438", ref: "SRV-00438", date: "2026-02-09", isNew: false },
    { id: "d11", type: "payment", name: "Request #115", ref: "PR-00115", date: "2026-02-01", isNew: false },
  ],
  eur: [
    { id: "e1", type: "invoice", name: "SaaS subscription — Q2", ref: "INV-EU-0412", date: "2026-04-10", isNew: true },
    { id: "e2", type: "service", name: "Translation services", ref: "SRV-EU-0118", date: "2026-04-02", isNew: false },
    { id: "e3", type: "payment", name: "Request #EU-042", ref: "PR-EU-0042", date: "2026-03-28", isNew: false },
    { id: "e4", type: "installment", name: "Installment — March", ref: "INST-EU-03", date: "2026-03-01", isNew: false },
    { id: "e5", type: "invoice", name: "SaaS subscription — Q1", ref: "INV-EU-0401", date: "2026-01-10", isNew: false },
  ],
  gbp: [
    { id: "g1", type: "invoice", name: "Advisory retainer — April", ref: "INV-GB-0221", date: "2026-04-05", isNew: false },
    { id: "g2", type: "payment", name: "Request #GB-011", ref: "PR-GB-0011", date: "2026-03-22", isNew: false },
  ],
};

export const MONTH_NAMES = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

export function typeMeta(id: string): DocType {
  return DOC_TYPES.find(t => t.id === id)!;
}

export function fmtMoney(amt: number, symbol: string): string {
  const s = amt.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  return `${symbol}${s}`;
}

export function fmtDate(iso: string): { short: string; year: number } {
  const d = new Date(iso);
  return { short: `${MONTH_NAMES[d.getMonth()]} ${d.getDate()}`, year: d.getFullYear() };
}

export function currentMonthKey(): string {
  const d = new Date();
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

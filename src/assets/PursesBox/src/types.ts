export interface Account {
  id: string;
  code: string;
  symbol: string;
  title: string;
  balance: number;
  credit: number;
  contact: { name: string; email: string };
  paymentDetails: string;
}

export interface DocType {
  id: string;
  label: string;
  className: string;
}

export interface Doc {
  id: string;
  type: string;
  name: string;
  ref: string;
  date: string;
  isNew: boolean;
}

export interface SortState {
  key: "type" | "ref" | "date";
  dir: "asc" | "desc";
}

export interface DateRange {
  from: string | null;
  to: string | null;
}

export type ModalKind = "generate" | "preview" | "update-replace" | "preview-updated";

export interface ModalState {
  kind: ModalKind;
  initial?: { type: string; month: string };
  doc?: Doc;
  busy?: boolean;
  progress?: number;
}

export interface ToastState {
  msg: string;
  icon: string;
}

export interface FilterOption {
  id: string;
  label: string;
  dot?: string;
  count?: number;
}

export interface SelectOption {
  value: string;
  label: string;
  sub?: string;
  icon?: string;
}

export interface MonthOption {
  key: string;
  label: string;
}

export interface AccountFilters {
  search: string;
  typeFilter: string[];
  dateRange: DateRange;
  sort: SortState;
  page: number;
}

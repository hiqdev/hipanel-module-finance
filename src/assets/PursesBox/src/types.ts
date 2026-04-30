export type PermissionKey =
  | "bill.read"
  | "document.read"
  | "document.generate"
  | "purse.update"
  | "client.update"
  | "owner-staff"
  | "has-own-seller"
  | "is-employee";

export interface PursesBoxProps {
  language: string,
  purses: Purse[];
  permissions: PermissionKey[];
}

export interface Purse {
  id: string;
  client_id: string;
  seller_id: string;
  currency: string;
  symbol: string;
  title: string;
  balance: number;
  contact: Contact;
  requisite?: Requisite;
  documents: Doc[];
  paymentDetails: string;
}

export interface Contact {
  id: string;
  name: string;
  email: string;
}

export interface Requisite {
  id: string;
  name: string;
  organization: string;
}

export interface DocType {
  id: string;
  label: string;
}

export interface Doc {
  id: string;
  type: string;
  type_label: string;
  filename: string;
  number: string;
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

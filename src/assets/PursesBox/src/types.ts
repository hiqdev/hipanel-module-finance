export type PermissionKey = string;

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
  balance: number;
  contact: Contact;
  requisite?: Requisite;
  documents: Doc[];
}

export interface Party {
  id: string;
  name: string;
  bankDetails: BankDetails[];
  organization?: string;
}

export interface Contact extends Party {
  email: string;
}

export interface Requisite extends Party {
}

export interface BankDetails {
  id: string;
  requisite_id: string;
  currency_id: string;
  no: string;
  currency: string;
  bank_name?: string;
  bank_account?: string;
  bank_address?: string;
  bank_swift?: string;
  bank_correspondent?: string;
  bank_correspondent_swift?: string;
  summary?: string;
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
  file_id: string;
  number: string;
  date: string;
  isNew: boolean;
  location?: string;
  bill_id?: string;
  url?: string;
}

export interface SortState {
  key: "type" | "number" | "date";
  dir: "asc" | "desc";
}

export interface DateRange {
  from: string | null;
  to: string | null;
}

export type ModalKind = "update" | "preview";

export interface ModalState {
  kind: ModalKind;
  initial?: { type: string; month: string };
  busy?: boolean;
  progress?: number;
}

export interface DocParams {
  id: string;
  client_id: string;
  type: string;
  month: string;
  seller_bank_account_no?: number;
  client_bank_account_no?: number;
  location?: string;
  bill_id?: string;
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

export interface GenerationResponse {
  status: "success" | "error";
  errors: string[];
  message: string;
  data?: Doc[];
}

export interface ApiError {
  status: number;
  message: string;
}

const BASE_URL = "";

interface ApiError {
  status: number;
  message: string;
}

function getCsrfToken(): string {
  const meta = document.querySelector("meta[name=\"csrf-token\"]");
  return meta?.getAttribute("content") ?? "";
}

async function request<T>(url: string, options: RequestInit = {}): Promise<T> {
  const response = await fetch(BASE_URL + url, {
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
      "X-CSRF-Token": getCsrfToken(),
      ...options.headers,
    },
    ...options,
  });

  if (!response.ok) {
    const error: ApiError = {
      status: response.status,
      message: await response.text(),
    };
    throw error;
  }

  const text = await response.text();

  return (text ? JSON.parse(text) : null) as T;
}

import type { Contact, Requisite } from "./types.ts";

export const api = {
  get: <T>(url: string) => request<T>(url),
  post: <T>(url: string, data: unknown) => request<T>(url, { method: "POST", body: JSON.stringify(data) }),
};

export const purseSettingsApi = {
  getContacts: (client_id: string) => api.post<Contact[]>(`/client/contact/search`, { client_id }),
  getRequisites: (client_id?: string) => api.post<Requisite[]>(`/finance/requisite/search`, { client_id }),
  updateContact: (purseId: string, contactId: string) =>
    api.post<void>(`/finance/purse/update-contact`, { name: "contact_id", pk: purseId, value: contactId }),
  updateRequisite: (purseId: string, requisiteId: string) =>
    api.post<void>(`/finance/purse/update-requisite?id=${purseId}`, { name: "requisite_id", value: requisiteId, pk: purseId }),
};

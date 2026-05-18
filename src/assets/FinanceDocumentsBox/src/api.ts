import type { ApiError, Contact, CreatePurseParams, DocParams, GenerationResponse, PursesDocumentsProps, Requisite } from "./types.ts";

const BASE_URL = "";

function extractErrorMessage(payload: unknown): string | null {
  if (payload && typeof payload === "object" && "status" in payload && (payload as { status: unknown }).status === "error") {
    const response = payload as { message?: unknown; errors?: unknown };

    if (typeof response.message === "string" && response.message.trim().length > 0) {
      return response.message;
    }

    if (Array.isArray(response.errors)) {
      const error = response.errors.find(e => typeof e === "string" && e.trim().length > 0);
      if (error) {
        return error;
      }
    }
  }

  if (payload && typeof payload === "object" && "error" in payload) {
    const errorValue = (payload as { error: unknown }).error;

    if (typeof errorValue === "string" && errorValue.trim().length > 0) {
      return errorValue;
    }

    if (errorValue !== null && errorValue !== undefined && String(errorValue).trim().length > 0) {
      return String(errorValue);
    }
  }

  return null;
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

  const text = await response.text();

  let payload: unknown = null;
  if (text) {
    try {
      payload = JSON.parse(text);
    } catch {
      payload = text;
    }
  }

  const payloadErrorMessage = extractErrorMessage(payload);

  if (!response.ok) {
    const error: ApiError = {
      status: response.status,
      message: payloadErrorMessage ?? text,
    };
    throw error;
  }

  if (payloadErrorMessage !== null) {
    const error: ApiError = {
      status: response.status,
      message: payloadErrorMessage,
    };
    throw error;
  }

  return payload as T;
}


const api = {
  get: <T>(url: string) => request<T>(url),
  post: <T>(url: string, data: unknown) => request<T>(url, { method: "POST", body: JSON.stringify(data) }),
};

export const purseDocumentsApi = {
  previewDocument: (p: DocParams) =>
    api.post<GenerationResponse>(`/finance/purse/preview-document`, p),
  previewActs: (p: DocParams) =>
    api.post<GenerationResponse>(`/finance/purse/preview-acts`, p),
  previewMonthlyDocument: (p: DocParams) =>
    api.post<GenerationResponse>(`/finance/purse/preview-monthly-document`, p),
  generateAndSaveDocument: (p: DocParams) =>
    api.post<GenerationResponse>("/finance/purse/generate-and-save-document", p),
  generateAndSaveMonthlyDocument: (p: DocParams) =>
    api.post<GenerationResponse>("/finance/purse/generate-and-save-monthly-document", p),
  generateAndSaveActs: (p: DocParams) =>
    api.post<GenerationResponse>("/finance/purse/generate-and-save-acts", p),
};

export const purseApi = {
  createPurse: (p: CreatePurseParams) =>
    api.post<void>("/finance/purse/create", p),
  fetchState: (p: { client_id: string }) =>
    api.post<PursesDocumentsProps>(`/client/client/get-finance-document-state?client_id=${p.client_id}`, p),
};

export const purseSettingsApi = {
  getContacts: (client_id: string) =>
    api.post<Contact[]>(`/client/contact/search`, { client_id }),
  getRequisites: (client_id?: string, query?: string) =>
    api.post<Requisite[]>(`/finance/requisite/search`, {
      client_id,
      name_ilike: query,
    }),
  updateContact: (purseId: string, contactId: string) =>
    api.post<void>(`/finance/purse/update-contact`, { name: "contact_id", pk: purseId, value: contactId }),
  updateRequisite: (purseId: string, requisiteId: string) =>
    api.post<void>(`/finance/purse/update-requisite?id=${purseId}`, { name: "requisite_id", value: requisiteId, pk: purseId }),
};

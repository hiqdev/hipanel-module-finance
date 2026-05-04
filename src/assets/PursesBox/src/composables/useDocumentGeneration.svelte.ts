import type { Doc, DocParams, ModalState, Purse } from "../types";
import { docMonthKey } from "../data";
import type { ToastType } from "./useToast.svelte";
import { purseDocumentsApi } from "../api";

type DocEndpoint = (p: DocParams) => Promise<Doc>;
type DocEndpoints = { preview: DocEndpoint; update: DocEndpoint };

const defaultEndpoints: DocEndpoints = {
  preview: purseDocumentsApi.previewMonthlyDocument,
  update:  purseDocumentsApi.generateAndSaveMonthlyDocument,
};

// Add a row here when a document type needs non-default endpoints.
// Omit a key to fall back to defaultEndpoints for that action.
const routesByType: Record<string, Partial<DocEndpoints>> = {
  contracts:        { update: purseDocumentsApi.generateAndSaveDocument },
  probations:       { update: purseDocumentsApi.generateAndSaveDocument },
  nda:              { update: purseDocumentsApi.generateAndSaveDocument },
  internalinvoices: { update: purseDocumentsApi.generateAndSaveActs },
};

function pickEndpoint(type: string, action: keyof DocEndpoints): DocEndpoint {
  return (routesByType[type]?.[action] ?? defaultEndpoints[action]);
}

function excludeDocForMonth(docs: Doc[], type: string, monthKey: string): Doc[] {
  return docs.filter(d => d.type !== type || docMonthKey(d.date) !== monthKey);
}

export function useDocumentGeneration(
  getDocs: () => Doc[],
  setDocs: (docs: Doc[]) => void,
  showToast: (msg: string, type?: ToastType) => void,
  getPurse: () => Pick<Purse, "id" | "client_id">,
) {
  let modal = $state<ModalState | null>(null);
  let pendingUpdate = $state<Doc | null>(null);
  let previewResult = $state<{ doc: Doc; canSave: boolean } | null>(null);
  let busyRowIds = $state<string[]>([]);

  function handleSubmit({ type, month, willReplace, mode, seller_bank_account_no = 0, client_bank_account_no }: {
    type: string; month: string; willReplace: boolean; mode: string; seller_bank_account_no?: number; client_bank_account_no?: number;
  }) {
    if (!modal) return;
    modal = { ...modal, busy: true, progress: 0 };
    const { id, client_id } = getPurse();
    const action = mode.startsWith("preview") ? "preview" : "update";

    pickEndpoint(type, action)({ type, month, seller_bank_account_no, client_bank_account_no, client_id, id })
      .then(doc => {
        modal = null;
        if (action === "preview") {
          previewResult = { doc, canSave: true };
        } else {
          const saved = { ...doc, isNew: true };
          setDocs([saved, ...excludeDocForMonth(getDocs(), type, month)]);
          showToast(willReplace ? `Document replaced — ${saved.number}` : `Document generated — ${saved.number}`);
        }
      })
      .catch((e: any) => {
        modal = null;
        showToast(e.message ?? "Generation failed", "error");
      });
  }

  function handleRowAction(kind: string, doc: Doc) {
    const initial = { type: doc.type, month: docMonthKey(doc.date) };
    if (kind === "update") {
      pendingUpdate = doc;
      return;
    }
    if (kind === "preview") {
      modal = { kind: "preview-locked", initial, doc };
    }
  }

  function applyUpdate() {
    const doc = pendingUpdate!;
    const monthKey = docMonthKey(doc.date);
    pendingUpdate = null;
    busyRowIds = [...busyRowIds, doc.id];

    const { id, client_id } = getPurse();
    pickEndpoint(doc.type, "update")({ type: doc.type, month: monthKey, seller_bank_account_no: 0, client_id, id })
      .then(newDoc => {
        setDocs(getDocs().map(d => d.id === doc.id ? { ...newDoc, isNew: true } : d));
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(`${doc.type_label} replaced — ${newDoc.number}`);
      })
      .catch((e: any) => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(e.message ?? "Generation failed", "error");
      });
  }

  function applyPreview() {
    if (!previewResult) return;
    const doc = previewResult.doc;
    const monthKey = docMonthKey(doc.date);
    setDocs([doc, ...excludeDocForMonth(getDocs(), doc.type, monthKey)]);
    previewResult = null;
    showToast(`Preview applied — ${doc.number}`);
  }

  return {
    get modal() {
      return modal;
    },
    get pendingUpdate() {
      return pendingUpdate;
    },
    get previewResult() {
      return previewResult;
    },
    get busyRowIds() {
      return busyRowIds;
    },
    openUpdate: () => {
      modal = { kind: "update" };
    },
    openPreview: () => {
      modal = { kind: "preview" };
    },
    closeModal: () => {
      if (!modal?.busy) modal = null;
    },
    cancelUpdate: () => {
      pendingUpdate = null;
    },
    closePreview: () => {
      previewResult = null;
    },
    handleSubmit,
    handleRowAction,
    applyUpdate,
    applyPreview,
  };
}

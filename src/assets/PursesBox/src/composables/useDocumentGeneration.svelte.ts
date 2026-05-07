import type { Doc, DocParams, GenerationResponse, ModalState, Purse } from "../types";
import { docMonthKey } from "../data";
import type { ToastType } from "./useToast.svelte";
import { purseDocumentsApi } from "../api";

type DocEndpoint = (p: DocParams) => Promise<GenerationResponse>;
type DocEndpoints = { preview: DocEndpoint; update: DocEndpoint };

const defaultEndpoints: DocEndpoints = {
  preview: purseDocumentsApi.previewMonthlyDocument,
  update: purseDocumentsApi.generateAndSaveMonthlyDocument,
};

// Add a row here when a document type needs non-default endpoints.
// Omit a key to fall back to defaultEndpoints for that action.
const routesByType: Record<string, Partial<DocEndpoints>> = {
  contracts: { update: purseDocumentsApi.generateAndSaveDocument },
  probations: { update: purseDocumentsApi.generateAndSaveDocument },
  nda: { update: purseDocumentsApi.generateAndSaveDocument },
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
  // ── State ──────────────────────────────────────────────────────────────────
  let modal = $state<ModalState | null>(null);
  let pendingUpdate = $state<Doc | null>(null);
  let previewResult = $state<{ doc: Doc; canSave: boolean } | null>(null);
  let busyRowIds = $state<string[]>([]);

  // ── Flow A: free-form modal (top "Preview" / "Generate" buttons) ───────────
  // Called by GenerateModal onSubmit. Marks matching rows busy while the API
  // runs, then transitions them in-place to isNew on success.
  function handleSubmit({ type, month, willReplace, mode, seller_bank_account_no, client_bank_account_no }: {
    type: string;
    month: string;
    willReplace: boolean;
    mode: string;
    seller_bank_account_no?: number;
    client_bank_account_no?: number;
  }) {
    if (!modal) return;
    modal = { ...modal, busy: true, progress: 0 };

    const affectedIds = getDocs()
      .filter(d => d.type === type && docMonthKey(d.date) === month)
      .map(d => d.id);
    busyRowIds = [...busyRowIds, ...affectedIds];

    const { id, client_id } = getPurse();
    const isPreview = mode === "preview";

    pickEndpoint(type, isPreview ? "preview" : "update")({
      type, month, client_id, id,
      ...(seller_bank_account_no != null ? { seller_bank_account_no } : {}),
      ...(client_bank_account_no != null ? { client_bank_account_no } : {}),
    })
      .then(rsp => {
        modal = null;
        busyRowIds = busyRowIds.filter(x => !affectedIds.includes(x));
        const data = rsp?.data;
        if (!data) {
          showToast("No document data returned", "error");
          return;
        }
        const newDoc = { ...data, isNew: true };

        if (isPreview) {
          previewResult = { doc: newDoc, canSave: true };
        } else {
          // Busy rows transition in place to isNew: true.
          // First matching row is replaced with the new doc; extras removed.
          // If no existing docs matched, prepend the new one.
          const current = getDocs();
          let placed = false;
          const next: Doc[] = affectedIds.length === 0 ? [newDoc] : [];
          for (const d of current) {
            if (!affectedIds.includes(d.id)) {
              next.push(d);
              continue;
            }
            if (!placed) {
              next.push(newDoc);
              placed = true;
            }
          }
          setDocs(next);
          showToast(willReplace ? `Document replaced — ${newDoc.number}` : `Document generated — ${newDoc.number}`);
        }
      })
      .catch((e: any) => {
        modal = null;
        busyRowIds = busyRowIds.filter(x => !affectedIds.includes(x));
        showToast(e.message ?? "Generation failed", "error");
      });
  }

  // ── Flow B: row actions ────────────────────────────────────────────────────
  // Each kind has its own direct path — no routing through the modal form.
  function handleRowAction(kind: string, doc: Doc) {
    if (kind === "update") {
      pendingUpdate = doc;
      return;
    }
    if (kind === "preview") {
      previewRowDoc(doc);
    }
  }

  // Row "preview": call the preview API directly; no form modal needed since
  // type, month, and all params are already known from the existing doc.
  function previewRowDoc(doc: Doc) {
    const monthKey = docMonthKey(doc.date);
    busyRowIds = [...busyRowIds, doc.id];
    const { id, client_id } = getPurse();

    pickEndpoint(doc.type, "preview")({
      type: doc.type,
      month: monthKey,
      client_id,
      id,
      bill_id: doc.bill_id,
      location: doc.location,
    })
      .then(rsp => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        const data = rsp?.data;
        if (!data) {
          showToast("Preview returned no document data", "error");
          return;
        }
        previewResult = { doc: data as Doc, canSave: true };
      })
      .catch((e: any) => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(e.message ?? "Preview failed", "error");
      });
  }

  // Row "update": called after ConfirmReplaceModal is confirmed.
  function applyUpdate() {
    const doc = pendingUpdate!;
    pendingUpdate = null;
    busyRowIds = [...busyRowIds, doc.id];
    const { id, client_id } = getPurse();

    pickEndpoint(doc.type, "update")({
      type: doc.type,
      month: docMonthKey(doc.date),
      client_id,
      id,
      bill_id: doc.bill_id,
      location: doc.location,
    })
      .then(rsp => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        const data = rsp?.data;
        if (!data) {
          showToast("Update returned no document data", "error");
          return;
        }
        setDocs(getDocs().map((d): Doc => (d.id === doc.id ? ({ ...(data as Doc), isNew: true }) : d)));
        showToast(`${doc.type_label} replaced — ${data.number}`);
      })
      .catch((e: any) => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(e.message ?? "Generation failed", "error");
      });
  }

  // ── Shared: preview result (both flows converge here) ──────────────────────
  function applyPreview() {
    if (!previewResult) return;
    const doc = previewResult.doc;
    setDocs([doc, ...excludeDocForMonth(getDocs(), doc.type, docMonthKey(doc.date))]);
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

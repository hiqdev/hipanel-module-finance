import type { Doc, DocParams, GenerationResponse, ModalState, PreviewDoc, Purse } from "../types";
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
  contract: { update: purseDocumentsApi.generateAndSaveDocument, preview: purseDocumentsApi.previewDocument },
  probation: { update: purseDocumentsApi.generateAndSaveDocument, preview: purseDocumentsApi.previewDocument },
  nda: { update: purseDocumentsApi.generateAndSaveDocument, preview: purseDocumentsApi.previewDocument },
  internal_invoice: { update: purseDocumentsApi.generateAndSaveActs, preview: purseDocumentsApi.previewActs },
};

function pickEndpoint(type: string, action: keyof DocEndpoints): DocEndpoint {
  return (routesByType[type]?.[action] ?? defaultEndpoints[action]);
}

function markAsNew(docs: Doc[], ids: string[]): Doc[] {
  return docs.map(d => ids.includes(d.id) ? { ...d, isNew: true } : d);
}

function cryptoRand(min: number, max: number) {
  return crypto.getRandomValues(new Uint32Array(1))[0] % (max - min + 1) + min;
}

function extractUrls(data: Doc[] | PreviewDoc[] | undefined): string[] {
  if (!data) return [];
  return data
    .filter((e): e is PreviewDoc => e != null && typeof e === "object" && "uuid" in e)
    .map(e => `/document/document/get-cached-file?uuid=${e.uuid}&v=${cryptoRand(1, 1000000)}`);
}

export function useDocumentGeneration(
  getDocs: () => Doc[],
  setDocs: (docs: Doc[]) => void,
  showToast: (msg: string, type?: ToastType) => void,
  getPurse: () => Pick<Purse, "id" | "client_id">,
  onGenerated?: () => void,
) {
  // ── State ──────────────────────────────────────────────────────────────────
  let modal = $state<ModalState | null>(null);
  let pendingUpdate = $state<Doc | null>(null);
  let previewResult = $state<{ files: string[] } | null>(null);
  let busyRowIds = $state<string[]>([]);

  // ── Flow A: free-form modal (top "Preview" / "Generate" buttons) ───────────
  // Captures affected rows before the API call, marks them busy, then on
  // success transitions them in-place to isNew (update) or opens the preview
  // modal with file URLs from the response (preview).
  function handleSubmit({ type, period, willReplace, mode, seller_bank_account_no, client_bank_account_no }: {
    type: string;
    period: string;
    willReplace: boolean;
    mode: string;
    seller_bank_account_no?: number;
    client_bank_account_no?: number;
  }) {
    if (!modal) return;
    modal = { ...modal, busy: true, progress: 0 };

    const timer = setInterval(() => {
      if (modal && (modal.progress ?? 0) < 90) {
        modal = { ...modal, progress: (modal.progress ?? 0) + (90 - (modal.progress ?? 0)) * 0.07 };
      }
    }, 400);

    const affectedDocs = getDocs().filter(d => d.type === type && docMonthKey(d.date) === period);
    const affectedIds = affectedDocs.map(d => d.id);
    busyRowIds = [...busyRowIds, ...affectedIds];

    const { id, client_id } = getPurse();
    const isPreview = mode === "preview";

    pickEndpoint(type, isPreview ? "preview" : "update")({
      type, period: period, client_id, id,
      ...(seller_bank_account_no != null ? { seller_bank_account_no } : {}),
      ...(client_bank_account_no != null ? { client_bank_account_no } : {}),
    })
      .then(rsp => {
        clearInterval(timer);
        modal = null;
        busyRowIds = busyRowIds.filter(x => !affectedIds.includes(x));

        if (isPreview) {
          previewResult = { files: extractUrls(rsp?.data) };
        } else if (affectedDocs.length > 0) {
          setDocs(markAsNew(getDocs(), affectedIds));
          showToast(willReplace ? "Document replaced" : "Document generated");
          onGenerated?.();
        } else {
          const rawData = rsp?.data;
          if (!rawData || !Array.isArray(rawData)) {
            showToast("Unexpected server response", "error");
          } else {
            const founds = (rawData as Doc[])
              .filter(d => d != null && typeof d === "object" && "id" in d && !!d.id)
              .map(d => ({ ...d, isNew: true as const }));
            if (founds.length === 0) {
              showToast("No documents were generated", "error");
            } else {
              const existingDocs = getDocs();
              const existingById = new Map(existingDocs.map(d => [d.id, d]));
              const duplicate = founds.find(d => existingById.has(d.id));
              if (duplicate) {
                const existing = existingById.get(duplicate.id)!;
                showToast(
                  `Document already exists for ${existing.type_label ?? existing.type} (${docMonthKey(existing.date)})`,
                  "error",
                );
              } else {
                setDocs([...existingDocs, ...founds]);
                showToast("Document generated");
                onGenerated?.();
              }
            }
          }
        }
      })
      .catch((e: any) => {
        clearInterval(timer);
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

  // Row "preview": call the preview API and open the result modal with the
  // returned file URLs. The existing doc is passed as context for the header.
  function previewRowDoc(doc: Doc) {
    busyRowIds = [...busyRowIds, doc.id];
    const { id, client_id } = getPurse();

    pickEndpoint(doc.type, "preview")({
      type: doc.type,
      period: docMonthKey(doc.date),
      client_id,
      id,
      bill_id: doc.bill_id,
      location: doc.location,
    })
      .then(rsp => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        previewResult = { files: extractUrls(rsp?.data) };
      })
      .catch((e: any) => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(e.message ?? "Preview failed", "error");
      });
  }

  // Row "update": called after ConfirmReplaceModal is confirmed.
  // API response data is not used — the existing doc is marked isNew in place.
  function applyUpdate() {
    const doc = pendingUpdate!;
    pendingUpdate = null;
    busyRowIds = [...busyRowIds, doc.id];
    const { id, client_id } = getPurse();

    pickEndpoint(doc.type, "update")({
      type: doc.type,
      period: docMonthKey(doc.date),
      client_id,
      id,
      bill_id: doc.bill_id,
      location: doc.location,
    })
      .then(() => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        setDocs(markAsNew(getDocs(), [doc.id]));
        showToast(`${doc.type_label} replaced`);
        onGenerated?.();
      })
      .catch((e: any) => {
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(e.message ?? "Generation failed", "error");
      });
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
  };
}

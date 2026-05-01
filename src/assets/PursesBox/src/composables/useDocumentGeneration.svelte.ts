import type { Doc, ModalState } from "../types";
import { docMonthKey, fmtMonthKey, typeMeta } from "../data";

function runGeneration({ durationMs = 2400, onProgress, onDone }: {
  durationMs?: number;
  onProgress: (p: number) => void;
  onDone: () => void;
}) {
  const start = Date.now();
  const tick = () => {
    const p = Math.min(100, Math.round(((Date.now() - start) / durationMs) * 100));
    onProgress(p);
    if (p < 100) setTimeout(tick, 80);
    else onDone();
  };
  tick();
}

function excludeDocForMonth(docs: Doc[], type: string, monthKey: string): Doc[] {
  return docs.filter(d => d.type !== type || docMonthKey(d.date) !== monthKey);
}

export function useDocumentGeneration(
  getDocs: () => Doc[],
  setDocs: (docs: Doc[]) => void,
  showToast: (msg: string, icon?: string) => void,
) {
  let modal = $state<ModalState | null>(null);
  let confirmReplace = $state<Doc | null>(null);
  let previewResult = $state<{ doc: Doc; canSave: boolean } | null>(null);
  let busyRowIds = $state<string[]>([]);

  function handleSubmitGenerate({ type, month, willReplace, mode }: {
    type: string; month: string; willReplace: boolean; mode: string;
  }) {
    if (!modal) return;
    modal = { ...modal, busy: true, progress: 0 };

    runGeneration({
      onProgress: p => {
        if (modal) modal = { ...modal, progress: p };
      },
      onDone: () => {
        const tm = typeMeta(type);
        const [yr, mo] = month.split("-");
        const newDoc: Doc = {
          id: `gen-${Date.now()}`,
          type,
          type_label: tm.label,
          filename: `${tm.label} ${fmtMonthKey(month)}`,
          number: `${type.slice(0, 3).toUpperCase()}-${yr}-${String(Math.floor(Math.random() * 900) + 100)}`,
          date: `${yr}-${mo}-15`,
          isNew: true,
        };
        if (mode === "generate" || mode === "update-replace") {
          setDocs([newDoc, ...excludeDocForMonth(getDocs(), type, month)]);
          modal = null;
          showToast(willReplace ? `Document replaced — ${newDoc.number}` : `Document generated — ${newDoc.number}`);
        } else {
          modal = null;
          previewResult = { doc: newDoc, canSave: mode === "preview-updated" };
        }
      },
    });
  }

  function handleRowAction(kind: string, doc: Doc) {
    if (kind === "download") {
      showToast(`Downloading ${doc.number}…`, "fa-download");
      return;
    }
    if (kind === "view") {
      showToast(`Opening ${doc.number}`, "fa-eye");
      return;
    }

    const initial = { type: doc.type, month: docMonthKey(doc.date) };
    if (kind === "update-replace") {
      confirmReplace = doc;
      return;
    }
    if (kind === "preview-updated") {
      modal = { kind: "preview-updated", initial, doc };
    }
  }

  function confirmReplaceNow() {
    const doc = confirmReplace!;
    const monthKey = docMonthKey(doc.date);
    confirmReplace = null;

    busyRowIds = [...busyRowIds, doc.id];
    runGeneration({
      onProgress: () => {
      },
      onDone: () => {
        const tm = typeMeta(doc.type);
        const newDoc: Doc = {
          ...doc,
          id: `gen-${Date.now()}`,
          number: `${doc.type.slice(0, 3).toUpperCase()}-${monthKey.replace("-", "")}-${String(Math.floor(Math.random() * 900) + 100)}`,
          isNew: true,
        };
        setDocs(getDocs().map(d => d.id === doc.id ? newDoc : d));
        busyRowIds = busyRowIds.filter(x => x !== doc.id);
        showToast(`${tm.label} replaced — ${newDoc.number}`, "fa-refresh");
      },
    });
  }

  function savePreviewResult() {
    if (!previewResult) return;
    const doc = previewResult.doc;
    const monthKey = docMonthKey(doc.date);
    setDocs([doc, ...excludeDocForMonth(getDocs(), doc.type, monthKey)]);
    previewResult = null;
    showToast(`Preview saved — ${doc.number}`);
  }

  return {
    get modal() {
      return modal;
    },
    get confirmReplace() {
      return confirmReplace;
    },
    get previewResult() {
      return previewResult;
    },
    get busyRowIds() {
      return busyRowIds;
    },
    openGenerate: () => {
      modal = { kind: "generate" };
    },
    openPreview: () => {
      modal = { kind: "preview" };
    },
    closeModal: () => {
      if (!modal?.busy) modal = null;
    },
    closeConfirm: () => {
      confirmReplace = null;
    },
    closePreview: () => {
      previewResult = null;
    },
    handleSubmitGenerate,
    handleRowAction,
    confirmReplaceNow,
    savePreviewResult,
  };
}

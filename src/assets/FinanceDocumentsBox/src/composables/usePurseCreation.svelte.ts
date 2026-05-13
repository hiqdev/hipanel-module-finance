import type { Purse, PursesDocumentsProps } from "../types";
import { purseApi } from "../api";
import type { ToastType } from "./useToast.svelte";

export function usePurseCreation(
  getPurse: () => Purse,
  onRefresh: (state: PursesDocumentsProps) => void,
  showToast: (msg: string, type?: ToastType) => void,
) {
  let busy = $state(false);
  let error = $state<string | null>(null);

  async function submit(currency: string) {
    busy = true;
    error = null;
    const p = getPurse();
    try {
      await purseApi.createPurse({ Purse: { currency, client_id: p.client_id, seller_id: p.seller_id }});
      const state = await purseApi.fetchState({ client_id: p.client_id });
      onRefresh(state);
      showToast("Purse created");
    } catch (e: any) {
      error = e.message ?? "Failed to create purse";
      showToast(error!, "error");
    } finally {
      busy = false;
    }
  }

  return {
    get busy() {
      return busy;
    },
    get error() {
      return error;
    },
    submit,
  };
}

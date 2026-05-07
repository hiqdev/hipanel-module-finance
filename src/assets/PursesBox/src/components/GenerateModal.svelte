<script lang="ts">
  import { untrack } from "svelte";
  import type { DocType, ModalKind, Purse } from "../types";
  import { currentMonthKey, monthsForYear } from "../data";

  let { mode, initial, types, existingMonths, busy, progress, onClose, onSubmit, language, purse }: {
      mode: ModalKind;
      initial?: { type: string; month: string; clientBankAccountNo?: number; sellerBankAccountNo?: number };
      types: DocType[];
      existingMonths: Record<string, string[]>;
      busy: boolean;
      progress: number;
      onClose: () => void;
      onSubmit: (args: {
          type: string;
          month: string;
          willReplace: boolean;
          mode: ModalKind;
          client_bank_account_no?: number;
          seller_bank_account_no?: number;
      }) => void;
      language: string;
      purse: Purse;
  } = $props();

  // untrack: deliberately read initial prop only once — the modal remounts on each open.
  let type = $state(untrack(() => initial?.type ?? types[0]?.id ?? ""));
  let clientBankAccountNo = $state(untrack(() => initial?.clientBankAccountNo ?? null));
  let sellerBankAccountNo = $state(untrack(() => initial?.sellerBankAccountNo ?? null));
  let selectedYear = $state(untrack(() => +(initial?.month ?? currentMonthKey()).split("-")[0]));
  let selectedMonthNum = $state(untrack(() => +(initial?.month ?? currentMonthKey()).split("-")[1]));
  let month = $derived(`${selectedYear}-${String(selectedMonthNum).padStart(2, "0")}`);

  let willReplace = $derived.by(() => {
      if (mode === "preview") return false;
      return !!(existingMonths[type]?.includes(month));
  });

  const titles: Record<ModalKind, { title: string; btn: string; icon: string; btnBusy: string }> = {
      "update":  { title: "Generate document", btn: "Generate", icon: "fa-cog", btnBusy: "Generating…"       },
      "preview": { title: "Preview document",  btn: "Preview",  icon: "fa-eye", btnBusy: "Building preview…" },
  };

  let t = $derived(titles[mode]);
  let minYear = $derived.by(() => {
      const allKeys = Object.values(existingMonths).flat();
      const cur = new Date().getFullYear();
      return allKeys.length ? Math.min(...allKeys.map(k => +k.split("-")[0])) : cur - 5;
  });
  let yearOptions = $derived(
      Array.from({ length: new Date().getFullYear() - minYear + 1 }, (_, i) => minYear + i).reverse(),
  );
  let months12 = $derived(monthsForYear(selectedYear, language));
  let existingForType = $derived(existingMonths[type] ?? []);

  let sellerBankDetails = $derived(purse.requisite?.bankDetails ?? []);
  let clientBankDetails = $derived(purse.contact?.bankDetails ?? []);

  $effect(() => {
      if (sellerBankAccountNo === null && sellerBankDetails.length > 1) {
          sellerBankAccountNo = +sellerBankDetails[0].no;
      }
      if (clientBankAccountNo === null && clientBankDetails.length > 1) {
          clientBankAccountNo = +clientBankDetails[0].no;
      }
  });

  function bankDetailsLabel(bankDetails: { summary?: string; bank_account?: string }): string {
      return bankDetails.summary?.trim() || bankDetails.bank_account?.trim() || "";
  }
</script>

<div class="modal-backdrop fade in"></div>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div
    class="modal fade in"
    style="display:block"
    role="dialog"
    aria-modal="true"
    tabindex="-1"
    onclick={busy ? undefined : onClose}
    onkeydown={() => {}}
>
  <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
  <div class="modal-dialog" role="document" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-content">
      <div class="modal-header">
        {#if !busy}
          <button type="button" class="close" onclick={onClose} aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        {/if}
          <h4 class="modal-title"><i class="fa {t.icon}"></i> {t.title}</h4>
      </div>

      <div class="modal-body">
          <div class="form-group">
          <label class="control-label" for="gen-doc-type">Document type</label>
          <select
              id="gen-doc-type"
              class="form-control"
              bind:value={type}
          >
            {#each types as dt}
              <option value={dt.id}>{dt.label}</option>
            {/each}
          </select>
        </div>

        {#if sellerBankDetails.length > 1}
          <div class="form-group">
            <label class="control-label" for="gen-seller-bank-account">Customer bank account</label>
            <select
                id="gen-seller-bank-account"
                class="form-control"
                bind:value={sellerBankAccountNo}
            >
              {#each sellerBankDetails as bd (bd.no)}
                <option value={+bd.no}>{bankDetailsLabel(bd)}</option>
              {/each}
            </select>
          </div>
        {/if}

        {#if clientBankDetails.length > 1}
          <div class="form-group">
            <label class="control-label" for="gen-client-bank-account">Contractor bank account</label>
            <select
                id="gen-client-bank-account"
                class="form-control"
                bind:value={clientBankAccountNo}
            >
              {#each clientBankDetails as bd (bd.no)}
                <option value={+bd.no}>{bankDetailsLabel(bd)}</option>
              {/each}
            </select>
          </div>
        {/if}

          <div class="form-group">
          <p class="form-row-label">Period</p>
          <select
              class="form-control"
              style="width:auto;display:inline-block;margin-bottom:8px"
              bind:value={selectedYear}
          >
            {#each yearOptions as y}
              <option value={y}>{y}</option>
            {/each}
          </select>
          <div class="month-picker">
            {#each months12 as m}
              {@const exists = existingForType.includes(m.key)}
                {@const isOn = month === m.key}
                <button
                    type="button"
                    class="month-opt {isOn ? 'is-on' : ''} {exists ? 'has-existing' : ''}"
                    onclick={() => { selectedMonthNum = +m.key.split("-")[1]; }}
                    title={exists ? 'A document already exists for this month' : ''}
                >
                <span class="m-label">{m.label}</span>
                    {#if exists}<span class="m-dot" title="Document exists"></span>{/if}
              </button>
            {/each}
          </div>
        </div>

        {#if willReplace}
          <div class="modal-warn">
            <i class="fa fa-exclamation-triangle"></i>
            <div>
              <strong>A document already exists for this type and period.</strong>
              <div>Proceeding will <strong>replace</strong> the existing document. This action cannot be undone.</div>
            </div>
          </div>
        {/if}

        {#if mode === 'preview'}
          {#if type === 'internal_invoice'}
            <div class="modal-warn">
              <i class="fa fa-exclamation-triangle"></i>
              <div>
                  An <strong>Internal invoice</strong> documents cannot be previwed.
              </div>
            </div>
          {:else}
            <div class="modal-note modal-note-info">
              <i class="fa fa-eye"></i>
              Preview mode — the generated document will be shown but <strong>not saved</strong>.
            </div>
          {/if}
        {/if}

          {#if busy}
          <div class="modal-progress">
            <div class="modal-progress-text">
              <span class="spinner spinner-lg"></span>
              <div>
                <strong>{t.btnBusy}</strong>
                <div class="progress-sub">This can take up to 30 seconds. Please don't close this window.</div>
              </div>
            </div>
            <div class="progress-bar-wrap">
              <div class="progress-bar-inner" style="width: {progress}%"></div>
            </div>
          </div>
        {/if}
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick={onClose} disabled={busy}>
          {busy ? 'Working…' : 'Cancel'}
        </button>
        <button
            type="button"
            class="btn {willReplace ? 'btn-warning' : 'btn-primary'}"
            onclick={() => onSubmit({
                type,
                month,
                willReplace,
                mode,
                client_bank_account_no: clientBankAccountNo ?? undefined,
                seller_bank_account_no: sellerBankAccountNo ?? undefined,
            })}
            disabled={busy || type === 'internal_invoice'}
        >
          {#if busy}
            <span class="spinner spinner-on-btn"></span> {t.btnBusy}
          {:else}
            <i class="fa {t.icon}"></i> {t.btn}
          {/if}
        </button>
      </div>
    </div>
  </div>
</div>

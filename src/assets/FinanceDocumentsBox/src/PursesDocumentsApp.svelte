<script lang="ts">
  import { untrack } from "svelte";
  import type { Doc, Purse, PursesDocumentsProps } from "./types";
  import { docMonthKey } from "./data";
  import { initI18n } from "./i18n";
  import { permissions } from "./permissions";
  import { useToast } from "./composables/useToast.svelte";
  import { useDocumentFilters } from "./composables/useDocumentFilters.svelte";
  import { useDocumentGeneration } from "./composables/useDocumentGeneration.svelte";
  import { usePurseCreation } from "./composables/usePurseCreation.svelte";
  import PurseTabs from "./components/PurseTabs.svelte";
  import PurseSummary from "./components/PurseSummary.svelte";
  import PurseSettings from "./components/PurseSettings.svelte";
  import DocumentsPanel from "./components/DocumentsPanel.svelte";
  import GenerateModal from "./components/GenerateModal.svelte";
  import PreviewResultModal from "./components/PreviewResultModal.svelte";
  import ConfirmReplaceModal from "./components/ConfirmReplaceModal.svelte";

  let {
      language,
      permissions: permKeys = [],
      purses: initialPurses = [],
      currencies: initialCurrencies = [],
      documentTypes: allTypes = [],
  }: PursesDocumentsProps = $props();

  initI18n(() => language);

  untrack(() => permissions.init(permKeys));
  let canPreviewAndGenerate = permissions.can("document.generate") && !permissions.can("has-own-seller");
  let canAddPurse = permissions.can("purse.update") && permissions.can("owner-staff");

  // State is scoped per purse so tab switching preserves filters/sort/page.
  const seedPurses = untrack(() => initialPurses.length ? initialPurses : []);
  let activeId = $state<string>(seedPurses[0].id);
  let purses = $state<Purse[]>([...seedPurses]);
  let currencies = $state(untrack(() => [...initialCurrencies]));
  let docsByPurse = $state<Record<string, Doc[]>>(
      Object.fromEntries(seedPurses.map(p => [p.id, [...p.documents]])),
  );

  let purse = $derived(purses.find(p => p.id === activeId)!);
  let availableCurrencies = $derived(
      currencies.filter(c => !purses.some(p => p.currency === c.id)),
  );
  let minDocYear = $derived.by(() => {
      const years = Object.values(docsByPurse).flat().map(d => +docMonthKey(d.date).split("-")[0]);
      return years.length ? Math.min(...years) : new Date().getFullYear() - 3;
  });

  const toast = useToast();
  const docFilters = useDocumentFilters(
      () => docsByPurse[activeId] ?? [],
      () => activeId,
      () => language,
  );
  const generation = useDocumentGeneration(
      () => docsByPurse[activeId] ?? [],
      (docs) => {
          docsByPurse[activeId] = docs;
      },
      toast.show,
      () => purse,
  );

  function handleStateRefresh(state: PursesDocumentsProps) {
      purses = [...state.purses];
      docsByPurse = Object.fromEntries(state.purses.map(p => [p.id, [...p.documents]]));
      currencies = [...state.currencies];
      if (state.purses.length > 0) activeId = state.purses[state.purses.length - 1].id;
  }

  const purseCreation = usePurseCreation(() => purse, handleStateRefresh, toast.show);

  function handleSettingChange(field: string, value: string) {
      purses = purses.map(p => {
          if (p.id !== activeId) return p;
          if (field === "contact") return { ...p, contact: { ...p.contact, name: value } };
          return { ...p, [field]: value };
      });
      toast.show(`Updated ${field === "requisite" ? "requisite" : "contact"}`);
  }

  function handleRecharge() {
      toast.show(`Opening top-up flow for ${purse.currency.toUpperCase()}`);
  }

</script>

<div class="accounts-block nav-tabs-custom">

  <PurseTabs
      {purses}
      {activeId}
      onChange={(id) => (activeId = id)}
      {language}
      {canAddPurse}
      currencies={availableCurrencies}
      {purse}
      {purseCreation}
  />

  <PurseSummary {purse} onRecharge={handleRecharge}/>

  <PurseSettings {purse} onChange={handleSettingChange}/>

  <DocumentsPanel
      pagedDocs={docFilters.pagedDocs}
      filters={docFilters.filters}
      typeOptions={docFilters.typeOptions}
      pageCount={docFilters.pageCount}
      totalFiltered={docFilters.filtered.length}
      totalBase={docFilters.totalDocs}
      activeChips={docFilters.activeChips}
      {language}
      {minDocYear}
      busyIds={generation.busyRowIds}
      {canPreviewAndGenerate}
      onRowAction={generation.handleRowAction}
      onSearch={docFilters.setSearch}
      onTypeFilter={docFilters.setTypeFilter}
      onDateRange={docFilters.setDateRange}
      onPage={docFilters.setPage}
      onSort={docFilters.handleSort}
      onClearFilters={docFilters.handleClearFilters}
      onOpenPreview={generation.openPreview}
      onOpenUpdate={generation.openUpdate}
  />
</div>

{#if generation.modal}
  <GenerateModal
      mode={generation.modal.kind}
      initial={generation.modal.initial}
      types={allTypes}
      existingMonths={docFilters.existingMonths}
      busy={!!generation.modal.busy}
      progress={generation.modal.progress ?? 0}
      onClose={generation.closeModal}
      onSubmit={generation.handleSubmit}
      {language}
      {purse}
  />
{/if}

{#if generation.pendingUpdate}
  <ConfirmReplaceModal
      doc={generation.pendingUpdate}
      onClose={generation.cancelUpdate}
      onConfirm={generation.applyUpdate}
      {language}
  />
{/if}

{#if generation.previewResult}
  <PreviewResultModal
      files={generation.previewResult.files}
      onClose={generation.closePreview}
  />
{/if}

<script lang="ts">
  import type { Contact, Purse, Requisite, SelectOption } from "../types";
  import { purseSettingsApi } from "../api";
  import { useAsync } from "../composables/useAsync.svelte";
  import SettingField from "./SettingField.svelte";

  let { purse, onChange }: {
      purse: Purse;
      onChange: (field: string, value: string) => void;
  } = $props();

  // Contacts: lazy load on dropdown open
  let contacts = $derived.by(() =>
      useAsync(() => purseSettingsApi.getContacts(purse.client_id), { lazy: true }),
  );

  let requisiteData = $state<Requisite[]>([]);
  let requisiteLoading = $state(false);
  let fetchSeq = 0;

  $effect(() => {
      purse.seller_id; // reset on account switch
      requisiteData = [];
      requisiteLoading = false;
  });

  async function fetchRequisites(query?: string) {
      const seq = ++fetchSeq;
      requisiteLoading = true;
      try {
          const data = await purseSettingsApi.getRequisites(purse.seller_id, query);
          if (seq === fetchSeq) requisiteData = data;
      } finally {
          if (seq === fetchSeq) requisiteLoading = false;
      }
  }

  // Selections keyed by purse id
  let selectedContacts = $state<Record<string, Contact>>({});
  let selectedRequisites = $state<Record<string, Requisite>>({});

  function requisiteLabel(r: { name: string; organization: string }): string {
      return r.organization ? `${r.organization} / ${r.name}` : r.name;
  }

  let contactOptions = $derived<SelectOption[]>(
      (contacts.data ?? []).map(c => ({
          value: c.name,
          label: c.name,
          sub: c.email,
          icon: "fa-user",
      })),
  );

  let requisiteOptions = $derived<SelectOption[]>(
      requisiteData.map(r => ({
          value: requisiteLabel(r),
          label: requisiteLabel(r),
          icon: "fa-university",
      })),
  );

  let contactDisplayValue = $derived(
      selectedContacts[purse.id]?.name ?? purse.contact.name,
  );

  let requisiteDisplayValue = $derived(
      purse.id in selectedRequisites
          ? requisiteLabel(selectedRequisites[purse.id])
          : purse.requisite ? requisiteLabel(purse.requisite) : "",
  );

  function saveContact(value: string) {
      const contact = contacts.data?.find(c => c.name === value);
      if (contact) {
          selectedContacts = { ...selectedContacts, [purse.id]: contact };
          purseSettingsApi.updateContact(purse.id, contact.id);
      }
      onChange("contact", value);
  }

  function saveRequisite(value: string) {
      const requisite = requisiteData.find(r => requisiteLabel(r) === value);
      if (requisite) {
          selectedRequisites = { ...selectedRequisites, [purse.id]: requisite };
          purseSettingsApi.updateRequisite(purse.id, requisite.id);
      }
      onChange("requisite", value);
  }
</script>

<div class="purse-settings">
  <SettingField
      label="Contact"
      value={contactDisplayValue}
      icon="fa-user-o"
      options={contactOptions}
      loading={contacts.loading}
      onOpen={() => { if (!contacts.data) contacts.refetch(); }}
      onSave={saveContact}
      createNewUrl="/client/contact/create"
  />
  <SettingField
      label="Requisite"
      value={requisiteDisplayValue}
      icon="fa-university"
      options={requisiteOptions}
      loading={requisiteLoading}
      searchable={true}
      onOpen={() => { if (requisiteData.length === 0) fetchRequisites(); }}
      onSearch={(q) => fetchRequisites(q || undefined)}
      onSave={saveRequisite}
      createNewUrl="/finance/requisite/create"
  />
</div>

<script lang="ts">
  import type { Contact, Purse, Requisite, SelectOption } from "../types";
  import { purseSettingsApi } from "../api";
  import { useAsync } from "../async.svelte";
  import SettingField from "./SettingField.svelte";

  let { account, onChange }: {
      account: Purse;
      onChange: (field: string, value: string) => void;
  } = $props();

  let contacts = $derived.by(() => useAsync(() => purseSettingsApi.getContacts(account.client_id), { lazy: true }));
  let requisites = $derived.by(() => useAsync(() => purseSettingsApi.getRequisites(account.seller_id), { lazy: true }));

  // Selections are keyed by purse id — no $effect needed, no race with onChange
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
      (requisites.data ?? []).map(r => ({
          value: requisiteLabel(r),
          label: requisiteLabel(r),
          icon: "fa-university",
      })),
  );

  let contactDisplayValue = $derived(
      selectedContacts[account.id]?.name ?? account.contact.name,
  );

  let requisiteDisplayValue = $derived(
      account.id in selectedRequisites
          ? requisiteLabel(selectedRequisites[account.id])
          : account.requisite ? requisiteLabel(account.requisite) : "",
  );

  function saveContact(value: string) {
      const contact = contacts.data?.find(c => c.name === value);
      if (contact) {
          selectedContacts = { ...selectedContacts, [account.id]: contact };
          purseSettingsApi.updateContact(account.id, contact.id);
      }
      onChange("contact", value);
  }

  function saveRequisite(value: string) {
      const requisite = requisites.data?.find(r => requisiteLabel(r) === value);
      if (requisite) {
          selectedRequisites = { ...selectedRequisites, [account.id]: requisite };
          purseSettingsApi.updateRequisite(account.id, requisite.id);
      }
      onChange("paymentDetails", value);
  }
</script>

<div class="acct-settings">
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
      loading={requisites.loading}
      onOpen={() => { if (!requisites.data) requisites.refetch(); }}
      onSave={saveRequisite}
      createNewUrl="/finance/requisite/create"
  />
</div>

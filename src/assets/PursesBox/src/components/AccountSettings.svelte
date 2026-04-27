<script lang="ts">
  import type { Account, SelectOption } from '../types';
  import SettingField from './SettingField.svelte';

  let { account, onChange }: {
    account: Account;
    onChange: (field: string, value: string) => void;
  } = $props();

  let contactOptions = $derived<SelectOption[]>(
    [
      { value: account.contact.name, label: account.contact.name, sub: account.contact.email, icon: 'fa-user' },
      { value: 'Olivia Martinez', label: 'Olivia Martinez', sub: 'o.martinez@acme.co', icon: 'fa-user' },
      { value: 'Jonas Weber',      label: 'Jonas Weber',      sub: 'j.weber@acme.co',   icon: 'fa-user' },
      { value: 'Priya Shah',       label: 'Priya Shah',       sub: 'p.shah@acme.co',    icon: 'fa-user' },
      { value: 'Marcus Lee',       label: 'Marcus Lee',       sub: 'm.lee@acme.co',     icon: 'fa-user' },
    ].filter((v, i, arr) => arr.findIndex(x => x.value === v.value) === i)
  );

  let paymentOptions = $derived<SelectOption[]>(
    [
      { value: account.paymentDetails,         label: account.paymentDetails,         icon: 'fa-university' },
      { value: 'Chase Bank · 2847',             label: 'Chase Bank · 2847',             icon: 'fa-university' },
      { value: 'Wire transfer · SWIFT',         label: 'Wire transfer · SWIFT',         sub: 'Default for international', icon: 'fa-exchange' },
      { value: 'Stripe · card ending 4421',     label: 'Stripe · card ending 4421',     icon: 'fa-credit-card' },
    ].filter((v, i, arr) => arr.findIndex(x => x.value === v.value) === i)
  );
</script>

<div class="acct-settings">
  <SettingField
    label="Contact"
    value={account.contact.name}
    icon="fa-user-o"
    options={contactOptions}
    onSave={(v) => onChange('contact', v)}
  />
  <SettingField
    label="Payment details"
    value={account.paymentDetails}
    icon="fa-university"
    options={paymentOptions}
    onSave={(v) => onChange('paymentDetails', v)}
  />
</div>

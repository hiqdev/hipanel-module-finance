import { getContext, setContext } from "svelte";

const translations: Record<string, Record<string, string>> = {
  en: {
    balance: "Balance",
  },
  ru: {
    balance: "Баланс",
  },
};

type TFunc = (key: string) => string

const I18N_KEY = Symbol("i18n");

export function initI18n(locale: string): void {
  const lang = translations[locale] ?? translations["en"];
  setContext(I18N_KEY, (key: string): string => lang[key] ?? key);
}

export function t(key: string): string {
  return getContext<TFunc>(I18N_KEY)(key);
}

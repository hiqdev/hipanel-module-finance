import { getContext, setContext } from "svelte";

const translations: Record<string, Record<string, string>> = {
  ru: {
    "account": "счет",
    "Balance": "Баланс",
    "Top-up account balance": "Пополнить баланс",
  },
};

type TFunc = (key: string) => string;

const I18N_KEY = Symbol("i18n");

function dictionary(locale: string): Record<string, string> {
  const baseLocale = locale.split("-")[0];
  return translations[locale] ?? translations[baseLocale] ?? {};
}

export function initI18n(getLocale: () => string): void {
  setContext(I18N_KEY, (key: string): string => dictionary(getLocale())[key] ?? key);
}

export function useI18n(): TFunc {
  return getContext<TFunc>(I18N_KEY) ?? ((key: string): string => key);
}

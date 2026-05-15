import { mount as svelteMount, unmount as svelteUnmount } from "svelte";
import "./styles.css";
import PursesDocumentsApp from "./PursesDocumentsApp.svelte";
import PartyDocumentsApp from "./PartyDocumentsApp.svelte";
import type { PartyDocumentsProps, PursesDocumentsProps } from "./types";

interface FinanceDocumentsBoxApi {
  mountPursesDocuments: (el: HTMLElement, props: PursesDocumentsProps) => void;
  unmount: (el: HTMLElement) => void;
  mountPartyDocuments: (el: HTMLElement, props: PartyDocumentsProps) => void;
}

declare global {
  interface Window {
    FinanceDocumentsBox: FinanceDocumentsBoxApi;
    hipanel: any;
  }
}

const instances = new WeakMap<HTMLElement, Record<string, any>>();

function mountPursesDocuments(el: HTMLElement, props: PursesDocumentsProps): void {
  if (instances.has(el)) return;
  const component = svelteMount(PursesDocumentsApp, { target: el, props });
  instances.set(el, component);
}

function unmount(el: HTMLElement): void {
  const component = instances.get(el);
  if (!component) return;
  svelteUnmount(component);
  instances.delete(el);
}

function mountPartyDocuments(el: HTMLElement, props: PartyDocumentsProps): void {
  if (instances.has(el)) return;
  const component = svelteMount(PartyDocumentsApp, { target: el, props });
  instances.set(el, component);
}

window.FinanceDocumentsBox = { mountPursesDocuments, unmount, mountPartyDocuments };

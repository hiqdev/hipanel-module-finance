import { mount as svelteMount, unmount as svelteUnmount } from "svelte";
import './accounts.css';
import PursesBox from "./App.svelte";

export interface PursesBoxProps {
  data: Record<string, any>;
}

interface PursesBoxApi {
  mount: (el: HTMLElement, props: PursesBoxProps) => void;
  unmount: (el: HTMLElement) => void;
}

declare global {
  interface Window {
    PursesBox: PursesBoxApi;
  }
}

const instances = new WeakMap<HTMLElement, Record<string, any>>();

function mount(el: HTMLElement, props: PursesBoxProps): void {
  if (instances.has(el)) return;
  const component = svelteMount(PursesBox, { target: el, props });
  instances.set(el, component);
}

function unmount(el: HTMLElement): void {
  const component = instances.get(el);
  if (!component) return;
  svelteUnmount(component);
  instances.delete(el);
}

window.PursesBox = { mount, unmount };

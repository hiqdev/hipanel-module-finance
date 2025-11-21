export default interface Charge {
  class: string;
  object: string;
  type: string;
  quantity: number;
  sum: number;
  description?: string | null;
  time?: string | null;
}

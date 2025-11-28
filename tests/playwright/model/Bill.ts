import Charge from "./Charge";

export default interface Bill {
  client: string;
  type: string;
  currency: string;
  sum: number;
  quantity: number;
  requisite?: string;
  time?: string | null;
  description?: string | null;
  class?: string | null;
  object?: string | null;
  charges?: Array<Charge> | null;
}

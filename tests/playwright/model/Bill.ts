import Charge from "@hipanel-module-finance/model/Charge";

export default class Bill {
  public client: string;
  public type: string;
  public currency: string;
  public sum: number;
  public quantity: number;
  public time?: string | null;
  public description?: string | null;
  public class?: string | null;
  public object?: string | null;
  public charges?: Array<Charge> | null;
}

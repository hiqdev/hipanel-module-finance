import Charge from "@hipanel-module-finance/tests/models/Charge";

export default class Bill {
  public client: string;
  public type: string;
  public currency: string;
  public sum: number;
  public quantity: number;
  public time: string | null = null;
  public description: string | null = null;
  public class: string | null = null;
  public object: string | null = null;
  public charges: Array<Charge> | null = null;
}

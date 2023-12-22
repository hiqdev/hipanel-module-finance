import { map } from "lodash/collection";
import { useTotals } from "./use/totals";

export function reducer(state, {
  type,
  payload,
}) {
  switch (type) {
    case "UPDATE_COLUMNS":
      return {
        ...state,
        ...payload,
        loading: false,
      };
    case "UPDATE_MONTH":
      return {
        ...state,
        ...payload,
        loading: false,
      };
    case "UPDATE_MONTHS":
      return {
        ...state,
        ...payload,
        loading: false,
      };
    case "UPDATE_ROWS":
      return {
        ...state,
        ...payload,
        loading: false,
      };
    case "UPDATE_ROW_DETAILS":
      state.rows = map(state.rows, r => {
        if (r.id === payload.rowId) {
          r.details = payload.details;
        }

        return r;
      });

      return {
        ...state,
        ...payload,
        loading: false,
      };
    case "FETCH_INIT":
      return {
        ...state,
        loading: true,
      };
    default:
      return state;
  }
}

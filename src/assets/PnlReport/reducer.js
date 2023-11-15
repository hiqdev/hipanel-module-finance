import map from "lodash/map";

export function reducer(state, { type, payload }) {
  switch (type) {
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

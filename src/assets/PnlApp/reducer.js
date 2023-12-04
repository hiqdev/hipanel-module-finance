import { find, map } from "lodash/collection";

const summirizeRow = (months, row) => {
  let total = 0;
  if (row) {
    months.forEach(month => {
      const columnName = moment(month).format("MMM YYYY");
      total += row[columnName];
    });
  }

  return total;
}

export function reducer(state, { type, payload }) {
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
    case "UPDATE_TOTALS":
      const incomeRow = find(state.rows, ['key', 'income']);
      const expenseRow = find(state.rows, ['key', 'expense']);
      const taxRow = find(state.rows, ['key', 'tax']);
      const directExpenseRow = find(state.flatRows, ['type', 'expense,direct']);

      const directExpense = summirizeRow(state.months, directExpenseRow);
      const income = summirizeRow(state.months, incomeRow);
      const expense = summirizeRow(state.months, expenseRow);
      const taxes = summirizeRow(state.months, taxRow);

      state.total_before_taxes = income - Math.abs(expense);
      state.profit = state.total_before_taxes - Math.abs(taxes);
      state.gross_prfit_margin = (((income - Math.abs(directExpense)) / income) / 100) * 100;

      return {
        ...state,
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

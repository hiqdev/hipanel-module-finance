import { find, map, filter, forEach } from "lodash/collection";
import { isEmpty } from "lodash/lang";

const summirizeRows = (months, rows) => {
  let sum = 0;
  if (rows) {
    forEach(months, date => {
      forEach(rows, row => {
        if (!isEmpty(row.month)) {
          sum += row[moment(date).format("MMM YYYY")];
        }
      });
    });
  }

  return sum;
};

const useTotals = (rows, months) => {
  const revenuesRows = filter(rows, (entry) => entry.type.startsWith("revenues,"));
  const expensesRows = filter(rows, (entry) => entry.type === "expenses,");
  const directExpensesRows = filter(rows, (entry) => entry.type.includes(",direct_expenses"));
  const taxWithoutVatRows = filter(rows, (entry) => entry.type.startsWith("tax,") && !entry.type.includes("tax,vat"));

  const revenues = summirizeRows(months, revenuesRows);
  const expenses = summirizeRows(months, expensesRows);
  const directExpenses = summirizeRows(months, directExpensesRows);
  const taxes = summirizeRows(months, taxWithoutVatRows);

  const total_before_taxes = revenues - Math.abs(expenses);
  const profit = total_before_taxes - Math.abs(taxes);
  const gross_profit_margin = (((revenues - Math.abs(directExpenses)) / revenues) / 100) * 100;

  return {
    total_before_taxes,
    profit,
    gross_profit_margin,
  };
};

export { useTotals };

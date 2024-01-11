import { find, map, filter, forEach } from "lodash/collection";
import { isEmpty, toNumber } from "lodash/lang";

const summirizeRows = (months, rows) => {
  let sum = 0;
  if (rows) {
    forEach(months, date => {
      forEach(rows, row => {
        if (!isEmpty(row.month)) {
          const total = row[moment(date).format("MMM YYYY")] ?? 0;
          sum += toNumber(total);
        }
      });
    });
  }

  return sum;
};

const useTotals = (flatRows, months) => {
  const revenuesRows = filter(flatRows, (entry) => entry.type.startsWith("revenues,"));
  const expensesRows = filter(flatRows, (entry) => entry.type.startsWith("expenses,"));
  // const directExpensesRows = filter(rows, (entry) => entry.type.includes(",direct_expenses"));
  const taxWithoutVatRows = filter(flatRows, (entry) => entry.type.startsWith("tax,") && !entry.type.includes("tax,vat"));

  const revenues = summirizeRows(months, revenuesRows);
  const expenses = summirizeRows(months, expensesRows);
  // const directExpenses = summirizeRows(months, directExpensesRows);
  const taxes = summirizeRows(months, taxWithoutVatRows);

  const total_before_taxes = revenues - Math.abs(expenses);
  const profit = total_before_taxes - Math.abs(taxes);
  // const gross_profit_margin = (((revenues - Math.abs(directExpenses)) / revenues) / 100) * 100;

  return {
    total_before_taxes,
    profit,
    // gross_profit_margin,
  };
};

export { useTotals };

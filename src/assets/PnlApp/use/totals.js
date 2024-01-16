import { find, map, filter, forEach } from "lodash/collection";
import { isEmpty, toNumber } from "lodash/lang";

const summirizeRows = (rows, dates) => {
  let sum = 0;
  if (rows) {
    forEach(dates, date => {
      forEach(rows, row => {
        const monthWithYear = moment(date).format("MMM YYYY");
        if (row.isLeaf) {
          const total = row[monthWithYear] ?? 0;
          sum += toNumber(total);
        }
      });
    });
  }

  return sum;
};

const useTotals = (flatRows, dates) => {
  const leafsOnly = filter(flatRows, (entry) => entry.isLeaf === true);
  const revenuesRows = filter(leafsOnly, (entry) => entry.type.startsWith("revenues,"));
  const expensesRows = filter(leafsOnly, (entry) => entry.type.startsWith("expenses,"));
  // const directExpensesRows = filter(rows, (entry) => entry.type.includes(",direct_expenses"));
  const taxWithoutVatRows = filter(leafsOnly, (entry) => entry.type.startsWith("tax,") && !entry.type.includes("tax,vat"));

  const revenues = summirizeRows(revenuesRows, dates);
  const expenses = summirizeRows(expensesRows, dates);
  // const directExpenses = summirizeRows(directExpensesRows, dates);
  const taxes = summirizeRows(taxWithoutVatRows, dates);

  const total_before_taxes = revenues - Math.abs(expenses);
  const profit = total_before_taxes - Math.abs(taxes);
  // const gross_profit_margin = (((revenues - Math.abs(directExpenses)) / revenues) / 100) * 100;

  return {
    total_before_taxes,
    taxes: Math.abs(taxes),
    profit,
    // gross_profit_margin,
  };
};

export { useTotals };

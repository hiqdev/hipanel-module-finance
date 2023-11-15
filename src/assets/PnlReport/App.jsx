import React, { useState, useEffect, useReducer } from "react";
import "./App.css";
import find from "lodash/find";
import Paper from "@mui/material/Paper";
import LinearProgress from "@mui/material/LinearProgress";
import Link from "@mui/material/Link";
import TableCell from "@mui/material/TableCell";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import Box from "@mui/material/Box";
import InputLabel from "@mui/material/InputLabel";
import MenuItem from "@mui/material/MenuItem";
import FormControl from "@mui/material/FormControl";
import Button from "@mui/material/Button";
import Select from "@mui/material/Select";
import Pager from "@mui/material/Paper";
import LayoutGrid from "@mui/material/Grid";
import Fab from "@mui/material/Fab";
import HomeIcon from "@mui/icons-material/KeyboardArrowLeft";
import {
  SearchState,
  FilteringState,
  IntegratedFiltering,
  DataTypeProvider,
  SummaryState,
  IntegratedSummary,
  PagingState,
  IntegratedPaging,
  RowDetailState,
} from "@devexpress/dx-react-grid";
import {
  Grid,
  Table,
  TableHeaderRow,
  TableFilterRow,
  Toolbar,
  SearchPanel,
  TableSummaryRow,
  PagingPanel,
  ColumnChooser,
  TableColumnVisibility,
  TableRowDetail,
} from "@devexpress/dx-react-grid-material-ui";

import { Loading } from "./components/Loading";
import { reducer } from "./reducer";

const initialState = {
  rows: [], loading: false, ...__initial_state,
};

const Theme = createTheme({
  typography: {
    // fontSize: 20,
    fontFamily: "\"Helvetica Neue\", Helvetica, Arial, sans-serif",
  },
});

const buildQueryString = (row, monthName) => {
  const month = moment().month(monthName).format("YYYY-MM-01");

  return `index?PnlSearch[month]=${month}&PnlSearch[type]=${row.type}`;
};

const SumFormatter = ({ value, row, column }) => {
  const sum = value / 100;
  if (sum === 0) {
    return (<span style={{ color: "lightgray" }}>
      {sum.toLocaleString("en-US", { style: "currency", currency: "EUR" })}
    </span>);
  }

  let color;
  if (row) {
    color = (sum > 0) ? "success.main" : "error.main";
  } else {
    return sum.toLocaleString("uk-UA", { maximumFractionDigits: 2 });
  }

  return (<Link href={buildQueryString(row, column.name)} target={"_blank"} color={color}>{sum.toFixed(2)}</Link>);
};

const CurrencyTypeProvider = props => (<DataTypeProvider formatterComponent={SumFormatter} {...props} />);

const initYear = new Date().getFullYear();
const initColumns = [
  { name: "section", title: "Section" }, // section
  { name: "direction", title: "Direction" }, // direction
  { name: "set", title: "Set" }, { name: "item", title: "Item" }, { name: "detail", title: "Detail" },
];
const initCurrencyColumns = [];
const initColumnExtensions = [];
const initTotalSummaryColumns = [];
const date = moment(initYear).startOf("year");
while (date < moment(initYear).endOf("year")) {
  initColumns.push({
    name: date.format("MMM"), title: date.format("MMM"),
  });
  initCurrencyColumns.push(date.format("MMM"));
  initTotalSummaryColumns.push({
    columnName: date.format("MMM"), type: "sum",
  });
  initColumnExtensions.push({
    columnName: date.format("MMM"), align: "right",
  });
  date.add(1, "month");
}

const SelectYear = ({ handleChange, value }) => (<Box>
  <FormControl variant={"standard"} sx={{ m: 1, minWidth: 120 }}>
    <InputLabel id="year-select-label">Year</InputLabel>
    <Select
      labelId="year-select-label"
      id="year-select"
      value={value}
      label="Year"
      onChange={handleChange}
    >
      {__initial_state.years.map(year => (<MenuItem key={year} value={year}>{year}</MenuItem>))}
    </Select>
  </FormControl>
</Box>);

const DropdownFilterCell = ({ filter, onFilter, column }) => {
  const filterValues = __initial_state[column.name + "s"] || [];

  return (<TableCell sx={{ width: "100%", p: 1 }}>
    <FormControl sx={{ m: 1, minWidth: 120 }} size="small">
      <Select
        value={filter ? filter.value : ""}
        onChange={e => onFilter(e.target.value ? { value: e.target.value } : null)}
      >
        <MenuItem value="">
          <em>None</em>
        </MenuItem>
        {filterValues.map(value => (<MenuItem key="value" value={value || null}>{value}</MenuItem>))}
      </Select>
    </FormControl>
  </TableCell>);
};

const FilterCell = (props) => {
  if (["section", "direction", "set", "item", "detail"].includes(props.column.name)) {
    return (<DropdownFilterCell {...props} />);
  }

  return "";
};

const RowDetail = ({ row }) => {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const abortController = new AbortController();
    const fetchData = async () => {
      setLoading(true);
      // const url = new URL("fetch-data", window.location.href);
      // url.searchParams.set("year", year);
      // const response = await fetch(url, { signal: abortController.signal });
      // const rows = await response.json();
      console.log("calling");
      const result = await resolveAfter2Seconds();
      console.log(result);
      row.details = 1;
      setRows([{ id: "123", mt: "321" }]);
      setLoading(false);
    };
    if (row.details === null) {
      fetchData();
    }

    return () => {
      abortController.abort();
    };
  }, [row.id]);

  if (loading || row.details === null) {
    return (<Box sx={{ width: "100%" }}>
      <LinearProgress color={"inherit"}/>
    </Box>);
  }

  return (<div>
    Details for
    {" "}
    {row.id}
    {" "}
    from
    {" "}
    {row.type}
  </div>);
};

function resolveAfter2Seconds() {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve("resolved");
    }, 1000);
  });
};


export default () => {
  const [columns] = useState(initColumns);
  const [year, setYear] = useState("");
  const [currencyColumns] = useState(initCurrencyColumns);
  const [tableColumnExtensions] = useState(initColumnExtensions);
  const [filteringStateColumnExtensions] = useState([
    { columnName: "section", filteringEnabled: true },
    { columnName: "direction", filteringEnabled: true },
    { columnName: "set", filteringEnabled: true },
    { columnName: "item", filteringEnabled: true },
    { columnName: "detail", filteringEnabled: true },
  ]);
  const [totalSummaryItems] = useState(initTotalSummaryColumns);
  const [pageSizes] = useState([50, 100, 200, 500, 0]);
  const [defaultHiddenColumnNames] = useState([]);
  const [expandedRowIds, setExpandedRowIds] = useState([]);
  const [state, dispatch] = useReducer(reducer, initialState);

  useEffect(() => {
    const abortController = new AbortController();
    const fetchData = async () => {
      dispatch({ type: "FETCH_INIT" });
      const url = new URL("fetch-data", window.location.href);
      url.searchParams.set("year", year);
      const response = await fetch(url, { signal: abortController.signal });
      const rows = await response.json();
      dispatch({ type: "UPDATE_ROWS", payload: { rows } });
    };
    if (year) {
      fetchData();
    }

    return () => {
      abortController.abort();
    };
  }, [year]);
  const redirectToDashboard = () => {
    window.location.href = "/finance/pnl/index";
  };

  const { rows, loading } = state;

  return (
    <ThemeProvider theme={Theme}>
      <LayoutGrid container spacing={3} justifyContent={"flex-start"} direction={"row"} alignItems={"flex-end"}>
        <LayoutGrid item>
          <Fab size={"small"} onClick={redirectToDashboard}>
            <HomeIcon/>
          </Fab>
        </LayoutGrid>
        <LayoutGrid item>
          <SelectYear
            value={year}
            handleChange={(e) => {
              setYear(e.target.value);
            }}
          />
        </LayoutGrid>
      </LayoutGrid>
      <Paper elevation={0}>
        <Grid rows={rows} columns={columns} getRowId={(row) => Object.values(row).join("")}>
          <CurrencyTypeProvider for={currencyColumns}/>
          <SearchState defaultValue=""/>
          <FilteringState
            defaultFilters={[]}
            columnFilteringEnabled={false}
            columnExtensions={filteringStateColumnExtensions}
          />
          <IntegratedFiltering/>
          <SummaryState totalItems={totalSummaryItems}/>
          <IntegratedSummary/>
          <PagingState
            defaultCurrentPage={0}
            defaultPageSize={50}
          />
          <IntegratedPaging/>
          <RowDetailState
            expandedRowIds={expandedRowIds}
            onExpandedRowIdsChange={setExpandedRowIds}
          />
          <Table columnExtensions={tableColumnExtensions}/>
          <TableHeaderRow/>
          <TableSummaryRow/>
          <TableFilterRow cellComponent={FilterCell}/>
          <TableColumnVisibility
            defaultHiddenColumnNames={defaultHiddenColumnNames}
          />
          <Toolbar/>
          <ColumnChooser/>
          <SearchPanel/>
          <PagingPanel
            pageSizes={pageSizes}
          />
          {/*<TableRowDetail*/}
          {/*  contentComponent={RowDetail}*/}
          {/*/>*/}
        </Grid>
        {loading && <Loading/>}
      </Paper>
    </ThemeProvider>
  );
};

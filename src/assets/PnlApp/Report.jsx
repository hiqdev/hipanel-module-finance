import React, { useState, useReducer, useEffect, useMemo } from "react";
import {
  DashboardOutlined,
  HomeOutlined,
  CalculatorOutlined,
  DownloadOutlined,
} from "@ant-design/icons";
import { Button, Switch, Table, Space, Layout, Menu, theme, ConfigProvider, Typography, TreeSelect } from "antd";
import { green, red } from "@ant-design/colors";
import { Excel } from "antd-table-saveas-excel";
import Spin from "antd/lib/spin";
import { map, orderBy } from "lodash/collection";
import { isEmpty } from "lodash/lang";

import PnlLayout from "./components/PnlLayout";
import { reducer } from "./reducer";
import { useTotals } from "./use/totals";

const {
  Header,
  Content,
  Footer,
} = Layout;
const {
  Link,
  Text,
} = Typography;
const { SHOW_PARENT } = TreeSelect;

const initialState = {
  rows: [],
  flatRows: [],
  months: [],
  loading: false,
  total_before_taxes: 0,
  profit: 0,
  gross_profit_margin: 0,
  ...__initial_state,
};

const headerStyle = {
  textAlign: "center",
  color: "#fff",
  height: 64,
  paddingInline: 16,
  lineHeight: "64px",
  backgroundColor: "#ffffff",
  top: 0,
  zIndex: 1,
  width: "100%",
  display: "flex",
  alignItems: "center",
};

const initialColumns = [
  {
    key: "type",
    dataIndex: "type",
    title: "Type",
    render: (value, row, idx) => (<Text>{row.type_label}</Text>),
    excelRender: (value, row, index) => {
      return row.type;
    },
    // filters: __initial_state.filtersTree,
    // filterMode: "tree",
    // filterSearch: true,
    // onFilter: (value, record) => {
    //   const t = record.type.includes(value);
    //
    //   return t;
    // },
    // width: "40%",
  },
];

const buildQueryString = (row, date) => {
  const month = moment(date).format("YYYY-MM-01");

  return `index?PnlSearch[month]=${month}&PnlSearch[type]=${row.type}`;
};

const Report = () => {
  const { token: { colorBgContainer } } = theme.useToken();
  const [columns, setColumns] = useState(initialColumns);
  const [state, dispatch] = useReducer(reducer, initialState);

  const {
    monthTreeData,
    rows,
    flatRows,
    loading,
    months,
  } = state;

  const updateColumns = () => {
    const addColumns = [];
    months.forEach(date => {
      const month = moment(date).format("MMM YYYY");
      addColumns.push({
        key: moment(date).format('M'),
        dataIndex: month,
        title: month,
        align: "right",
        render: (value, row, idx) => {
          let sum = 0;
          if (!value) {
            return (
              <Text type={"secondary"}>{sum.toFixed(2)}</Text>
            );
          }
          sum = value / 100;
          const color = (sum > 0) ? green.primary : red.primary;

          return (
            <Link href={buildQueryString(row, date)} target={"_blank"} style={{ color }}>
              {sum.toLocaleString("uk-UA", {
                maximumFractionDigits: 2,
                minimumFractionDigits: 2,
              })}
            </Link>);
        },
      });
    });
    const orderedColumns = orderBy(addColumns, (col) => moment().month(col.key).unix(), ["asc"]);
    orderedColumns.push({
      key: "row_total",
      dataIndex: "row_total",
      title: "Total for period",
      align: "right",
      render: (value, row, idx) => {
        const totals = [];
        months.forEach(date => {
          const month = moment(date).format("MMM YYYY");
          totals.push(row[month]);
        });
        const sum = totals.length ? totals.reduce((acc, value) => acc + value) / 100 : 0;

        return (
          <Text type={"secondary"} strong={true}>
            {sum.toLocaleString("uk-UA", {
              maximumFractionDigits: 2,
              minimumFractionDigits: 2,
            })}
          </Text>
        );
      },
    });
    setColumns([...initialColumns, ...orderedColumns]);
  };

  const onMonthsChange = (newMonths) => {
    dispatch({
      type: "UPDATE_MONTHS",
      payload: { months: newMonths },
    });
  };

  useEffect(() => {
    const abortController = new AbortController();
    const fetchRows = async () => {
      dispatch({ type: "FETCH_INIT" });
      const endpoint = new URL("fetch-rows", window.location.href);
      endpoint.searchParams.set("months", months);
      const response = await fetch(endpoint, { signal: abortController.signal });
      const data = await response.json();
      dispatch({
        type: "UPDATE_ROWS",
        payload: { ...data },
      });
      // dispatch({ type: "UPDATE_TOTALS" });
    };

    fetchRows();
    updateColumns();

    return () => {
      abortController.abort();
    };
  }, [months]);
  const ReportHeader = () => (
    <Space>
      <TreeSelect
        treeData={monthTreeData}
        value={months}
        onChange={onMonthsChange}
        treeCheckable={true}
        placeholder={"Select the months"}
        allowClear={true}
        showSearch={false}
        style={{
          minWidth: "15em",
          maxWidth: "100em",
        }}
        treeDefaultExpandAll={true}
      />
      <Button
        type="primary"
        icon={<DownloadOutlined/>}
        disabled={!flatRows.length}
        onClick={() => {
          if (flatRows.length) {
            const excel = new Excel();
            excel.addSheet("report").addColumns(columns).addDataSource(flatRows).saveAs("pnl-report.xlsx");
          }
        }}
      >
        Microsoft Excel (.xlsx)
      </Button>
    </Space>
  );

  return (
    <ConfigProvider
      theme={{
        token: {
          colorBgContainer: "#fff",
        },
      }}
    >
      <Spin spinning={loading} delay={100}>
        <PnlLayout page={"report"}>
          <Header style={headerStyle}>
            <Space>
              <TreeSelect
                treeData={monthTreeData}
                value={months}
                onChange={onMonthsChange}
                treeCheckable={true}
                placeholder={"Select the months"}
                allowClear={true}
                showSearch={false}
                style={{
                  minWidth: "15em",
                  maxWidth: "100em",
                }}
                treeDefaultExpandAll={true}
              />
              <Button
                type="primary"
                icon={<DownloadOutlined/>}
                disabled={!flatRows.length}
                onClick={() => {
                  if (flatRows.length) {
                    const excel = new Excel();
                    excel.addSheet("report").addColumns(columns).addDataSource(flatRows).saveAs("pnl-report.xlsx");
                  }
                }}
              >
                Microsoft Excel (.xlsx)
              </Button>
            </Space>
          </Header>
          <Content className="site-layout" style={{ margin: "1em 16px 0" }}>
            <Table
              rowKey={(row) => row.key}
              columns={columns}
              dataSource={rows}
              pagination={false}
              bordered
              size={"small"}
              summary={(pageData) => {
                if (isEmpty(pageData)) {
                  return;
                }
                const totalsByMonth = {};
                months.forEach(date => {
                  const monthAndYear = moment(date).format("MMM YYYY");
                  totalsByMonth[monthAndYear] = useTotals(flatRows, [date])
                  totalsByMonth[monthAndYear]['month'] = monthAndYear;
                });
                const orderedTotals = orderBy(totalsByMonth, (row) => moment(row.month, 'MMM YYYY').format('M'), ["asc"]);
                const totalItems = {
                  total_before_taxes: "Total before taxes",
                  profit: "Net profit",
                  // gross_profit_margin: "Gross profit margin",
                };
                const summirize = (key) => {
                  if (key === 'gross_profit_margin') {
                    return '';
                  }
                  let amount = 0;
                  map(orderedTotals, (totals) => {
                    amount += totals[key];
                  });

                  return (amount / 100).toLocaleString("uk-UA", { style: "currency", currency: "EUR" });
                };

                return (
                  <>
                    {map(totalItems, (label, key) => (
                      <Table.Summary.Row key={key}>
                        <Table.Summary.Cell index={0}>
                          <Text>
                            {label}
                          </Text>
                        </Table.Summary.Cell>
                        {map(orderBy(months, (date) => moment(date).unix(), ["asc"]), (date) => {
                          const monthNo = moment(date).format('M');
                          const totals = useTotals(flatRows, [date]);
                          let amount = totals[key];
                          if (key === "gross_profit_margin") {
                            amount = isNaN(amount) ? 0 : amount.toLocaleString("uk-UA", { style: "percent" });
                          } else {
                            amount = (amount / 100).toLocaleString("uk-UA", { style: "currency", currency: "EUR" });
                          }

                          return (
                            <Table.Summary.Cell index={monthNo} align={"right"} key={monthNo}>
                              <Text>{amount}</Text>
                            </Table.Summary.Cell>
                          );
                        })}
                        <Table.Summary.Cell index={13} align={"right"}>
                          <Text type={"secondary"} strong={true}>
                            {summirize(key)}
                          </Text>
                        </Table.Summary.Cell>
                      </Table.Summary.Row>
                    ))}
                  </>
                );
              }}
            />
          </Content>
        </PnlLayout>
      </Spin>
    </ConfigProvider>
  );
};

export default Report;

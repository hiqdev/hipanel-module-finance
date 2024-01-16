import React, { useState, useReducer, useEffect, useMemo } from "react";
import {
  DashboardOutlined,
  HomeOutlined,
  CalculatorOutlined,
  DownloadOutlined,
  ArrowUpOutlined,
  ArrowDownOutlined,
} from "@ant-design/icons";
import { Button, Switch, Table, Space, Layout, Menu, theme, ConfigProvider, Typography, TreeSelect } from "antd";
import { green, red } from "@ant-design/colors";
import { Excel } from "antd-table-saveas-excel";
import Spin from "antd/lib/spin";
import styled from "styled-components";
import { map, orderBy } from "lodash/collection";
import { isEmpty, toNumber } from "lodash/lang";
import { hasIn } from "lodash/object";

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

const isPositive = (sum) => sum > 0;

const StyledDiff = styled.div`
  color: ${({ $diff }) => $diff === 0 ? "#00000040" : "#000000A6" };
  display: ${({ $show }) => $show ? "block" : "none"};
  &:before {
    color: ${({$diff, $type, $isPrevGreater}) => {
        if ($diff === 0) {
            return "#00000040";
        }

        return $isPrevGreater ? red.primary : green.primary;
    }};

    margin-right: .3em;
    vertical-align: text-bottom;
    content: '${({ $diff, $type, $isPrevGreater }) => {
    if ($diff === 0) {
        return "";
    }
    if ($type.startsWith("revenues")) {
        return $isPrevGreater ? "\\2193" : "\\2191";
    }

    return $isPrevGreater ? "\\2191" : "\\2193";
  }}';
  }
`;

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

  const totalsByMonth = useMemo(() => {
    const result = {}
    if (flatRows.length > 0) {
      months.forEach(date => {
        const monthWithYear = moment(date).format("MMM YYYY");
        result[monthWithYear] = useTotals(flatRows, [date]);
        result[monthWithYear]["month"] = monthWithYear;
      });
    }

    return result;
  }, [months.length, flatRows.length]);

  const updateColumns = () => {
    const addColumns = [];
    months.forEach(date => {
      const month = moment(date).format("MMM YYYY");
      const prevMonth = moment(date).subtract(1, "month").format("MMM YYYY");
      addColumns.push({
        key: moment(date).format("M"),
        dataIndex: month,
        title: month,
        align: "right",
        render: (value, row, idx) => {
          let sum = 0;
          let diff = 0;
          if (!value) {
            return (
              <Text type={"secondary"}>{sum.toFixed(2)}</Text>
            );
          }
          sum = value / 100;
          if (hasIn(row, prevMonth)) {
            diff = Math.abs(row[month] - row[prevMonth]) / 100;
          }

          return (
            <Space.Compact direction={"vertical"} size={"small"} style={{ display: "flex" }}>
              <Link href={buildQueryString(row, date)} target={"_blank"} style={{ color: isPositive(sum) ? green.primary : red.primary }}>
                {sum.toLocaleString("uk-UA", {
                  maximumFractionDigits: 2,
                  minimumFractionDigits: 2,
                })}
              </Link>
              <span style={{ display: "none" }}>/</span>
              <StyledDiff $type={row.type} $diff={diff} $isPrevGreater={row[prevMonth] > row[month]} $show={hasIn(row, prevMonth)}>
                {hasIn(row, prevMonth) ?
                  diff.toLocaleString("uk-UA", {
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2,
                  })
                  : "0"}
              </StyledDiff>
            </Space.Compact>
          );
        },
      });
    });
    const orderedColumns = orderBy(addColumns, (col) => moment(col.dataIndex, 'MMM YYYY').unix(), ["asc"]);
    orderedColumns.push({
      key: "row_total",
      dataIndex: "row_total",
      title: "Total for period",
      align: "right",
      render: (value, row, idx) => {
        const totals = [];
        months.forEach(date => {
          const month = moment(date).format("MMM YYYY");
          const total = row[month] ?? 0;
          totals.push(toNumber(total));
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
                    excel
                      .addSheet("report")
                      .addColumns(columns)
                      .addDataSource(flatRows)
                      .saveAs("pnl-report.xlsx");
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
                const orderedTotals = orderBy(totalsByMonth, (row) => moment(row.month, "MMM YYYY").unix(), ["asc"]);
                const totalItems = {
                  total_before_taxes: "Total before taxes",
                  taxes: "Taxes",
                  profit: "Net profit",
                  // gross_profit_margin: "Gross profit margin",
                };
                const summirize = (key) => {
                  if (key === "gross_profit_margin") {
                    return "";
                  }
                  let amount = 0;
                  map(orderedTotals, (totals) => {
                    amount += totals[key];
                  });

                  return (amount / 100).toLocaleString("uk-UA", {
                    style: "currency",
                    currency: "EUR",
                  });
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
                          const monthNo = moment(date).unix();
                          const monthWithYear = moment(date).format("MMM YYYY");
                          let amount = hasIn(totalsByMonth, monthWithYear) ? totalsByMonth[monthWithYear][key] : 0;
                          if (key === "gross_profit_margin") {
                            amount = isNaN(amount) ? 0 : amount.toLocaleString("uk-UA", { style: "percent" });
                          } else {
                            amount = (amount / 100).toLocaleString("uk-UA", {
                              style: "currency",
                              currency: "EUR",
                            });
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

import React, { useState, useReducer, useEffect } from "react";
import {
  DashboardOutlined,
  HomeOutlined,
  CalculatorOutlined,
  DownloadOutlined,
} from "@ant-design/icons";
import { Button, Switch, Table, Space, Layout, Menu, theme, ConfigProvider, Typography, TreeSelect } from "antd";
import { green, red } from "@ant-design/colors";
import { Excel } from 'antd-table-saveas-excel';
import Spin from "antd/lib/spin";
import { map, orderBy } from "lodash/collection";

import { reducer } from "./reducer";

const { Header, Content, Footer } = Layout;
const { Link, Text } = Typography;
const { SHOW_PARENT } = TreeSelect;

const initialState = {
  rows: [],
  flatRows: [],
  months: [],
  loading: false,
  total_before_taxes: 0,
  profit: 0,
  gross_prfit_margin: 0,
  ...__initial_state,
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

const headerStyle = {
  textAlign: "center",
  color: "#fff",
  height: 64,
  paddingInline: 50,
  lineHeight: "64px",
  backgroundColor: "#ffffff",
  top: 0,
  zIndex: 1,
  width: "100%",
  display: "flex",
  alignItems: "center",
};


const headerItems = [
  {
    key: "tm1",
    label: <Link href={"/finance/pnl/index"}>All records</Link>,
    icon: <HomeOutlined/>,
  },
  {
    key: "tm2",
    label: <Link href={"/finance/pnl/calculation"}>Calculations</Link>,
    icon: <CalculatorOutlined/>,
  },
];

const buildQueryString = (row, date) => {
  const month = moment(date).format("YYYY-MM-01");

  return `index?PnlSearch[month]=${month}&PnlSearch[type]=${row.type}`;
};

const App = () => {
  const { token: { colorBgContainer } } = theme.useToken();
  const [columns, setColumns] = useState(initialColumns);
  const [state, dispatch] = useReducer(reducer, initialState);

  const { monthTreeData, rows, flatRows, loading, months } = state;

  const updateColumns = () => {
    const addColumns = [];
    months.forEach(date => {
      const month = moment(date).format("MMM YYYY");
      addColumns.push({
        key: month,
        dataIndex: month,
        title: month,
        align: 'right',
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
              {sum.toLocaleString("uk-UA", { maximumFractionDigits: 2, minimumFractionDigits: 2 })}
            </Link>);
        },
      });
    });
    const orderedColumns = orderBy(addColumns, (col) => moment().month(col.key).unix(), ['asc']);
    setColumns([...initialColumns, ...orderedColumns]);
  };

  const totals = {
    total_before_taxes: "Total before taxes",
    profit: "Profit",
    gross_prfit_margin: "Gross total margin",
  };

  const onMonthsChange = (newMonths) => {
    dispatch({ type: "UPDATE_MONTHS", payload: { months: newMonths } });
  };

  useEffect(() => {
    const abortController = new AbortController();
    const fetchRows = async () => {
      dispatch({ type: "FETCH_INIT" });
      const endpoint = new URL("fetch-rows", window.location.href);
      endpoint.searchParams.set("months", months);
      const response = await fetch(endpoint, { signal: abortController.signal });
      const data = await response.json();
      dispatch({ type: "UPDATE_ROWS", payload: { ...data } });
      dispatch({ type: "UPDATE_TOTALS" });
    };

    fetchRows();
    updateColumns();

    return () => {
      abortController.abort();
    };
  }, [months]);


  return (
    <ConfigProvider
      theme={{
        token: {
          colorBgContainer: "#fff",
        },
      }}
    >
      <Spin spinning={loading} delay={100}>
        <Layout>
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
                style={{ minWidth: "15em", maxWidth: "100em" }}
              />
              <Button
                type="primary"
                icon={<DownloadOutlined/>}
                disabled={!flatRows.length}
                onClick={() => {
                  if (flatRows.length) {
                    const excel = new Excel();
                    excel.addSheet('report').addColumns(columns).addDataSource(flatRows).saveAs('pnl-report.xlsx');
                  }
                }}
              >
                Microsoft Excel (.xlsx)
              </Button>
              <Menu
                theme="light"
                mode="horizontal"
                defaultSelectedKeys={["2"]}
                items={headerItems}
              />
            </Space>
          </Header>
          <Content className="site-layout" style={{ padding: "1em 50px 0" }}>
            <Table
              rowKey={(row) => row.key}
              columns={columns}
              dataSource={rows}
              pagination={false}
              bordered
              size={"small"}
              summary={(pageData) => {
                // const totalByMonth = {};
                // pageData.forEach((row) => {
                //   months.forEach(month => {
                //     const mf = moment(month).format("MMM YYYY");
                //     if (mf in totalByMonth) {
                //       totalByMonth[mf] += row[mf];
                //     } else {
                //       totalByMonth[mf] = row[mf];
                //     }
                //   });
                // });
                // let i = 0;

                return (
                  <>
                    {/*<Table.Summary.Row>*/}
                    {/*  <Table.Summary.Cell index={0}></Table.Summary.Cell>*/}
                    {/*  {months.map(month => {*/}
                    {/*    i++;*/}
                    {/*    const mf = moment(month).format("MMM YYYY");*/}

                    {/*    return (*/}
                    {/*      <Table.Summary.Cell key={i} index={i} align={"right"}>*/}
                    {/*        <Text type={"secondary"} strong={true}>*/}
                    {/*          {totalByMonth.hasOwnProperty(mf) ? (totalByMonth[mf] / 100).toLocaleString("uk-UA", {*/}
                    {/*            maximumFractionDigits: 2,*/}
                    {/*            minimumFractionDigits: 2*/}
                    {/*          }) : 0}*/}
                    {/*        </Text>*/}
                    {/*      </Table.Summary.Cell>*/}
                    {/*    );*/}
                    {/*  })}*/}
                    {/*</Table.Summary.Row>*/}
                    {map(totals, (label, key) => {
                      let amount = 0;
                      if (key === "gross_prfit_margin") {
                        amount = (isNaN(state[key]) ? 0 : state[key]).toLocaleString('uk-UA', { style: "percent" });
                      } else {
                        amount = (state[key] / 100).toLocaleString("uk-UA", { style: "currency", currency: "EUR" });
                      }
                      return (
                        <Table.Summary.Row key={key}>
                          <Table.Summary.Cell index={0}>
                            <Text strong={true}>
                              {label}
                            </Text>
                          </Table.Summary.Cell>
                          <Table.Summary.Cell index={1} colSpan={months.length}>
                            <Text strong={true}>{amount}</Text>
                          </Table.Summary.Cell>
                        </Table.Summary.Row>
                      );
                    })}
                  </>
                );
              }}
            />
          </Content>
          <Footer style={{ textAlign: "center" }}>
            <Link href={"/dashboard/dashboard"}>
              <Space>
                <DashboardOutlined/>
                Back to HiPanel Dashboard
              </Space>
            </Link>
          </Footer>
        </Layout>
      </Spin>
    </ConfigProvider>
  );
};

export default App;

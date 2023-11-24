import React, { CSSProperties, useState, useReducer, useEffect } from "react";
import {
  DashboardOutlined,
  HomeOutlined,
  CalculatorOutlined,
} from "@ant-design/icons";
import { green, red } from "@ant-design/colors";
import { Switch, Table, Space, Layout, Menu, theme, ConfigProvider, Typography, TreeSelect } from "antd";
import { reducer } from "./reducer";
import Spin from "antd/lib/spin";

const { Header, Content, Footer } = Layout;
const { Link, Text } = Typography;
const { SHOW_PARENT } = TreeSelect;

const initialState = {
  rows: [],
  loading: false,
  ...__initial_state,
};

const initialColumns = [
  {
    key: "type",
    dataIndex: "type",
    title: "Type",
    render: (value, row, idx) => <Text style={{ width: "200px" }} ellipsis={{ tooltip: row.type }}>{row.type_label}</Text>
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

const buildQueryString = (row, monthName) => {
  const month = moment().month(monthName).format("YYYY-MM-01");

  return `index?PnlSearch[month]=${month}&PnlSearch[type]=${row.type}`;
};

const App = () => {
  const { token: { colorBgContainer } } = theme.useToken();
  const [state, dispatch] = useReducer(reducer, initialState);
  const [months, setMonths] = useState([]);
  const [columns, setColumns] = useState(initialColumns);

  const updateColumns = () => {
    const addColumns = [];
    months.forEach(date => {
      const month = moment(date).format("MMM YYYY");
      addColumns.push({
        key: month,
        dataIndex: month,
        title: month,
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
    setColumns([...initialColumns, ...addColumns]);
  };

  const onMonthsChange = (newMonths) => {
    console.log("onMonthsChange ", newMonths);
    setMonths(newMonths);
  };

  useEffect(() => {
    const abortController = new AbortController();
    const fetchData = async () => {
      dispatch({ type: "FETCH_INIT" });
      const url = new URL("fetch-data", window.location.href);
      url.searchParams.set("months", months);
      const response = await fetch(url, { signal: abortController.signal });
      const rows = await response.json();
      dispatch({ type: "UPDATE_ROWS", payload: { rows } });
    };
    updateColumns();
    fetchData();

    return () => {
      abortController.abort();
    };
  }, [months]);

  const { monthTreeData, rows, loading } = state;

  return (
    <ConfigProvider
      theme={{
        token: {
          colorBgContainer: "#fff",
        },
      }}
    >
      <Spin spinning={loading} delay={200}>
        <Layout>
          <Header style={headerStyle}>
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
            <Menu
              theme="light"
              mode="horizontal"
              defaultSelectedKeys={["2"]}
              items={headerItems}
            />
          </Header>
          <Content className="site-layout" style={{ padding: "1em 50px 0" }}>
            <Table
              rowKey={"type"}
              columns={columns}
              dataSource={rows}
              pagination={false}
              bordered
              size={"small"}
              summary={(pageData) => {
                const totalByMonth = {};
                pageData.forEach((row) => {
                  months.forEach(month => {
                    const mf = moment(month).format("MMM YYYY");
                    if (mf in totalByMonth) {
                      totalByMonth[mf] += row[mf];
                    } else {
                      totalByMonth[mf] = row[mf];
                    }
                  });
                });
                let i = 0;

                return (
                  <>
                    <Table.Summary.Row>
                      <Table.Summary.Cell index={0}></Table.Summary.Cell>
                      {months.map(month => {
                        i++;
                        const mf = moment(month).format("MMM YYYY");

                        return (
                          <Table.Summary.Cell key={i} index={i}>
                            <Text type={"secondary"} strong={true}>
                              {totalByMonth.hasOwnProperty(mf) ? (totalByMonth[mf] / 100).toLocaleString("uk-UA", { maximumFractionDigits: 2, minimumFractionDigits: 2 }) : 0}
                            </Text>
                          </Table.Summary.Cell>
                        );
                      })}
                    </Table.Summary.Row>
                    <Table.Summary.Row>
                      <Table.Summary.Cell index={0}>
                        <Text strong={true}>
                          Total expenses
                        </Text>
                      </Table.Summary.Cell>
                      <Table.Summary.Cell index={1} colSpan={months.length}>
                        <Text type="warning">0.00</Text>
                      </Table.Summary.Cell>
                    </Table.Summary.Row>
                    <Table.Summary.Row>
                      <Table.Summary.Cell index={0}>
                        <Text strong={true}>
                          Total income
                        </Text>
                      </Table.Summary.Cell>
                      <Table.Summary.Cell index={1} colSpan={months.length}>
                        <Text type="warning">0.00</Text>
                      </Table.Summary.Cell>
                    </Table.Summary.Row>
                    <Table.Summary.Row>
                      <Table.Summary.Cell index={0}>
                        <Text strong={true}>
                          Gross Profit margin
                        </Text>
                      </Table.Summary.Cell>
                      <Table.Summary.Cell index={1} colSpan={months.length}>
                        <Text type="warning">0.00</Text>
                      </Table.Summary.Cell>
                    </Table.Summary.Row>
                  </>
                );
              }}
            />
          </Content>
          <Footer style={{ textAlign: "center" }}>
            <Link href={"/dashboard/dashboard"}>
              <Space>
                <DashboardOutlined/>
                Dashboard
              </Space>
            </Link>
          </Footer>
        </Layout>
      </Spin>
    </ConfigProvider>
  );
};

export default App;

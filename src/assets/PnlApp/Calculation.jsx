import React, { useReducer, useState } from "react";
import { Layout, Table, Col, Row, notification, Typography, Tag } from "antd";
import {
  CalculatorOutlined, ImportOutlined,
} from "@ant-design/icons";
import PnlLayout from "./components/PnlLayout";
import MonthRangeInput from "./components/MonthRangeInput";
import { reducer } from "./reducer";
import cloneDeep from "lodash/cloneDeep";

const { Content } = Layout;
const { Link } = Typography;

const initialState = {
  loading: false,
  ...__initial_state,
};

const infoColumns = [
  {
    title: "Month",
    dataIndex: "month",
    key: "month",
  }, {
    title: "Categorized",
    dataIndex: "categorized",
    key: "categorized",
  }, {
    title: "Uncategorized",
    dataIndex: "uncategorized",
    key: "uncategorized",
    render: (value, row, idx) => {
      return (
        <Link target={"_blank"} href={`/finance/charge/index?ChargeSearch[ids]=${row.uncategorized_ids}`}>
          <Tag color={"volcano"}>{row.uncategorized}</Tag>
        </Link>
      );
    },
  },
];

const osrcColumns = [
  {
    title: "Month",
    dataIndex: "month",
    key: "month",
  }, {
    title: "Employees",
    dataIndex: "employees",
    key: "employees",
    render: (value, row, idx) => {
      return (
        <Link target={"_blank"} href={`/client/client/index?ClientSearch[ids]=${row.categorized_ids}`}>
          <Tag color={"geekblue"}>{row.categorized}</Tag>
        </Link>
      );
    },
  },
];

const data = [];

const Calculation = () => {
  const [api, contextHolder] = notification.useNotification();
  const [state, dispatch] = useReducer(reducer, initialState);
  const {
    pnlRows,
    osrcRows,
    loading,
  } = state;

  const calculate = async (date, endpointName) => {
    dispatch({ type: "FETCH_INIT" });
    const from = cloneDeep(date.date[0]);
    const till = cloneDeep(date.date[1]);
    const betweenMonths = [];
    if (from < till) {
      const date = from.startOf("month");
      while (date < till.endOf("month")) {
        betweenMonths.push(date.format("YYYY-MM-01"));
        date.add(1, "month");
      }
    } else {
      betweenMonths.push(from.format("YYYY-MM-01"));
    }
    const urls = [];
    betweenMonths.forEach(month => {
      const url = new URL(endpointName, window.location.href);
      url.searchParams.set("month", month);
      urls.push(url);
    });
    const results = await Promise.allSettled(urls.map(url => fetch(url)));
    const successfulPromises = results.filter(p => p.status === "fulfilled");
    const errors = results.filter(p => p.status === "rejected").map(p => p.reason);
    const rows = await Promise.all(successfulPromises.map(r => {
      const date = moment(r.value.url.split("=")[1]).format("MMMM YYYY");
      if (r.value.ok) {
        api.success({
          message: date,
          description: "Has been calculated",
        });

        return r.value.json();
      } else {
        api.error({
          message: `Error: ${date}`,
          description: r.value.statusText,
        });

        return r.value.text();
      }
    }));
    if (rows.length) {
      const endpoint = new URL("calculation", window.location.href);
      const response = await fetch(endpoint, {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });
      const data = await response.json();
      dispatch({
        type: "UPDATE_ROWS",
        payload: { ...data },
      });
    }
  };

  return (
    <>
      {contextHolder}
      <PnlLayout page={"calculation"}>
        <Content className="site-layout" style={{ margin: "1em 16px 0" }}>
          <Row gutter={[16, 24]}>
            <Col span={14}>
              <MonthRangeInput
                disabledDate={(current) => current < moment("2010-06-01") || current > moment().endOf("month")}
                icon={<CalculatorOutlined/>}
                label={"Calculate charges"}
                loading={loading}
                handleCalculate={(event, date) => {
                  calculate(date, "calculate-pnl-rows");
                }}
              />
              <Table
                columns={infoColumns}
                dataSource={pnlRows}
                pagination={{ defaultPageSize: 24 }}
              />
            </Col>
            <Col span={10}>
              <MonthRangeInput
                disabledDate={(current) => current < moment("2023-01-01")}
                icon={<ImportOutlined/>}
                label={"Import OSRC data"}
                loading={loading}
                handleCalculate={(event, date) => {
                  calculate(date, "import-osrc-rows");
                }}
              />
              <Table
                columns={osrcColumns}
                dataSource={osrcRows}
                pagination={{ defaultPageSize: 24 }}
              />
            </Col>
          </Row>
        </Content>
      </PnlLayout>
    </>
  );
};

export default Calculation;

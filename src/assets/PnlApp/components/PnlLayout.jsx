import React, { useState } from "react";
import { Breadcrumb, Layout, Menu, theme, Typography } from "antd";
import {
  UnorderedListOutlined,
  TableOutlined,
  CalculatorOutlined,
} from "@ant-design/icons";
import "./PnlLayout.css";

const { Header, Content, Footer, Sider } = Layout;
const { Link, Text } = Typography;

function getItem(label, key, icon, children) {
  return {
    key,
    icon,
    children,
    label,
  };
}

const items = [
  getItem(<Link href={"/finance/pnl/index"}>All records</Link>, "all-records", <UnorderedListOutlined/>),
  getItem(<Link href={"/finance/pnl/report"}>Report</Link>, "report", <TableOutlined/>),
  getItem(<Link href={"/finance/pnl/calculation"}>Calculation</Link>, "calculation", <CalculatorOutlined/>),
];

const PnlLayout = ({ header, page, children }) => {
  const [collapsed, setCollapsed] = useState(false);

  return (
    <Layout style={{ minHeight: "100vh" }}>
      <Sider collapsible collapsed={collapsed} onCollapse={(value) => setCollapsed(value)}>
        <div className="demo-logo-vertical"/>
        <Menu theme="dark" defaultSelectedKeys={[page]} mode="inline" items={items}/>
      </Sider>
      <Layout>
        {children}
      </Layout>
    </Layout>
  );
};

export default PnlLayout;

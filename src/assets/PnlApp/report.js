import React from "react";
import { createRoot } from "react-dom/client";
import "antd/dist/reset.css";
import Report from "./Report.jsx";

const container = document.getElementById("pnl-report-app");
if (container) {
  const root = createRoot(container); // createRoot(container!) if you use TypeScript

  root.render(<Report/>);
}

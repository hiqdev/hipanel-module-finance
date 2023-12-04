import React from "react";
import { createRoot } from "react-dom/client";
import "antd/dist/reset.css";
import Calculation from "./Calculation.jsx";

const container = document.getElementById("pnl-calculation-app");
if (container) {
  const root = createRoot(container); // createRoot(container!) if you use TypeScript

  root.render(<Calculation/>);
}

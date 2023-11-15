import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App.jsx";

const container = document.getElementById("pnl-report-app");
const root = createRoot(container); // createRoot(container!) if you use TypeScript

root.render(<App/>);

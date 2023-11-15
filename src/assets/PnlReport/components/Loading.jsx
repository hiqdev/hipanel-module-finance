import * as React from "react";
import CircularProgress from "@mui/material/CircularProgress";

import "./Loading.css";

export const Loading = () => (
  <div className="loading-shading-mui">
    <CircularProgress className="loading-icon-mui" color={"inherit"}/>
  </div>
);

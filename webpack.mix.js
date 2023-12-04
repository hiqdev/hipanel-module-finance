const mix = require("laravel-mix");
const path = require("path");
const AntdMomentWebpackPlugin = require("@ant-design/moment-webpack-plugin");

mix.webpackConfig({
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "src/assets/PnlApp"),
      },
    },
    plugins: [new AntdMomentWebpackPlugin()],
  })
  .autoload({
    moment: ["moment"],
  })
  .js(["src/assets/PnlApp/report.js", "src/assets/PnlApp/calculation.js"], "src/assets/PnlApp/dist/pnl-app.js")
  .sourceMaps()
  .react();

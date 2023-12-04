const mix = require("laravel-mix");
const path = require("path");

mix.webpackConfig({
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "src/assets/PnlCalculation"),
      },
    },
  })
  .autoload({
    moment: ["moment"],
  })
  .js("src/assets/PnlCalculation/index.js", "src/assets/PnlCalculation/dist/pnl-calculation.js")
  .sourceMaps()
  .react();

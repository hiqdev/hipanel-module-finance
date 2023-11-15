const mix = require("laravel-mix");
const path = require("path");

mix.webpackConfig({
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "src/assets/PnlReport"),
      },
    },
  })
  .autoload({
    moment: ["moment"],
  })
  .js("src/assets/PnlReport/index.js", "src/assets/PnlReport/dist/pnl-app.js")
  .sourceMaps()
  .react();

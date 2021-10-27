module.exports = {
  configureWebpack: {
    devtool: "source-map",
  },
  outputDir: "../backend/public/app",
  publicPath: "/app",
  pages: {
    index: {
      entry: "src/main.js",
      template: "templates/base.html",
      filename: "../../resources/views/spa/app.blade.php",
    },
  },
};

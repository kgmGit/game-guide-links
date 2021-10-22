module.exports = {
  outputDir: "../backend/public/app",
  publicPath: "/app",
  pages: {
    app: {
      entry: "src/main.js",
      template: "template/base.html",
      filename: "../../resources/views/spa/app.blade.php",
    },
  },
};

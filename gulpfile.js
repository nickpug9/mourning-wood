// General Stuff
const browser = require("browser-sync").create();
const injector = require("bs-html-injector");
const gulp = require("gulp");
const path = require("path");
const rename = require("gulp-rename");
const gulpif = require("gulp-if");

const mode = process.env.MODE;

// Image Stuff
const imagemin = require("gulp-imagemin");

// CSS Stuff
const postcss = require("gulp-postcss");
const postcssimport = require("postcss-easy-import");
const tailwind = require("tailwindcss");
const autoprefixer = require("autoprefixer");
const postcssnested = require("postcss-nested");
const minmax = require("postcss-media-minmax");
const csso = require("gulp-csso");

function optimize() {
  return csso({
    restructure: false,
  });
}

const plugins = [
  postcssimport(),
  tailwind(),
  autoprefixer(),
  postcssnested(),
  minmax(),
  // reporter()
];

// JS Stuff
const webpack = require("webpack-stream");
const strip = require("gulp-strip-comments");

const config = (modern) => {
  if (modern === true) {
    var webpackPath = path.resolve(__dirname, "dist/modern/");
    var webpackPublic = "/wp-content/themes/practiceTheme/dist/modern/";
    var builtIns = "usage";
  } else {
    var webpackPath = path.resolve(__dirname, "dist/legacy/");
    var webpackPublic = "/wp-content/themes/practiceTheme/dist/legacy/";
    var builtIns = "entry";
  }

  return {
    mode: mode,
    output: {
      path: webpackPath,
      publicPath: webpackPublic,
      filename: "[name].js",
    },
    // devtool: "source-map",
    module: {
      rules: [
        {
          test: /\.js$/,
          include: path.resolve(__dirname, "src/scripts"),
          exclude: /node_modules/,
          use: {
            loader: "babel-loader",
            options: {
              presets: [
                [
                  "@babel/preset-env",
                  {
                    useBuiltIns: builtIns,
                    corejs: 3,
                    targets: {
                      esmodules: modern,
                    },
                  },
                ],
              ],
              plugins: [
                "@babel/plugin-proposal-class-properties",
                "@babel/plugin-transform-runtime",
              ],
              cacheDirectory: true,
            },
          },
        },
      ],
    },
    optimization: {
      splitChunks: {
        chunks: "async",
      },
    },
  };
};

gulp.task("images", () => {
  return gulp
    .src("src/images/*")
    .pipe(
      gulpif(mode === "production", imagemin({ options: { verbose: true } }))
    )
    .pipe(gulp.dest("dist/images/"));
});

gulp.task("videos", () => {
  return gulp.src("src/videos/*").pipe(gulp.dest("dist/videos/"));
});

gulp.task("css", () => {
  return gulp
    .src("src/styles/app.pcss")
    .pipe(postcss(plugins))
    .pipe(gulpif(mode === "production", optimize()))
    .pipe(rename("app.css"))
    .pipe(gulp.dest("dist"))
    .pipe(gulpif(mode === "development", browser.reload({ stream: true })));
});

gulp.task("blocks", () => {
  return gulp
    .src("src/styles/blocks.pcss")
    .pipe(postcss(plugins))
    .pipe(gulpif(mode === "production", optimize()))
    .pipe(rename("custom-blocks.css"))
    .pipe(gulp.dest("dist"))
    .pipe(gulpif(mode === "development", browser.reload({ stream: true })));
});

gulp.task("js", () => {
  return gulp
    .src("src/scripts/main.js")
    .pipe(webpack(config(true)))
    .pipe(strip())
    .pipe(gulp.dest("dist/modern"));
});

gulp.task("legacy", () => {
  return gulp
    .src("src/scripts/legacy.js")
    .pipe(webpack(config(false)))
    .pipe(strip())
    .pipe(gulp.dest("dist/legacy"));
});

gulp.task("reload", (done) => {
  browser.reload();
  done();
});

function server(done) {
  browser.use(injector, {
    files: "templates/**/*.twig",
  });

  browser.init({
    proxy: "http://development.local",
    ui: {
      port: 9000,
    },
  });
  done();
}

function watch() {
  gulp.watch("src/images/**/*", gulp.series("images", "reload"));
  gulp.watch("src/videos/**/*", gulp.series("videos", "reload"));
  gulp.watch("src/styles/**/*.pcss", gulp.series("css", "blocks"));
  gulp.watch("src/scripts/**/*.js", gulp.series("js", "reload"));
}

gulp.task(
  "default",
  gulp.series(
    gulp.parallel("images", "videos", "css", "blocks", "js"),
    server,
    watch
  )
);
gulp.task(
  "build",
  gulp.series(
    gulp.parallel("images", "videos", "css", "blocks", "js"),
    "legacy"
  )
);

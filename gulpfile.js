const path = require("path");
const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const plumber = require("gulp-plumber");
const rename = require("gulp-rename");
const postcss = require("gulp-postcss");
const cssnano = require("cssnano");
const sassGraph = require('sass-graph');

const sassOptions = {
    outputStyle: "expanded",
    silenceDeprecations: ["import"],
};

const paths = {
    assetsScss: ["./assets/scss/**/*.scss", "!./assets/scss/**/_*.scss"],
    assetsCss: "./assets/css",
    blocksScss: ["./blocks/**/scss/*.scss", "!./blocks/**/scss/_*.scss"],
};

function getSassGraph(directory) {
    return sassGraph.parseDir(directory, {
        loadPaths: [directory],
        follow: true
    });
}

function compileSass(src, dest, options = {}, message) {
    let stream = gulp
        .src(src, { base: options.base })
        .pipe(plumber({
            errorHandler: function(err) {
                console.error(err.message);
                this.emit("end");
            }
        }))
        .pipe(sass(sassOptions).on("error", sass.logError))
        .pipe(postcss([cssnano()]));

    if (options.rename) {
        stream = stream.pipe(rename(options.rename));
    }

    stream = stream.pipe(gulp.dest(dest));

    if (message) {
        stream = stream.on("end", () => console.log(message));
    }

    return stream;
}

function compileAssetsScss() {
    return compileSass(
        paths.assetsScss,
        paths.assetsCss,
        {},
        "Assets SCSS compiled."
    );
}

function compileBlocksScss() {
    return compileSass(
        paths.blocksScss,
        ".",
        {
            base: ".",
            rename: (file) => {
                file.dirname = file.dirname.replace(/scss$/, "css");
            },
        },
        "Blocks SCSS compiled."
    );
}

function getDependentFiles(graph, filePath) {
    const normalizedPath = path.resolve(filePath);
    const dependents = new Set();

    function findDependents(file) {
        if (graph.index[file] && graph.index[file].importedBy) {
            graph.index[file].importedBy.forEach(dependent => {
                if (!dependents.has(dependent)) {
                    dependents.add(dependent);
                    findDependents(dependent);
                }
            });
        }
    }

    findDependents(normalizedPath);
    return Array.from(dependents).filter(file => !path.basename(file).startsWith('_'));
}

function watchScss() {
    gulp.watch("./assets/scss/**/*.scss").on("change", (filePath) => {
        const assetsGraph = getSassGraph('./assets/scss');
        const isPartial = path.basename(filePath).startsWith('_');

        if (isPartial) {
            const dependentFiles = getDependentFiles(assetsGraph, filePath);
            if (dependentFiles.length > 0) {
                console.log(`Compiling files that depend on ${filePath}:`, dependentFiles);
                return compileSass(
                    dependentFiles,
                    paths.assetsCss,
                    {},
                    `Compiled dependent assets files for ${filePath}`
                );
            }
        } else {
            return compileSass(
                filePath,
                paths.assetsCss,
                {},
                `Compiled assets file: ${filePath}`
            );
        }
    });

    gulp.watch("./blocks/**/scss/**/*.scss").on("change", (filePath) => {
        const blocksGraph = getSassGraph('./blocks');
        const isPartial = path.basename(filePath).startsWith('_');

        if (isPartial) {
            const dependentFiles = getDependentFiles(blocksGraph, filePath);
            if (dependentFiles.length > 0) {
                console.log(`Compiling files that depend on ${filePath}:`, dependentFiles);
                return compileSass(
                    dependentFiles,
                    ".",
                    {
                        base: ".",
                        rename: (file) => {
                            file.dirname = file.dirname.replace(/scss$/, "css");
                        },
                    },
                    `Compiled dependent block files for ${filePath}`
                );
            }
        } else {
            return compileSass(
                filePath,
                ".",
                {
                    base: ".",
                    rename: (file) => {
                        file.dirname = file.dirname.replace(/scss$/, "css");
                    },
                },
                `Compiled blocks file: ${filePath}`
            );
        }
    });
}

exports.build = gulp.parallel(compileAssetsScss, compileBlocksScss);
exports.watch = watchScss;
exports.default = gulp.series(exports.build, exports.watch);

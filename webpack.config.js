var Encore = require('@symfony/webpack-encore');
const path = require('path');
let glob = require("glob-all");
let PurgecssPlugin = require("purgecss-webpack-plugin");

// Custom PurgeCSS extractor for Tailwind that allows special characters in
// class names.
//
// https://github.com/FullHuman/purgecss#extractor
class TailwindExtractor {
    static extract(content) {
        return content.match(/[A-z0-9-:\/]+/g) || [];
    }
}

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath(path.resolve(__dirname, 'assets/build'))

    // the public path used by the web server to access the previous directory
    .setPublicPath('/assets/build')

    .setManifestKeyPrefix('')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', './system/resources/js/app.js')

    // allow sass/scss files to be processed
    // .enableSassLoader()

    // enable postcss
    .enablePostCssLoader()

    // allow legacy applications to use $/jQuery as a global variable
    // .autoProvidejQuery()

    // enable vue
    .enableVueLoader()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel(function (babelConfig) {
        babelConfig.plugins.push('transform-object-rest-spread');
        babelConfig.plugins.push('syntax-dynamic-import');
    });

if (Encore.isProduction()) {

    Encore.addPlugin(new PurgecssPlugin({

        // Specify the locations of any files you want to scan for class names.
        paths: glob.sync([
            path.join(__dirname, "system/resources/views/**/*.blade.php"),
            path.join(__dirname, "system/resources/js/components/**/*.vue")
        ]),
        extractors: [
            {
                extractor: TailwindExtractor,

                // Specify the file extensions to include when scanning for
                // class names.
                extensions: ["html", "js", "php", "vue"]
            }
        ]
    }));
}


const config = Encore.getWebpackConfig();

// export the final configuration
module.exports = config;

var Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    .enableSingleRuntimeChunk()

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
        //babelConfig.plugins.push('@babel/plugin-proposal-object-rest-spread');
        //babelConfig.plugins.push('@babel/plugin-syntax-dynamic-import');
    });


const config = Encore.getWebpackConfig();

// export the final configuration
module.exports = config;

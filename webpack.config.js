var Encore = require('@symfony/webpack-encore');
const GlobImporter = require('node-sass-glob-importer');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    .addEntry('app', './assets/js/app.js')
    .addEntry('signin', './assets/js/signin.js')
    .addEntry('sso', './assets/js/sso.js')
    .addEntry('attributes', './assets/js/attributes.js')
    .addEntry('code-generator', './assets/js/code-generator.js')

    // uncomment to define the assets of the project
    // .addEntry('js/app', './assets/js/app.js')
    // .addStyleEntry('css/app', './assets/css/app.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(options) {
        options.importer = GlobImporter();
    })

    // uncomment for legacy applications that require $/jQuery as a global variable
    //.autoProvidejQuery()

    .disableSingleRuntimeChunk()

    .addLoader(
        {
            test: /bootstrap\.native/,
            use: {
                loader: 'bootstrap.native-loader'
            }
        }
    )
;

module.exports = Encore.getWebpackConfig();

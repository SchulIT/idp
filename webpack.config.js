const Encore = require('@symfony/webpack-encore');
const GlobImporter = require('node-sass-glob-importer');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('editor', './assets/js/editor.js')
    .addStyleEntry('simple', './assets/css/simple.scss')
    .addEntry('attributes', './assets/js/attributes.js')
    .addEntry('code-generator', './assets/js/code-generator.js')
    .addStyleEntry('email', './assets/css/email.scss')
    .addEntry('metadata', './assets/js/metadata.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    //.splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    //.enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(options) {
        options.sassOptions.importer = GlobImporter()
    })
    .enablePostCssLoader()

    .disableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();

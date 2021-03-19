const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/entries/app.js')
    .addEntry('chart', './assets/js/entries/chart.js')
    .addEntry('entity_form', './assets/js/entries/entity_form.js')
    .addEntry('entity_show', './assets/js/entries/entity_show.js')
    .addEntry('login', './assets/js/entries/login.js')
    .addEntry('contact', './assets/js/entries/contact.js')
    .addEntry('activity', './assets/js/entries/activity.js')
    .addEntry('worker', './assets/js/entries/worker.js')
    .addEntry('user_form', './assets/js/entries/user_form.js')
    .addEntry('sv_data_import','./assets/js/entries/sv_data_import.js')
    .addEntry('sv_dashboard', './assets/js/entries/sv_dashboard.js')
    .addEntry('sv_homepage', './assets/js/entries/sv_homepage.js')
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()
    // .disableSingleRuntimeChunk()

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

    // enables @babel/preset-env polyfills
    .configureBabel(function(babelConfig) {
        babelConfig.plugins.push("babel-plugin-transform-class-properties");
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .copyFiles([
        {from: './assets/images', to: 'images/[path][name].[hash:8].[ext]' },
        {from: './assets/js/sinervis/plugins/lobibox/sounds', to: 'sounds/[path][name].[ext]'},
        {from: './node_modules/ckeditor/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor/skins', to: 'ckeditor/skins/[path][name].[ext]'},
    ])

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;

//module.exports = Encore.getWebpackConfig();


const config = Encore.getWebpackConfig();

config.watchOptions = { poll: true, ignored: /node_modules/ };

module.exports = config;
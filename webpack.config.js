const Encore = require('@symfony/webpack-encore');
const WatchExternalFilesPlugin = require('webpack-watch-files-plugin').default;

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // =============================================================================
    // OUTPUT CONFIGURATION
    // =============================================================================
    
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    // =============================================================================
    // ENTRY CONFIGURATION
    // =============================================================================
    
    /*
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')

    // =============================================================================
    // OPTIMIZATION CONFIGURATION
    // =============================================================================
    
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')
    
    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // =============================================================================
    // BUILD CONFIGURATION
    // =============================================================================
    
    .cleanupOutputBeforeBuild()
    // Disabled to avoid Windows notification spam
    // .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css) for cache busting
    .enableVersioning(Encore.isProduction())

    // =============================================================================
    // FRAMEWORK INTEGRATION
    // =============================================================================
    
    // =============================================================================
    // CSS/SASS PROCESSING
    // =============================================================================
    
    // Enable Sass/SCSS support
    .enableSassLoader()
    
    // Enable PostCSS processing (required for Tailwind CSS v4)
    .enablePostCssLoader((options) => {
        // Optimize PostCSS for production
        if (Encore.isProduction()) {
            options.postcssOptions = {
                ...options.postcssOptions,
                plugins: [
                    ...(options.postcssOptions?.plugins || []),
                    require('cssnano')({
                        preset: 'default'
                    })
                ]
            };
        }
    })

    // =============================================================================
    // BABEL CONFIGURATION
    // =============================================================================
    
    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // =============================================================================
    // WATCH CONFIGURATION
    // =============================================================================
    
    // Watch only specific files that affect CSS classes (more selective to avoid spam)
    .addPlugin(new WatchExternalFilesPlugin({
        files: [
            './templates/base.html.twig',
            './templates/_composants/**/*.html.twig',
            './assets/**/*.js',
            './assets/**/*.scss',
            './assets/**/*.css',
            './assets/controllers.json',
        ],
        verbose: false // Disable verbose output to reduce noise
    }))
;

// =============================================================================
// WEBPACK CONFIGURATION EXPORT
// =============================================================================

const config = Encore.getWebpackConfig();

// Additional optimizations for Tailwind v4
if (Encore.isProduction()) {
    // Ensure proper tree-shaking for CSS
    config.optimization = {
        ...config.optimization,
        usedExports: true,
        sideEffects: false,
    };
}

config.watchOptions = {
    ...config.watchOptions,
    ignored: [
        '**/public/build/**',
        '**/node_modules/**'
    ],
    poll: false,
    followSymlinks: false,
};


module.exports = config;

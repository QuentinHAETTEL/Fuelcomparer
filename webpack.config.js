const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('build')

    .addEntry('app', './assets/scripts/app.js')
    .addEntry('vue', './assets/vue/index.ts')
    .addStyleEntry('main', './assets/styles/main.scss')

    .enableSassLoader((config) => {
        config.additionalData = '@import "./assets/styles/settings/index";';
    })
    .enableVueLoader()
    .enableTypeScriptLoader()
    .addAliases({
        '@': path.resolve('./assets/vue/')
    })

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
;

module.exports = Encore.getWebpackConfig();

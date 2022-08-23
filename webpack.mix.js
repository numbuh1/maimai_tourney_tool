const mix = require('laravel-mix');
const webpack = require('webpack');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.webpackConfig({

    plugins: [
        new webpack.ContextReplacementPlugin(/\.\/locale$/, 'empty-module', false, /js$/)
    ]
});

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]).webpackConfig({ devtool: 'source-map' })
    .sourceMaps()
    .version();

mix.js('resources/js/song/index.js', 'public/js/song')
    .webpackConfig({ devtool: 'source-map' })
    .sourceMaps()
    .version();

mix.js('resources/js/pool/index.js', 'public/js/pool')
    .webpackConfig({ devtool: 'source-map' })
    .sourceMaps()
    .version();

mix.js('resources/js/pool/edit.js', 'public/js/pool')
    .webpackConfig({ devtool: 'source-map' })
    .sourceMaps()
    .version();

mix.js('resources/js/player/index.js', 'public/js/player')
    .webpackConfig({ devtool: 'source-map' })
    .sourceMaps()
    .version();
mix.js('resources/js/tournament/index.js', 'public/js/tournament')
    .webpackConfig({ devtool: 'source-map' })
    .sourceMaps()
    .version();

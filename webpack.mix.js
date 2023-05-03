const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js').postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
    require('autoprefixer'),
]).sourceMaps();

mix.combine([
    'resources/backend/css/bootstrap.min.css',
    'resources/backend/css/bootstrap-rtl.min.css',
    'resources/backend/css/jquery.mCustomScrollbar.css',
    'resources/backend/css/animate.css',
    'resources/backend/css/bootstrap-select.min.css',
    'resources/backend/css/simple-sidebar-v1.css',
    'resources/backend/css/dragula.min.css',
    'resources/backend/css/custom.css',
] , 'public/backend/css/back.css' );

mix.combine([
    'resources/backend/js/jquery.js',
    'resources/backend/js/bootstrap.min.js',
    'resources/backend/js/jquery.mCustomScrollbar.js',
    'resources/backend/js/bootstrap-select.min.js',
    'resources/backend/js/jquery.cookie.js',
    'resources/backend/js/custom.js',
    'resources/backend/js/sweetalert.min.js',
] , 'public/backend/js/back.js' );

mix.combine([
    'resources/backend/css/Grapes/grapes.min.css',
    'resources/backend/css/Grapes/grapesjs-preset-webpage.min.css',
] , 'public/backend/css/Grapes/grapes.css' );

mix.combine([
    'resources/backend/js/Grapes/grapes.min.js',
    'resources/backend/js/Grapes/grapesjs-preset-webpage.min.js',
    'resources/backend/js/Grapes/grapesjs-custom-code.min.js',
    'resources/backend/js/Grapes/grapesjs-script-editor.min.js',
] , 'public/backend/js/Grapes/grapes.js' );

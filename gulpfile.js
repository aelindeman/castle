/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

// disable by default if unset
process.env.DISABLE_NOTIFIER =
    process.env.DISABLE_NOTIFIER === undefined ?
        true :
        process.env.DISABLE_NOTIFIER;

var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.less('app.less', 'public/css/app.css')
        .scripts([
            'lib/jquery-2.1.4.min.js',
            'lib/bootstrap-3.3.6.min.js',
            'lib/bootstrap-markdown-2.10.0.min.js',
            'lib/highlight-9.4.0.min.js',
            'lib/jsdiff-2.2.2.min.js',
            'lib/markdown-0.5.0.min.js',
            'lib/selectize-0.12.1.min.js',
            'lib/sticky-kit-1.1.2.min.js',
            'app.js'
        ], 'public/js/app.js')
        .copy('resources/assets/images/', 'public/images/')
        .version([
            'css/app.css',
            'js/app.js',
            'images',
        ]);
});

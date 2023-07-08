const { mix } = require('laravel-mix');

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


mix.styles('resources/assets/css/404.css', 'public/css/404.css');
mix.styles('resources/assets/css/common.css', 'public/css/common.css');
mix.styles('resources/assets/css/download.css', 'public/css/download.css');
mix.styles('resources/assets/css/encyclopedia.css', 'public/css/encyclopedia.css');
mix.styles('resources/assets/css/flags.css', 'public/css/flags.css');
mix.styles('resources/assets/css/homepage.css', 'public/css/homepage.css');
mix.styles('resources/assets/css/play.css', 'public/css/play.css');
mix.styles('resources/assets/css/shop.css', 'public/css/shop.css');
mix.styles('resources/assets/css/vote.css', 'public/css/vote.css');
mix.styles('resources/assets/css/ladder.css', 'public/css/ladder.css');
mix.styles('resources/assets/css/ladder-tiny.css', 'public/css/ladder-tiny.css');
mix.styles('resources/assets/css/server.css', 'public/css/server.css');
mix.styles('resources/assets/css/codes.css', 'public/css/codes.css');
mix.styles('resources/assets/css/set.css', 'public/css/set.css');
mix.styles('resources/assets/css/builders.css', 'public/css/builders.css');
mix.styles('resources/assets/css/directories.css', 'public/css/directories.css');


mix.copy('resources/assets/js/common.js', 'public/js');
mix.copy('resources/assets/js/common2.js', 'public/js');
mix.copy('resources/assets/js/shopcommon.js', 'public/js');
mix.copy('resources/assets/js/jquery-2.1.4.min.js', 'public/js');
mix.copy('resources/assets/js/jquery-ui.min.js', 'public/js');

    /*
    |--------------------------------------------------------------------------
    | Admin Styles, Scripts and Other plugins
    |--------------------------------------------------------------------------
    */

    /* ---------------------STYLES----------------------- */

    // BOOTSTRAP //
    mix.styles('resources/assets/css/admin/bootstrap.css', 'public/css/bootstrap.min.css');

    // ADMIN THEME //
    mix.styles([
        'resources/assets/css/admin/core.css',
        'resources/assets/css/admin/components.css',
        'resources/assets/css/admin/icons.css',
        'resources/assets/css/admin/pages.css',
        'resources/assets/css/admin/menu.css',
        'resources/assets/css/admin/responsive.css'
    ], 'public/css/vendor_admin.min.css');
    // MY CSS FILE //
    mix.styles('resources/assets/css/admin/app.css', 'public/css/app_admin.min.css');

    /* ---------------------SCRIPTS----------------------- */

    // MODERNIZR //
    mix.copy('resources/assets/js/admin/modernizr.min.js', 'public/js/admin');

    // PLUGINS THEME //
    mix.scripts([
        'resources/assets/js/admin/bootstrap.min.js',
        'resources/assets/js/admin/detect.js',
        'resources/assets/js/admin/fastclick.js',
        'resources/assets/js/admin/jquery.slimscroll.js',
        'resources/assets/js/admin/jquery.blockUI.js',
        'resources/assets/js/admin/waves.js',
        'resources/assets/js/admin/jquery.nicescroll.js',
        'resources/assets/js/admin/jquery.scrollTo.min.js'
    ], 'public/js/admin/vendor_admin.min.js');

    // ADMIN THEME //
    mix.scripts([
        'resources/assets/js/admin/jquery.app.js',
        'resources/assets/js/admin/jquery.core.js'
    ], 'public/js/admin/app_admin.min.js');

    /* ---------------------OTHER PLUGINS----------------------- */

    // TOASTR //
    mix.copy('resources/assets/css/admin/toastr/toastr.min.css', 'public/css/toastr.min.css');
    mix.copy('resources/assets/js/admin/toastr/toastr.min.js', 'public/js/admin/toastr.min.js');

    // BROWSE SERVER //
    mix.copy('resources/assets/js/admin/browseserver.js', 'public/js/admin/browseserver.min.js');

    // DATATABLES //
    mix.styles([
        'resources/assets/css/admin/datatables/jquery.dataTables.min.css',
        'resources/assets/css/admin/datatables/buttons.bootstrap.min.css',
        'resources/assets/css/admin/datatables/fixedHeader.bootstrap.min.css',
        'resources/assets/css/admin/datatables/scroller.bootstrap.min.css',
        'resources/assets/css/admin/datatables/responsive.bootstrap.min.css',
    ], 'public/css/vendor_admin_datatables.min.css');
    mix.scripts([
        'resources/assets/js/admin/datatables/jquery.dataTables.min.js',
        'resources/assets/js/admin/datatables/dataTables.bootstrap.js',
        'resources/assets/js/admin/datatables/dataTables.buttons.min.js',
        'resources/assets/js/admin/datatables/buttons.bootstrap.min.js',
        'resources/assets/js/admin/datatables/dataTables.fixedHeader.min.js',
        'resources/assets/js/admin/datatables/dataTables.keyTable.min.js',
        'resources/assets/js/admin/datatables/dataTables.responsive.min.js',
        'resources/assets/js/admin/datatables/responsive.bootstrap.min.js',
        'resources/assets/js/admin/datatables/dataTables.scroller.min.js',
    ], 'public/js/admin/vendor_admin_datatables.min.js');

    // SWEET ALERT //
    mix.styles('vendor/bower_components/sweetalert/dist/sweetalert.css', 'public/css/sweetalert.min.css');
    mix.copy('vendor/bower_components/sweetalert/dist/sweetalert.min.js', 'public/js/admin');

    // DROPIFY //
    mix.styles('vendor/bower_components/dropify/dist/css/dropify.css', 'public/css/dropify.min.css');
    mix.copy('vendor/bower_components/dropify/dist/js/dropify.min.js', 'public/js/admin');

    // DATETIME PICKER //
    mix.copy('vendor/bower_components/datetimepicker/build/jquery.datetimepicker.min.css', 'public/css/jquery.datetimepicker.min.css');
    mix.copy('vendor/bower_components/datetimepicker/build/jquery.datetimepicker.full.min.js', 'public/js/admin');

    // PICK A COLOR (DEPENDENCE TINYCOLOR) //
    mix.less('vendor/bower_components/pick-a-color/src/less/pick-a-color.less', 'public/css/pick-a-color.min.css');
    mix.scripts([
        'vendor/bower_components/tinycolor/dist/tinycolor-min.js',
        'vendor/bower_components/pick-a-color/src/js/pick-a-color.js',
    ], 'public/js/admin/pick-a-color.min.js');

    // BOOTSTRAP TOUCHSPIN //
    mix.copy('vendor/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css', 'public/css/jquery.bootstrap-touchspin.min.css');
    mix.copy('vendor/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js', 'public/js/admin');

    // DRAGULA (DRAG AND DROP) //
    mix.styles('vendor/bower_components/dragula/dist/dragula.css', 'public/css/dragula.min.css');
    mix.copy('vendor/bower_components/dragula/dist/dragula.min.js', 'public/js/admin');

    // LIGHTBOX2 //
    mix.copy('vendor/bower_components/lightbox2/dist/css/lightbox.min.css', 'public/css/lightbox.min.css');
    mix.copy('vendor/bower_components/lightbox2/dist/js/lightbox.min.js', 'public/js/lightbox.min.js');
    mix.copy('vendor/bower_components/lightbox2/dist/images', 'public/images');

    // FLAG ICON CSS //
    mix.copy('vendor/bower_components/flag-icon-css/css/flag-icon.min.css', 'public/css/flag-icon.min.css');
    mix.copy('vendor/bower_components/flag-icon-css/flags', 'public/flags');




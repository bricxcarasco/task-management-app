const mix = require('laravel-mix');

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

mix
  .js('resources/js/app.js', 'public/js')

  // Login
  .js('resources/js/components/login/index.js', 'public/js/dist/login')

  // Cordova
  .js('resources/js/components/cordova/app.js', 'public/js/dist/cordova')
  .copyDirectory(
    'resources/js/components/cordova/platforms',
    'public/js/dist/cordova/platforms'
  )

  // FCM
  .js('resources/js/components/fcm/registration.js', 'public/js/dist/fcm')
  .js('resources/js/components/fcm/notification.js', 'public/js/dist/fcm')

  // RIO
  .js('resources/js/components/rio/index.js', 'public/js/dist/rio')

  // NEO
  .js('resources/js/components/neo/index.js', 'public/js/dist/neo')

  // Connection Group
  .js(
    'resources/js/components/connection-group/index.js',
    'public/js/dist/connection-group'
  )

  // Chat Service
  .js('resources/js/components/chat/index.js', 'public/js/dist/chat')

  // Connections
  .js(
    'resources/js/components/connections/index.js',
    'public/js/dist/connections'
  )

  // Document Management
  .js(
    'resources/js/components/document-management/index.js',
    'public/js/dist/document-management'
  )

  // File Uploader
  .js('resources/js/helper/fileuploader-facade.js', 'public/js/dist/')

  // Image Canvas
  .js('resources/js/helper/imagecanvas-facade.js', 'public/js/dist/')

  // Schedule service
  .js('resources/js/components/schedules/index.js', 'public/js/dist/schedules')

  // Task service
  .js('resources/js/components/tasks/index.js', 'public/js/dist/tasks')

  // Form service
  .js('resources/js/components/forms/index.js', 'public/js/dist/forms')

  // Notifications
  .js(
    'resources/js/components/notifications/index.js',
    'public/js/dist/notifications'
  )

  // Classified service
  .js(
    'resources/js/components/classifieds/index.js',
    'public/js/dist/classifieds'
  )

  // Electronic Contract
  .js(
    'resources/js/components/electronic-contracts/index.js',
    'public/js/dist/electronic-contract'
  )

  // Knowlege service
  .js(
    'resources/js/components/knowledges/index.js',
    'public/js/dist/knowledges'
  )

  // Workflows
  .js('resources/js/components/workflows/index.js', 'public/js/dist/workflows')

  // Paid Plan
  .js('resources/js/components/paid-plan/index.js', 'public/js/dist/paid-plan')

  .vue()
  .sass('resources/sass/app.scss', 'public/css')
  .sass('resources/sass/pdf.scss', 'public/css');

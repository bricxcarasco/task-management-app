cordova.define('cordova/plugin_list', function (require, exports, module) {
  module.exports = [
    {
      id: 'cordova-plugin-inappbrowser.inappbrowser',
      file: 'plugins/cordova-plugin-inappbrowser/www/inappbrowser.js',
      pluginId: 'cordova-plugin-inappbrowser',
      clobbers: ['cordova.InAppBrowser.open'],
    },
  ];
  module.exports.metadata = {
    'cordova-plugin-splashscreen': '6.0.0',
    'cordova-plugin-inappbrowser': '5.0.0',
  };
});

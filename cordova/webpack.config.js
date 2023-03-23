const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const XMLWebpackPlugin = require('xml-webpack-plugin');
const CopyPlugin = require("copy-webpack-plugin");
const dotenv = require('dotenv');
const path = require('path');

dotenv.config();

module.exports = {
  entry: './src/js/index.js',
  devtool: "source-map",
  output: {
    filename: 'main.js',
    path: path.resolve(__dirname, 'www/dist'),
  },
  plugins: [
    new webpack.DefinePlugin({
        'process.env': JSON.stringify(dotenv.config().parsed)
    }),
    new HtmlWebpackPlugin({
        inject: false,
        template: path.resolve(__dirname, 'src/index.html'),
        filename: path.resolve(__dirname, 'www/index.html'),
        targetApp: process.env.TARGET_URL,
    }),
    new XMLWebpackPlugin({
      files: [{
        template: path.join(__dirname, 'cordovaConfig.ejs'),
        filename: 'config.xml',
        writeToContext: true,
        data: {
          targetUrl: process.env.TARGET_URL,
          appName: process.env.APP_NAME,
          appDescription: process.env.APP_DESCRIPTION,
          appVersion: process.env.APP_VERSION,
          packageName: process.env.PACKAGE_NAME,
          displayName: process.env.DISPLAY_NAME,
        }
      }],
      options: {
        delimiter: '%',
        openDelimiter: '<',
        closeDelimiter: '>'
      }
    }),
    new CopyPlugin({
      patterns: [
        {
          from: path.resolve(__dirname, 'platforms/ios/platform_www'),
          to: path.resolve(__dirname, '../src/resources/js/components/cordova/platforms/ios/'),
          noErrorOnMissing: true,
          globOptions: {
            ignore: [
              "**/cordova-js-src/**",
            ],
          },
        },
        {
          from: path.resolve(__dirname, 'platforms/android/platform_www'),
          to: path.resolve(__dirname, '../src/resources/js/components/cordova/platforms/android/'),
          noErrorOnMissing: true,
        },
      ],
    }),
  ]
};
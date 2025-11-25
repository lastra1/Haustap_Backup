const createExpoWebpackConfigAsync = require('@expo/webpack-config');
const path = require('path');

module.exports = async function (env, argv) {
  const config = await createExpoWebpackConfigAsync(env, argv);
  config.resolve = config.resolve || {};
  config.resolve.alias = {
    ...(config.resolve.alias || {}),
    'react-native-maps': path.resolve(__dirname, './stubs/react-native-maps-web.tsx'),
    'react-native/Libraries/Utilities/codegenNativeCommands': path.resolve(__dirname, './stubs/empty.js'),
  };
  return config;
};

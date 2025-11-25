const { getDefaultConfig } = require('expo/metro-config')
const path = require('path')

const config = getDefaultConfig(__dirname)

config.resolver.resolveRequest = (context, realModuleName, platform) => {
  if (platform === 'web') {
    if (realModuleName === 'react-native-maps') {
      return {
        type: 'sourceFile',
        filePath: path.resolve(__dirname, './stubs/react-native-maps-web.tsx'),
      }
    }
    if (realModuleName === 'react-native/Libraries/Utilities/codegenNativeCommands') {
      return {
        type: 'sourceFile',
        filePath: path.resolve(__dirname, './stubs/empty.js'),
      }
    }
  }
  return context.resolveRequest(context, realModuleName, platform)
}

module.exports = config

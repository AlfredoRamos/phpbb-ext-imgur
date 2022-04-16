const path = require('path');

const modulesPath = path.join(__dirname, 'node_modules');
const sourcesPath = path.join(__dirname, 'scss');
const tailwindConfigFile = path.join(__dirname, 'tailwind.config.js');

module.exports = {
	plugins: [
		require('postcss-import')({path: [modulesPath, sourcesPath]}),
		require('tailwindcss')({config: tailwindConfigFile, path: [modulesPath, sourcesPath]}),
		require('cssnano'),
		require('autoprefixer')
	]
};

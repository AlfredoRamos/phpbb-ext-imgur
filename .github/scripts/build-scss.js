'use strict';

const fs = require('fs');
const path = require('path');
const glob = require('glob');
const sass = require('sass');
const tailwind = require('tailwindcss');
const autoprefixer = require('autoprefixer');
const postcssImport = require('postcss-import');
const postcss = require('postcss');

const modulesPath = path.join(__dirname, '../../node_modules');
const sourcesPath = path.join(__dirname, '../../scss');
const tailwindConfigFile = path.join(__dirname, '../../tailwind.config.js');
const scssFileList = glob.sync('scss/themes/**/*.scss');

scssFileList.forEach(function(s) {
	const normalFile = s.replace('scss/themes/', '').replace('.scss', '.css');
	const normalFilePath = path.dirname(normalFile);

	if (!fs.existsSync(normalFilePath)) {
		fs.mkdirSync(normalFilePath, {recursive: true, mode: 0o755});
	}

	const result = sass.renderSync({
		file: s,
		indentType: 'tab',
		indentWidth: 1,
		omitSourceMapUrl: true,
		outFile: normalFile,
		outputStyle: 'expanded',
		sourceMap: false,
		includePaths: [modulesPath]
	});

	postcss([
		postcssImport({path: [modulesPath, sourcesPath]}),
		tailwind(({config: tailwindConfigFile, path: [modulesPath, sourcesPath]})),
		autoprefixer({cascade: false})
	])
	.process(result.css, {from: normalFile})
	.then(function(res) {
		res.warnings().forEach(function(warn) {
			console.warn(warn.toString());
		});

		fs.writeFileSync(normalFile, res.css, {mode: 0o644});
	});
});

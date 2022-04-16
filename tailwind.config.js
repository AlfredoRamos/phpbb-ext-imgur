module.exports = {
	prefix: 'tw-',
	corePlugins: {preflight: false},
	content: [
		__dirname + '/styles/**/*.{html,js}',
		__dirname + '/adm/style/**/*.{html,js}'
	],
	theme: {
		extend: {
			backgroundImage: {
				'imgur': 'url("../images/imgur.svg")',
				'loader': 'url("../images/loader.svg")'
			}
		}
	}
};

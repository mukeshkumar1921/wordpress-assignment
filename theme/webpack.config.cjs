const path = require( 'node:path' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

module.exports = {
	mode: 'production',
	entry: { index: path.resolve( __dirname, 'src/index.js' ) },
	output: { path: path.resolve( __dirname, 'build' ), clean: true },
	module: {
		rules: [ {
			test: /\.js$/,
			exclude: /node_modules/,
			use: {
				loader: require.resolve( 'babel-loader' ),
				options: { presets: [ require.resolve( '@wordpress/babel-preset-default' ) ] },
			},
		} ],
	},
	plugins: [ new DependencyExtractionWebpackPlugin() ],
};

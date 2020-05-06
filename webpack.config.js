const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
const nodeSassGlobImporter = require( 'node-sass-glob-importer' );

module.exports = {
    ...defaultConfig,

    entry: {
        'gfpa-blocks' : path.resolve( process.cwd(), 'src/blocks.js' ),
        'gfpa-editor' : path.resolve( process.cwd(), 'src/styles/editor.scss' ),
        'gfpa-style' : path.resolve( process.cwd(), 'src/styles/style.scss' ),
    },

    output: {
        filename: '[name].js',
        path: path.resolve( process.cwd(), 'dist/' ),
    },

    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            sassOptions: {
                                importer: nodeSassGlobImporter(),
                            }
                        }
                    }
                ],
            }
        ]
    },

    plugins: [
        ...defaultConfig.plugins,

        new FixStyleOnlyEntriesPlugin(),
        new MiniCssExtractPlugin( {
            filename: '[name].css',
        } ),
    ],
};

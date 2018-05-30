var Encore = require('@symfony/webpack-encore');
const path = require("path");

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js and web/build/app.css
    .addEntry('advertisement', './assets/js/advertisement/globalAdvertisement.js')
    .createSharedEntry('vendor',[
        'jquery',
        'jquery-ui',
        'bootstrap-sass',
        'bootbox',
    ])
    // allow sass/scss files to be processed
  //  .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    .enableSassLoader(function(sassOptions) {

    }, {
        resolveUrlLoader: false

    })



// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
//module.exports = Encore.getWebpackConfig();
var config = Encore.getWebpackConfig();

config.externals = {
    module: {
        loaders: [
            {
                test   : /\.css$/,
                loaders: ['style-loader', 'css-loader', 'resolve-url-loader']
            }, {
                test   : /\.scss$/,
                loaders: ['style-loader', 'css-loader', 'resolve-url-loader', 'sass-loader?sourceMap']
            }
        ],
        rules: [
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: "style-loader"
                    },
                    {
                        loader: "css-loader",
                        options: {
                            alias: {
                                "../fonts/bootstrap": "bootstrap-sass/assets/fonts/bootstrap"
                            }
                        }
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            includePaths: [
                                path.resolve("../node_modules/bootstrap-sass/assets/stylesheets")
                            ]
                        }
                    }
                ]
            },
            {
                test: /\.(png|jpg|gif)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: 'images/[name].[ext]',
                        },
                    }
                ]
            },
            {
                test: /\.css$/,
                use: [
                    {loader: "style-loader",

                    },
                    {
                        loader: "css-loader",
                        options: { }
                    }
                ]
            }
        ],
    }
}

module.exports = config;


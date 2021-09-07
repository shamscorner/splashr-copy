const path = require('path')

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js')
        }
    },
    module: {
        rules: [
            {
                test: /\.(graphql|gql)$/,
                exclude: /node_modules/,
                loader: 'graphql-tag/loader'
            }
        ]
    }
}

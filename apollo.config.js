module.exports = {
    client: {
        service: {
            name: 'splashr',
            // URL to the GraphQL API
            url: process.env.MIX_GRAPHQL_API_ENDPOINT
        },
        // Files processed by the extension
        includes: [
            './resources/js/**/*.vue',
            './resources/js/**/*.js',
            './resources/js/Graphql/**/*.gql'
        ]
    },
    service: {
        localSchemaFile: './graphql/schema.graphql'
    }
}

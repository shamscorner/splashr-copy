import GetCommitmentOfClientQuery from '@/Graphql/Company/Queries/GetCommitmentOfClient.gql'

export default {
    methods: {
        fetchCommitments(companyId) {
            return this.$apollo.query({
                query: GetCommitmentOfClientQuery,
                variables: {
                    companyId: companyId
                },
                fetchPolicy: 'network-only'
            })
        }
    }
}

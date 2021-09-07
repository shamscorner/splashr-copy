<template>
    <div>
        <div v-if="orderDetails" class="flex flex-col p-5 space-y-5">
            <div>
                <div
                    v-if="orderDetails.manager_notes"
                    v-html="orderDetails.manager_notes"
                ></div>
                <div v-else>No manager feedback yet</div>
            </div>
            <div>
                <inertia-link
                    :href="
                        route('admin.orders.show', {
                            order: orderId
                        })
                    "
                    class="block mt-5"
                >
                    <jet-secondary-button>
                        View More
                    </jet-secondary-button>
                </inertia-link>
            </div>
        </div>
        <div
            v-else
            class="w-full mt-8 text-2xl text-center text-gray-500 animate-pulse"
        >
            Loading . . .
        </div>
    </div>
</template>

<script>
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import GetOrderDetailsById from '@/Graphql/Video/Queries/GetOrderDetailsById.gql'

export default {
    components: {
        JetSecondaryButton
    },

    props: {
        videoId: {
            type: String,
            required: true
        },
        orderId: {
            type: String,
            required: true
        },
        tabKey: {
            type: Number,
            required: false,
            default: 1
        }
    },

    data() {
        return {
            orderDetails: null
        }
    },

    watch: {
        tabKey: {
            immediate: true,
            handler() {
                this.fetchOrderDetails()
            }
        }
    },

    methods: {
        async fetchOrderDetails() {
            try {
                const response = await this.$apollo.query({
                    query: GetOrderDetailsById,
                    variables: {
                        id: this.videoId
                    },
                    fetchPolicy: 'network-only'
                })
                this.orderDetails = response.data.video
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        }
    }
}
</script>

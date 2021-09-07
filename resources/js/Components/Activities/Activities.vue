<template>
    <div>
        <div
            v-if="isActivitiesLoading"
            class="flex-1 mt-4 text-center text-gray-600"
        >
            Loading . . .
        </div>
        <div v-else-if="activities.data.length" class="py-6 pl-16 pr-5">
            <task-item
                v-for="activity in activities.data"
                :key="activity.id"
                :title="
                    `${activity.user.first_name} ${activity.user.last_name}`
                "
                :leading-image="activity.user.profile_photo_url"
            >
                {{ activity.description }}
                <span class="text-gray-400">
                    {{ orderId ? '' : ` in ${activity.order.video.name}` }}
                </span>
                <template #footer>
                    {{ activity.updated_at | humanTime }}
                </template>
            </task-item>

            <!-- load more button -->
            <button-text
                v-if="activities.paginatorInfo.hasMorePages"
                class="block w-full my-5 text-center text-gray-500"
                :class="{
                    'text-gray-300 pointer-events-none': isLoadingMoreActivities
                }"
                @click.native="fetchActivities(true)"
            >
                {{ isLoadingMoreActivities ? 'Loading' : 'More Activities' }}
            </button-text>
        </div>
        <div v-else class="flex-1 mt-4 text-center text-gray-600">
            No Activities found.
        </div>
    </div>
</template>

<script>
import TaskItem from '@/Components/Activities/TaskItem'
import { diffForHumansTime } from '@/Utilities/RelativeTime'
import GetActivitiesByUserQuery from '@/Graphql/Order/Queries/GetActivitiesByUser.gql'
import GetActivitiesByOrderQuery from '@/Graphql/Order/Queries/GetActivitiesByOrder.gql'

export default {
    components: {
        TaskItem,
        ButtonText: () =>
            import(
                /* webpackChunkName: 'ButtonText' */ '@/Components/ButtonText'
            )
    },

    props: {
        orderId: {
            type: String,
            required: false,
            default: null
        },
        tabKey: {
            type: Number,
            required: false,
            default: 1
        }
    },

    data() {
        return {
            isActivitiesLoading: false,
            activities: {
                data: [],
                paginatorInfo: {
                    currentPage: 1
                }
            },
            isLoadingMoreActivities: false
        }
    },

    watch: {
        tabKey: {
            immediate: true,
            handler() {
                // reset data
                this.activities.data = []
                this.activities.paginatorInfo.currentPage = 1

                this.fetchActivities(false)
            }
        }
    },

    filters: {
        humanTime(time) {
            if (!time) {
                return ''
            }
            return diffForHumansTime(time)
        }
    },

    methods: {
        async fetchActivities(isLoadMoreActivities) {
            if (!isLoadMoreActivities) {
                // show the main loading
                this.isActivitiesLoading = true
            } else {
                this.isLoadingMoreActivities = true
                this.activities.paginatorInfo.currentPage += 1
            }

            let query = GetActivitiesByOrderQuery
            let variables = {
                order_id: this.orderId,
                user_id: this.$page.user.id,
                page: this.activities.paginatorInfo.currentPage
            }
            if (!this.orderId) {
                // fetch activities by user with order data
                query = GetActivitiesByUserQuery
                variables = {
                    user_id: this.$page.user.id,
                    page: this.activities.paginatorInfo.currentPage
                }
            }

            try {
                const response = await this.$apollo.query({
                    // Query
                    query: query,
                    // Parameters
                    variables: variables,
                    fetchPolicy: 'network-only'
                })

                if (response.data.activitiesByOrder) {
                    this.setActivitiesData(
                        response.data.activitiesByOrder,
                        isLoadMoreActivities
                    )
                } else if (response.data.activitiesByUser) {
                    this.setActivitiesData(
                        response.data.activitiesByUser,
                        isLoadMoreActivities
                    )
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isActivitiesLoading = false
                this.isLoadingMoreActivities = false
            }
        },

        setActivitiesData(data, isLoadMoreActivities) {
            if (isLoadMoreActivities) {
                this.activities.data = this.activities.data.concat(data.data)
            } else {
                this.activities.data = data.data
            }

            this.activities.paginatorInfo = {
                currentPage: data.paginatorInfo.currentPage,
                hasMorePages: data.paginatorInfo.hasMorePages,
                lastPage: data.paginatorInfo.lastPage
            }
        }
    }
}
</script>

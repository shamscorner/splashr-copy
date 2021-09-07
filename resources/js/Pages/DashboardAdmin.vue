<template>
    <dashboard-admin-layout>
        <!-- top header section -->
        <top-header></top-header>

        <div class="px-6 py-4 mb-10 max-w-screen-2xl">
            <!-- interaction section -->
            <div class="flex items-center justify-between mt-3 mb-5">
                <input-searchable
                    v-model="inputSearchOrder"
                    placeholder="Search Orders"
                    @keyup.native="searchOrders"
                ></input-searchable>

                <data-table-filter
                    @filter="openManagerFilter"
                    @reset="resetFilter"
                ></data-table-filter>
            </div>
            <!-- table section -->
            <table-custom
                :headers="[
                    'Order Name',
                    'Company Name',
                    'Status',
                    'Acct. Manager',
                    '',
                    'Deadline'
                ]"
                :is-loading="isOrdersListLoading"
            >
                <table-row-custom
                    v-for="(order, index) in ordersList"
                    :key="order.id"
                    @click.native="
                        showOrderDetailsPanel(
                            order.id,
                            order.video_id,
                            order.video.name,
                            order.video.session_type
                        )
                    "
                    :active="sidePanelOrderData.id === order.id"
                >
                    <table-data-custom>
                        <row-data-with-label
                            :label="hasSeenByTheManager(order.managers)"
                        >
                            {{ order.video.name }}
                        </row-data-with-label>
                    </table-data-custom>
                    <table-data-custom>
                        {{ order.video.company.name }}
                    </table-data-custom>
                    <td class="relative w-64 ring-1 ring-white">
                        <status-indicator
                            :side="order.video.pending_side"
                            :status="getVideoStatusLabel(order.video.status)"
                        ></status-indicator>
                    </td>
                    <table-data-custom class="w-60">
                        <profile-stack
                            :profiles="order.managers"
                            :extra="{
                                orderId: order.id,
                                videoId: order.video_id,
                                managers: order.managers
                            }"
                            @on-add="onAddPeopleInProfile"
                            @on-remove="onRemovePeopleFromProfile"
                        ></profile-stack>
                    </table-data-custom>
                    <table-data-custom class="relative w-24 px-8">
                        <button
                            class="absolute top-0 bottom-0 left-0 right-0 flex items-center justify-center w-full h-full border-b border-white focus:outline-none"
                            @click.stop="
                                openComments(
                                    order.id,
                                    order.video.name,
                                    order.video.session_type
                                )
                            "
                        >
                            <div class="relative">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="20"
                                    viewBox="0 0 22 20"
                                    class="w-6 h-6"
                                >
                                    <g fill="none" fill-rule="evenodd">
                                        <g fill="#000" fill-rule="nonzero">
                                            <g>
                                                <g>
                                                    <path
                                                        d="M1.28.093C.574.093 0 .668 0 1.372v13.302c0 .704.575 1.28 1.28 1.28h6.306l3.254 2.501c.094.075.226.075.32 0l3.254-2.502h6.307c.704 0 1.279-.575 1.279-1.279V1.372c0-.704-.575-1.28-1.28-1.28H1.28zm0 .511h19.44c.43 0 .768.338.768.768v13.302c0 .43-.337.767-.767.767h-6.395c-.059 0-.115.02-.16.056L11 17.936l-3.166-2.439c-.045-.036-.101-.056-.16-.056H1.28c-.43 0-.767-.337-.767-.767V1.372c0-.43.337-.768.767-.768zm2.765 3.582c-.141.013-.245.138-.232.28.013.14.139.245.28.231h13.814c.092.002.178-.047.225-.127.046-.08.046-.178 0-.257-.047-.08-.133-.129-.225-.127H4.093c-.016-.002-.032-.002-.048 0zm0 3.581c-.141.013-.245.139-.232.28.013.141.139.245.28.232h13.814c.092.001.178-.047.225-.127.046-.08.046-.178 0-.258-.047-.08-.133-.128-.225-.127H4.045zm0 3.581c-.141.014-.245.139-.232.28.013.142.139.245.28.232h13.814c.092.001.178-.047.225-.127.046-.08.046-.178 0-.258-.047-.08-.133-.128-.225-.127H4.045z"
                                                        transform="translate(-1155 -295) translate(1155 295) translate(0 .163)"
                                                    />
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <div
                                    v-if="order.hasUnreadComments"
                                    class="absolute w-3 h-3 bg-red-400 rounded-full -top-1.5 -right-1.5"
                                ></div>
                            </div>
                        </button>
                    </table-data-custom>
                    <table-data-custom @click.native.stop="">
                        <date-picker
                            v-model="deadlines[index]"
                            valueType="YYYY-MM-DD HH:mm:ss"
                            :clearable="false"
                            @change="
                                onChangeDate(order.id, order.video_id, index)
                            "
                            style="width: 170px;"
                        >
                            <template #input>
                                <button
                                    class="w-full py-1 rounded hover:ring-1 focus:outline-none"
                                    @click.stop=""
                                >
                                    {{ deadlines[index] | diffForHumansTime }}
                                </button>
                            </template>
                        </date-picker>
                    </table-data-custom>
                </table-row-custom>
            </table-custom>

            <empty-row-message v-if="ordersList.length == 0">
                There are no order yet
            </empty-row-message>
        </div>

        <portal to="modal">
            <!-- side panel -->
            <side-panel v-if="isDetailsPanel" @on-close="closeDetailsPanel">
                <template #header>
                    <div class="mb-2">
                        {{ sidePanelOrderData.title }}
                    </div>
                    <session-badge
                        v-if="sidePanelOrderData.sessionType"
                        :session-type="sidePanelOrderData.sessionType"
                    ></session-badge>
                </template>

                <template #tabs>
                    <nav class="flex px-5">
                        <tab-button
                            v-for="tab in tabs"
                            :key="tab"
                            :is-selected="currentTab == tab"
                            @on-selected="selectTab(tab)"
                            class="flex-1"
                        >
                            {{ tab }}
                        </tab-button>
                    </nav>
                </template>

                <template>
                    <keep-alive>
                        <order-overview
                            v-if="currentTab == 'Details'"
                            :key="currentTab"
                            :video-id="currentOrderVideoId"
                            :order-id="sidePanelOrderData.id"
                            :tab-key="tabKey"
                            class="pb-32"
                        ></order-overview>
                    </keep-alive>
                    <keep-alive>
                        <activities
                            v-if="currentTab == 'Activities'"
                            :key="currentTab"
                            :order-id="sidePanelOrderData.id"
                            :tab-key="tabKey"
                            class="pb-32"
                        ></activities>
                    </keep-alive>
                    <keep-alive>
                        <todo
                            v-if="currentTab == 'Tasks'"
                            :key="currentTab"
                            :order-id="sidePanelOrderData.id"
                            :tab-key="tabKey"
                            class="pb-32"
                        ></todo>
                    </keep-alive>
                    <keep-alive>
                        <order-comments
                            v-if="currentTab == 'Comments'"
                            :key="currentTab"
                            :order-id="sidePanelOrderData.id"
                            :tab-key="tabKey"
                            @mark-comment-as-seen="markCommentAsSeen"
                            class="pb-32"
                        ></order-comments>
                    </keep-alive>
                </template>
            </side-panel>

            <!-- account manager popup -->
            <search-manager-popup
                v-if="popupManager.isShow"
                @on-selected="managerSelected"
                @on-close="popupManager.isShow = false"
            ></search-manager-popup>

            <dialog-confirm
                :show="dialogConfirm.isShow"
                @on-close="dialogConfirm.isShow = false"
                @on-remove="removeManagerFromProfile"
            ></dialog-confirm>
        </portal>
    </dashboard-admin-layout>
</template>

<script>
import DashboardAdminLayout from '@/Layouts/DashboardAdminLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import SessionBadge from '@/Components/VideoSummary/SessionBadge'
import ProfileStack from '@/Components/ProfileStack'
import StatusIndicator from '@/Components/StatusIndicator'
import InputSearchable from '@/Components/InputSearchable'
import TableCustom from '@/Components/Table/TableCustom'
import TableRowCustom from '@/Components/Table/TableRowCustom'
import TableDataCustom from '@/Components/Table/TableDataCustom'
import DataTableFilter from '@/Components/Table/DataTableFilter'
import RowDataWithLabel from '@/Components/Table/RowDataWithLabel'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import { diffForHumansTime } from '@/Utilities/RelativeTime'
import { getVideoStatusLabel } from '@/Utilities/VideoStatus'
import EmptyRowMessage from '@/Components/Table/EmptyRowMessage'
import DialogConfirm from '@/Components/DialogConfirm'
import UpdateOrderMutation from '@/Graphql/Order/Mutations/UpdateOrder.gql'
import SearchOrders from '@/Graphql/Order/Queries/SearchOrders.gql'
import FetchOrdersOfClientsOfManager from '@/Graphql/Order/Queries/FetchOrdersOfClientsOfManager.gql'
import UpdateManagerOrderMutation from '@/Graphql/Order/Mutations/UpdateManagerOrder.gql'

export default {
    components: {
        DashboardAdminLayout,
        TopHeader,
        SessionBadge,
        ProfileStack,
        StatusIndicator,
        InputSearchable,
        TableCustom,
        TableRowCustom,
        TableDataCustom,
        DataTableFilter,
        RowDataWithLabel,
        DatePicker,
        TabButton: () =>
            import(
                /* webpackChunkName: 'TabButton' */ '@/Components/Tab/TabButton'
            ),
        SidePanel: () =>
            import(
                /* webpackChunkName: 'SidePanel' */ '@/Components/SidePanel'
            ),
        EmptyRowMessage,
        SearchManagerPopup: () =>
            import(
                /* webpackChunkName: 'SearchManagerPopup' */ '@/Components/SearchManagerPopup'
            ),
        DialogConfirm,
        OrderOverview: () =>
            import(
                /* webpackChunkName: 'OrderOverview' */ '@/Components/OrderOverview'
            ),
        Todo: () =>
            import(/* webpackChunkName: 'Todo' */ '@/Components/Todo/Todo'),
        OrderComments: () =>
            import(
                /* webpackChunkName: 'OrderComment' */ '@/Components/OrderComments/OrderComments'
            ),
        Activities: () =>
            import(
                /* webpackChunkName: 'Activities' */ '@/Components/Activities/Activities'
            )
    },

    data() {
        return {
            ordersList: this.$page.orders.map(order => {
                if (order.recent_commenters) {
                    return {
                        ...order,
                        hasUnreadComments: !JSON.parse(
                            order.recent_commenters
                        ).includes(this.$page.user.id)
                    }
                }
                return order
            }),
            isOrdersListLoading: false,
            deadlines: [],
            isDetailsPanel: false, // make it false by default
            tabs: ['Details', 'Activities', 'Tasks', 'Comments'],
            currentTab: 'Details', // make it Details by default
            tabKey: 1,
            inputSearchOrder: '',
            popupManager: {
                isShow: false,
                currentOrderId: null,
                currentOrderVideoId: null,
                managers: [],
                currentProfile: null
            },
            dialogConfirm: {
                isShow: false
            },
            sidePanelOrderData: {
                id: null, // null by default
                title: '',
                sessionType: ''
            },
            currentOrderVideoId: null,
            isDataTableFilter: false
        }
    },

    mounted() {
        // extract deadlines from orders
        this.$page.orders.forEach(order => {
            this.deadlines.push(order.deadline)
        })
    },

    filters: {
        diffForHumansTime(timeStamp) {
            if (!timeStamp) {
                return '7 days'
            }
            return diffForHumansTime(timeStamp)
        }
    },

    methods: {
        hasSeenByTheManager(managers) {
            if (managers) {
                const manager = managers.find(
                    manager => manager.id === this.$page.manager.id
                )
                if (manager && manager.manager_order_pivot) {
                    return manager.manager_order_pivot.is_visited ? '' : 'new'
                }
            }
            return ''
        },

        getVideoStatusLabel(status) {
            return getVideoStatusLabel(status)
        },

        resetFilter() {
            this.fetchOrders()
        },

        openManagerFilter() {
            this.popupManager.isShow = true
            this.isDataTableFilter = true
        },

        selectTab(tab) {
            ++this.tabKey
            this.currentTab = tab
        },

        closeDetailsPanel() {
            this.isDetailsPanel = false
            this.sidePanelOrderData = {
                id: null,
                title: '',
                sessionType: ''
            }
        },

        async showOrderDetailsPanel(
            orderId,
            videoId,
            videoName,
            videoSessionType
        ) {
            this.currentOrderVideoId = videoId

            this.setSidePanelData(orderId, videoName, videoSessionType)

            // show the details panel
            ++this.tabKey
            this.isDetailsPanel = true
            this.currentTab = 'Details'

            // remove the new status from this row
            this.removeNewStatus(orderId)
        },

        setSidePanelData(orderId, videoName, videoSessionType) {
            // set the side panel data
            this.sidePanelOrderData.id = orderId
            this.sidePanelOrderData.title = videoName
            this.sidePanelOrderData.sessionType = videoSessionType
                ? videoSessionType
                : ''
        },

        async removeNewStatus(orderId) {
            const managers = this.ordersList.find(
                orderData => orderData.id === orderId
            ).managers

            const manager = managers.find(
                manager => manager.id === this.$page.manager.id
            )

            if (manager) {
                try {
                    const response = await this.$apollo.mutate({
                        mutation: UpdateManagerOrderMutation,
                        variables: {
                            id: manager.manager_order_pivot.id,
                            data: {
                                is_visited: true
                            }
                        }
                    })
                    // update data
                    if (response.data.updateManagerOrder) {
                        this.ordersList.forEach(orderData => {
                            if (orderData.id === orderId) {
                                orderData.managers.find(manager => {
                                    if (manager.id === this.$page.manager.id) {
                                        manager.manager_order_pivot =
                                            response.data.updateManagerOrder
                                    }
                                })
                                return
                            }
                        })
                    }
                } catch (error) {
                    // Error
                    console.error(error)
                }
            }
        },

        async onChangeDate(orderId, videoId, index) {
            // console.log(this.deadlines[index])
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateOrderMutation,
                    // Parameters
                    variables: {
                        id: orderId,
                        order: {
                            video_id: videoId,
                            deadline: this.deadlines[index]
                        }
                    }
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        searchOrders() {
            // use lodash to call search event
            this.debouncedQueryForOrders()
        },

        debouncedQueryForOrders: window._.debounce(function() {
            // fetch data from api
            this.fetchOrders()
        }, 1000),

        async fetchOrders(managerUserId = false) {
            let query = SearchOrders
            let variables = {
                userId: this.$page.user.id,
                query: this.inputSearchOrder
            }

            if (managerUserId) {
                query = FetchOrdersOfClientsOfManager
                variables = {
                    userId: managerUserId
                }
            }

            try {
                this.isOrdersListLoading = true
                const response = await this.$apollo.query({
                    // query
                    query: query,
                    // Parameters
                    variables: variables
                })

                let responseData = null
                if (managerUserId) {
                    responseData = response.data.fetchOrdersOfClientsOfManager
                } else {
                    responseData = response.data.searchOrders
                }

                // set the orders list
                this.ordersList =
                    responseData.map(order => {
                        if (order.recent_commenters) {
                            return {
                                ...order,
                                hasUnreadComments: !JSON.parse(
                                    order.recent_commenters
                                ).includes(this.$page.user.id)
                            }
                        }
                        return order
                    }) || []
                // extract deadlines from orders
                this.deadlines = []
                this.ordersList.forEach(order => {
                    this.deadlines.push(order.deadline)
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isOrdersListLoading = false
            }
        },

        onAddPeopleInProfile(extra) {
            // show the search popup
            this.popupManager.isShow = true
            // set values
            this.popupManager.currentOrderId = extra.orderId
            this.popupManager.currentOrderVideoId = extra.videoId
            this.popupManager.managers = extra.managers
        },

        async managerSelected(manager) {
            // sync the managers
            if (!manager) {
                return
            }

            if (this.isDataTableFilter) {
                // filter the current data and return
                this.fetchOrders(manager.user_id)
                this.isDataTableFilter = false
                // close the popup
                this.popupManager.isShow = false
                return
            }

            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateOrderMutation,
                    // Parameters
                    variables: {
                        id: this.popupManager.currentOrderId,
                        order: {
                            video_id: this.popupManager.currentOrderVideoId,
                            managers: {
                                sync: [
                                    ...this.popupManager.managers.map(
                                        preManagers => preManagers.id
                                    ),
                                    manager.id
                                ]
                            }
                        }
                    }
                })
                // push the manager value
                this.ordersList.find(order => {
                    if (order.id === this.popupManager.currentOrderId) {
                        if (
                            !order.managers.some(
                                preManager => preManager.id === manager.id
                            )
                        ) {
                            order.managers.push(manager)
                        }
                    }
                })
                // close the popup
                this.popupManager.isShow = false
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        onRemovePeopleFromProfile(extra) {
            // open the confirmation dialog
            this.dialogConfirm.isShow = true
            // set the values
            this.popupManager.currentOrderId = extra.orderId
            this.popupManager.currentOrderVideoId = extra.videoId
            this.popupManager.managers = extra.managers
            // set current profile id
            this.popupManager.currentProfile = extra.profile
        },

        async removeManagerFromProfile() {
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateOrderMutation,
                    // Parameters
                    variables: {
                        id: this.popupManager.currentOrderId,
                        order: {
                            video_id: this.popupManager.currentOrderVideoId,
                            managers: {
                                disconnect: [
                                    this.popupManager.currentProfile.id
                                ]
                            }
                        }
                    }
                })
                // remove from the managers list
                this.ordersList.find(order => {
                    if (order.id === this.popupManager.currentOrderId) {
                        if (
                            order.managers.some(
                                preManager =>
                                    preManager.id ===
                                    this.popupManager.currentProfile.id
                            )
                        ) {
                            order.managers = order.managers.filter(
                                manager =>
                                    manager.id !==
                                    this.popupManager.currentProfile.id
                            )
                        }
                    }
                })
                // close the confirmation dialog
                this.dialogConfirm.isShow = false
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        openComments(orderId, videoName, videoSessionType) {
            this.setSidePanelData(orderId, videoName, videoSessionType)

            // show details panel
            this.currentTab = 'Comments'
            this.isDetailsPanel = true
        },

        markCommentAsSeen(orderId) {
            this.ordersList.find(order => {
                if (order.id === orderId) {
                    order.hasUnreadComments = false
                }
            })
        }
    }
}
</script>

<template>
    <dashboard-admin-layout>
        <!-- toast alert -->
        <toast
            v-if="isToast.show"
            :role="isToast.role"
            @close="isToast.show = false"
        >
            {{ isToast.message }}
        </toast>

        <!-- top header section -->
        <top-header></top-header>
        <div class="px-6 py-4 mb-10">
            <!-- header -->
            <div class="flex items-end justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">
                        {{ $page.company.name }}
                    </h1>
                    <div class="mt-2">
                        <profile-stack
                            :profiles="companyClients"
                            :add-profile="false"
                            :remove-option="false"
                        ></profile-stack>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <button
                        class="text-lg text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-1 focus:ring-offset-2"
                        @click="viewAssetsLibrary"
                    >
                        <div class="flex items-center space-x-3">
                            <loading-icon
                                v-if="isAssetsLoading"
                                class="w-5 h-5"
                            ></loading-icon>
                            <div
                                class="border-b border-gray-500 hover:border-gray-700"
                            >
                                View assets
                            </div>
                        </div>
                    </button>
                    <!-- manager info-->
                    <div class="mt-3">
                        <profile-stack
                            :profiles="$page.company.managers"
                            :extra="{
                                managers: $page.company.managers
                            }"
                            @on-add="onAddPeopleInProfile"
                            @on-remove="onRemovePeopleFromProfile"
                        ></profile-stack>
                    </div>
                </div>
            </div>

            <!-- content -->
            <div
                class="grid grid-cols-1 mt-6 lg:grid-cols-2 lg:divide-x lg:divide-gray-300"
            >
                <div>
                    <div
                        class="flex items-center justify-between text-2xl font-semibold lg:pr-8"
                    >
                        <div class="flex items-center space-x-4">
                            <div>Orders</div>
                            <button-plus
                                class="w-6 h-6 text-gray-600 border-2 border-gray-500 rounded-full"
                                @click.native="createOrderForClient"
                            ></button-plus>
                        </div>
                        <!-- <div>
                            {{ $page.company.active_orders_count }} /
                            {{ $page.company.total_orders_count }}
                        </div> -->
                    </div>

                    <div class="pr-4">
                        <!-- in progress section -->
                        <div>
                            <h1
                                class="mt-5 text-lg font-semibold text-gray-900"
                            >
                                In Progress
                            </h1>
                            <table-custom
                                :headers="[
                                    'Order Name',
                                    'Status',
                                    'Acct. Manager',
                                    'Deadline'
                                ]"
                            >
                                <table-row-custom
                                    v-for="order in $page.orders.inProgress"
                                    :key="order.id"
                                    @click.native="
                                        $inertia.visit(
                                            route('admin.orders.show', {
                                                order: order.id
                                            })
                                        )
                                    "
                                >
                                    <table-data-custom>
                                        {{ order.video.name }}
                                    </table-data-custom>
                                    <td class="relative w-48 ring-1 ring-white">
                                        <status-indicator
                                            :side="order.video.pending_side"
                                            :status="
                                                getVideoStatusLabel(
                                                    order.video.status
                                                )
                                            "
                                        ></status-indicator>
                                    </td>
                                    <table-data-custom>
                                        <profile-stack
                                            :profiles="order.managers"
                                            :add-profile="false"
                                            :remove-option="false"
                                        ></profile-stack>
                                    </table-data-custom>
                                    <table-data-custom>
                                        {{ order.deadline | humanTime }}
                                    </table-data-custom>
                                </table-row-custom>
                            </table-custom>
                            <empty-row-message
                                v-if="$page.orders.inProgress.length == 0"
                            >
                                There is no order in progress yet
                            </empty-row-message>
                        </div>

                        <!-- completed section -->
                        <div>
                            <h1
                                class="mt-5 text-lg font-semibold text-gray-900"
                            >
                                Completed
                            </h1>
                            <table-custom
                                :headers="[
                                    'Order Name',
                                    'Acct. Manager',
                                    'Completed On'
                                ]"
                            >
                                <table-row-custom
                                    v-for="order in $page.orders.completed"
                                    :key="order.id"
                                    @click.native="
                                        $inertia.visit(
                                            route('admin.orders.show', {
                                                order: order.id
                                            })
                                        )
                                    "
                                >
                                    <table-data-custom>
                                        {{ order.video.name }}
                                    </table-data-custom>
                                    <table-data-custom>
                                        <profile-stack
                                            :profiles="order.managers"
                                            :add-profile="false"
                                            :remove-option="false"
                                        ></profile-stack>
                                    </table-data-custom>
                                    <table-data-custom>
                                        {{ order.updated_at | humanTime }}
                                    </table-data-custom>
                                </table-row-custom>
                            </table-custom>
                            <empty-row-message
                                v-if="$page.orders.completed.length == 0"
                            >
                                There is no completed order yet
                            </empty-row-message>
                        </div>
                    </div>
                </div>

                <div class="lg:pl-5">
                    <div class="mt-10 text-2xl font-semibold lg:mt-0">
                        Prices
                    </div>
                    <prices-table
                        leading-title="Motion"
                        type="motion"
                        :credit-types="{
                            master: {
                                quantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .motion_quantity_master
                                    : 0,
                                usedQuantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .used_motion_quantity_master
                                    : 0,
                                price: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .motion_price_master
                                    : 500
                            },
                            iteration: {
                                quantity: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .motion_quantity_iteration
                                    : 0,
                                usedQuantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .used_motion_quantity_iteration
                                    : 0,
                                price: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .motion_price_iteration
                                    : 500
                            },
                            richContent: {
                                quantity: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .motion_quantity_rich_content
                                    : 0,
                                usedQuantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .used_motion_quantity_rich_content
                                    : 0,
                                price: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .motion_price_rich_content
                                    : 250
                            }
                        }"
                        :company-id="$page.company.id"
                        class="pr-4"
                        @show-toast="showToastMessage"
                    ></prices-table>
                    <prices-table
                        leading-title="Acting"
                        type="acting"
                        :credit-types="{
                            master: {
                                quantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .acting_quantity_master
                                    : 0,
                                usedQuantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .used_acting_quantity_master
                                    : 0,
                                price: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .acting_price_master
                                    : 500
                            },
                            iteration: {
                                quantity: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .acting_quantity_iteration
                                    : 0,
                                usedQuantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .used_acting_quantity_iteration
                                    : 0,
                                price: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .acting_price_iteration
                                    : 500
                            },
                            richContent: {
                                quantity: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .acting_quantity_rich_content
                                    : 0,
                                usedQuantity: $page.company.commitment
                                    ? this.$page.company.commitment
                                          .used_acting_quantity_rich_content
                                    : 0,
                                price: this.$page.company.commitment
                                    ? this.$page.company.commitment
                                          .acting_price_rich_content
                                    : 250
                            }
                        }"
                        :company-id="$page.company.id"
                        class="pr-4"
                        @show-toast="showToastMessage"
                    ></prices-table>
                </div>
            </div>
        </div>

        <portal to="modal">
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

            <!-- asset library dialog -->
            <div v-if="isMediaLibrary">
                <jet-dialog-modal :show="isShowPickerDialog" max-width="7xl">
                    <template #content>
                        <google-picker-body
                            :parent-folder-id="$page.company.g_media_folder_id"
                            :is-user-drive-tab="false"
                            @picker-loaded="pickerLoaded"
                            @close-picker="closeMediaLibraryDialog"
                        ></google-picker-body>
                    </template>
                </jet-dialog-modal>
            </div>
        </portal>
    </dashboard-admin-layout>
</template>

<script>
import DashboardAdminLayout from '@/Layouts/DashboardAdminLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import ProfileStack from '@/Components/ProfileStack'
import ButtonPlus from '@/Components/ButtonPlus'
import TableCustom from '@/Components/Table/TableCustom'
import TableRowCustom from '@/Components/Table/TableRowCustom'
import TableDataCustom from '@/Components/Table/TableDataCustom'
import PricesTable from '@/Components/VideoSummary/PricesTable'
import StatusIndicator from '@/Components/StatusIndicator'
import { diffForHumansTime } from '@/Utilities/RelativeTime'
import { getVideoStatusLabel } from '@/Utilities/VideoStatus'
import EmptyRowMessage from '@/Components/Table/EmptyRowMessage'
import LoadingIcon from '@/Components/Icons/LoadingIcon'
import DialogConfirm from '@/Components/DialogConfirm'
import UpdateCompanyMutation from '@/Graphql/Company/Mutations/UpdateCompany.gql'

export default {
    components: {
        DashboardAdminLayout,
        TopHeader,
        ProfileStack,
        ButtonPlus,
        StatusIndicator,
        TableCustom,
        TableRowCustom,
        TableDataCustom,
        PricesTable,
        EmptyRowMessage,
        LoadingIcon,
        SearchManagerPopup: () =>
            import(
                /* webpackChunkName: 'SearchManagerPopup' */ '@/Components/SearchManagerPopup'
            ),
        JetDialogModal: () =>
            import(
                /* webpackChunkName: 'JetDialogModal' */ '@/Jetstream/DialogModal'
            ),
        GooglePickerBody: () =>
            import(
                /* webpackChunkName: 'GooglePickerBody' */ '@/Components/MediaLibrary/GooglePickerBody'
            ),
        DialogConfirm
    },

    data() {
        return {
            isMediaLibrary: false,
            isShowPickerDialog: false,
            isAssetsLoading: false,
            popupManager: {
                isShow: false,
                managers: [],
                currentProfile: null
            },
            dialogConfirm: {
                isShow: false
            },

            isToast: {
                show: false,
                role: 'success',
                message: ''
            }
        }
    },

    computed: {
        companyClients() {
            return this.$page.company.users.map(user => {
                return {
                    user: user
                }
            })
        }
    },

    filters: {
        humanTime(time) {
            if (!time) {
                return '7 days'
            }
            return diffForHumansTime(time)
        }
    },

    methods: {
        pickerLoaded() {
            this.isShowPickerDialog = true
            this.isAssetsLoading = false
        },

        closeMediaLibraryDialog() {
            this.isMediaLibrary = false
            this.isShowPickerDialog = false
        },

        getVideoStatusLabel(status) {
            return getVideoStatusLabel(status)
        },

        viewAssetsLibrary() {
            this.isAssetsLoading = true
            this.isMediaLibrary = true
        },

        showToastMessage({ message, role }) {
            this.isToast.role = role
            this.isToast.message = message
            this.isToast.show = true
        },

        onAddPeopleInProfile(extra) {
            // show the search popup
            this.popupManager.isShow = true

            // set selected data
            this.popupManager.managers = extra.managers
        },

        async managerSelected(manager) {
            // sync the managers
            if (!manager) {
                return
            }

            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateCompanyMutation,
                    // Parameters
                    variables: {
                        id: this.$page.company.id,
                        company: {
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
                if (
                    !this.$page.company.managers.some(
                        preManager => preManager.id === manager.id
                    )
                ) {
                    this.$page.company.managers.push(manager)
                }
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

            // set current profile id
            this.popupManager.currentProfile = extra.profile
        },

        async removeManagerFromProfile() {
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateCompanyMutation,
                    // Parameters
                    variables: {
                        id: this.$page.company.id,
                        company: {
                            managers: {
                                disconnect: [
                                    this.popupManager.currentProfile.id
                                ]
                            }
                        }
                    }
                })
                // remove from the managers list
                if (
                    this.$page.company.managers.some(
                        preManager =>
                            preManager.id ===
                            this.popupManager.currentProfile.id
                    )
                ) {
                    this.$page.company.managers = this.$page.company.managers.filter(
                        manager =>
                            manager.id !== this.popupManager.currentProfile.id
                    )
                }
                // close the confirmation dialog
                this.dialogConfirm.isShow = false
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        createOrderForClient() {
            if (!this.$page.company.commitment) {
                this.isToast.message =
                    'No price has been defined for this client yet. Please add first.'
                this.isToast.role = 'danger'
                this.isToast.show = true
                return
            }

            this.$inertia.visit(
                // eslint-disable-next-line no-undef
                route('videos.create', {
                    company: this.$page.company.id,
                    isFromManager: true
                })
            )
        }
    }
}
</script>

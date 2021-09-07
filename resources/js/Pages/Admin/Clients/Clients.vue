<template>
    <dashboard-admin-layout>
        <!-- top header section -->
        <top-header></top-header>
        <div class="px-6 py-4 mb-10 max-w-screen-2xl">
            <!-- interaction section -->
            <div class="flex items-center justify-between mt-3 mb-5">
                <input-searchable
                    v-model="inputSearchClient"
                    placeholder="Search Clients"
                    @keyup.native="searchClients"
                ></input-searchable>

                <data-table-filter
                    @filter="openManagerFilter"
                    @reset="resetFilter"
                ></data-table-filter>
            </div>
            <!-- table section -->
            <table-custom
                :headers="[
                    'Company Name',
                    'Contact Name',
                    'Contact Email',
                    'Acct. Manager',
                    'Orders',
                    'Credits'
                ]"
                :is-loading="isClientsListLoading"
            >
                <table-row-custom
                    v-for="companyData in companiesData"
                    :key="companyData.id"
                    @click.native="openClientProfile(companyData.id)"
                >
                    <table-data-custom>
                        <row-data-with-label
                            :label="hasSeenByTheManager(companyData.managers)"
                        >
                            {{ companyData.name }}
                        </row-data-with-label>
                    </table-data-custom>
                    <table-data-custom>
                        {{
                            companyData.users
                                .map(user => {
                                    return (
                                        user.first_name + ' ' + user.last_name
                                    )
                                })
                                .join(', ') | truncateText
                        }}
                    </table-data-custom>
                    <table-data-custom>
                        {{
                            companyData.users
                                .map(user => {
                                    return user.email
                                })
                                .join(', ') | truncateText
                        }}
                    </table-data-custom>
                    <table-data-custom class="w-60">
                        <profile-stack
                            :profiles="companyData.managers"
                            :extra="{
                                companyId: companyData.id,
                                managers: companyData.managers
                            }"
                            @on-add="onAddPeopleInProfile"
                            @on-remove="onRemovePeopleFromProfile"
                        ></profile-stack>
                    </table-data-custom>
                    <table-data-custom class="w-20">
                        <span
                            class="px-2 py-1 text-xs text-center text-white rounded-full bg-splashr-violet-light"
                        >
                            {{ companyData.active_orders_count }} /
                            {{ companyData.total_orders_count }}
                        </span>
                    </table-data-custom>
                    <table-data-custom>
                        <div class="flex flex-col space-y-1">
                            <div
                                v-if="
                                    !companyData.commitment ||
                                        (!companyData.commitment
                                            .motion_quantity_master &&
                                            !companyData.commitment
                                                .acting_quantity_master)
                                "
                                class="text-lg font-bold text-center"
                            >
                                -
                            </div>
                            <div
                                v-if="
                                    companyData.commitment &&
                                        companyData.commitment
                                            .motion_quantity_master
                                "
                            >
                                <span
                                    class="px-2 py-1 text-xs font-bold text-center rounded-full text-splashr-cyan-deep bg-splashr-blue-light"
                                >
                                    {{
                                        `${
                                            companyData.commitment
                                                .used_motion_quantity_master
                                                ? companyData.commitment
                                                      .used_motion_quantity_master
                                                : 0
                                        } / ${
                                            companyData.commitment
                                                .motion_quantity_master
                                                ? companyData.commitment
                                                      .motion_quantity_master
                                                : 0
                                        }`
                                    }}
                                </span>
                                <span
                                    class="px-2 py-1 ml-2.5 text-xs font-bold text-center rounded-full bg-splashr-cyan-light text-splashr-cyan-deep"
                                >
                                    {{
                                        `${
                                            companyData.commitment
                                                .used_motion_quantity_iteration
                                                ? companyData.commitment
                                                      .used_motion_quantity_iteration
                                                : 0
                                        } / ${
                                            companyData.commitment
                                                .motion_quantity_iteration
                                                ? companyData.commitment
                                                      .motion_quantity_iteration
                                                : 0
                                        }`
                                    }}
                                </span>
                            </div>
                            <div
                                v-if="
                                    companyData.commitment &&
                                        companyData.commitment
                                            .acting_quantity_master
                                "
                            >
                                <span
                                    class="px-2 py-1 text-xs text-center text-white rounded-full bg-splashr-violet-deep"
                                >
                                    {{
                                        `${
                                            companyData.commitment
                                                .used_acting_quantity_master
                                                ? companyData.commitment
                                                      .used_acting_quantity_master
                                                : 0
                                        } / ${
                                            companyData.commitment
                                                .acting_quantity_master
                                                ? companyData.commitment
                                                      .acting_quantity_master
                                                : 0
                                        }`
                                    }}
                                </span>
                                <span
                                    class="px-2 py-1 ml-2.5 text-xs font-bold text-center rounded-full bg-splashr-violet-extra-light text-splashr-violet-deep"
                                >
                                    {{
                                        `${
                                            companyData.commitment
                                                .used_acting_quantity_iteration
                                                ? companyData.commitment
                                                      .used_acting_quantity_iteration
                                                : 0
                                        } / ${
                                            companyData.commitment
                                                .acting_quantity_iteration
                                                ? companyData.commitment
                                                      .acting_quantity_iteration
                                                : 0
                                        }`
                                    }}
                                </span>
                            </div>
                        </div>
                    </table-data-custom>
                </table-row-custom>
            </table-custom>
            <empty-row-message v-if="companiesData.length == 0">
                There are no client yet
            </empty-row-message>
        </div>

        <portal to="modal">
            <!-- account manager popup -->
            <search-manager-popup
                v-if="popupManager.isShow"
                @on-close="popupManager.isShow = false"
                @on-selected="managerSelected"
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
import ProfileStack from '@/Components/ProfileStack'
import InputSearchable from '@/Components/InputSearchable'
import TableCustom from '@/Components/Table/TableCustom'
import TableRowCustom from '@/Components/Table/TableRowCustom'
import TableDataCustom from '@/Components/Table/TableDataCustom'
import DataTableFilter from '@/Components/Table/DataTableFilter'
import RowDataWithLabel from '@/Components/Table/RowDataWithLabel'
import EmptyRowMessage from '@/Components/Table/EmptyRowMessage'
import SearchManagerPopup from '@/Components/SearchManagerPopup'
import DialogConfirm from '@/Components/DialogConfirm'
import SearchCompaniesDataQuery from '@/Graphql/Company/Queries/SearchCompaniesData.gql'
import FetchClientsOfManager from '@/Graphql/Company/Queries/FetchClientsOfManager.gql'
import UpdateCompanyMutation from '@/Graphql/Company/Mutations/UpdateCompany.gql'
import UpdateCompanyManagerMutation from '@/Graphql/Manager/Mutations/UpdateCompanyManager.gql'

export default {
    components: {
        DashboardAdminLayout,
        TopHeader,
        ProfileStack,
        InputSearchable,
        TableCustom,
        TableRowCustom,
        TableDataCustom,
        DataTableFilter,
        RowDataWithLabel,
        EmptyRowMessage,
        SearchManagerPopup,
        DialogConfirm
    },

    data() {
        return {
            inputSearchClient: '',
            isClientsListLoading: false,
            companiesData: this.$page.companiesData,
            popupManager: {
                isShow: false,
                companyId: null,
                managers: [],
                currentProfile: null
            },
            dialogConfirm: {
                isShow: false
            },
            isDataTableFilter: false
        }
    },

    filters: {
        truncateText(text) {
            if (text.length > 30) {
                return text.substring(0, 27) + '...'
            }
            return text
        }
    },

    methods: {
        hasSeenByTheManager(managers) {
            if (managers) {
                const manager = managers.find(
                    manager => manager.id === this.$page.manager.id
                )
                if (manager && manager.company_manager_pivot) {
                    return manager.company_manager_pivot.has_seen ? '' : 'new'
                }
            }
            return ''
        },

        resetFilter() {
            this.fetchClients()
        },

        openManagerFilter() {
            this.popupManager.isShow = true
            this.isDataTableFilter = true
        },

        onAddPeopleInProfile(extra) {
            // show the search popup
            this.popupManager.isShow = true

            // set selected data
            this.popupManager.companyId = extra.companyId
            this.popupManager.managers = extra.managers
        },

        onRemovePeopleFromProfile(extra) {
            // open the confirmation dialog
            this.dialogConfirm.isShow = true

            // set selected data
            this.popupManager.companyId = extra.companyId
            // set current profile id
            this.popupManager.currentProfile = extra.profile
        },

        async managerSelected(manager) {
            // sync the managers
            if (!manager) {
                return
            }

            if (this.isDataTableFilter) {
                // filter the current data and return
                this.fetchClients(manager.user_id)
                this.isDataTableFilter = false
                // close the popup
                this.popupManager.isShow = false
                return
            }

            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateCompanyMutation,
                    // Parameters
                    variables: {
                        id: this.popupManager.companyId,
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
                this.companiesData.find(company => {
                    if (company.id === this.popupManager.companyId) {
                        if (
                            !company.managers.some(
                                preManager => preManager.id === manager.id
                            )
                        ) {
                            company.managers.push(manager)
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

        async removeManagerFromProfile() {
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateCompanyMutation,
                    // Parameters
                    variables: {
                        id: this.popupManager.companyId,
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
                this.companiesData.find(company => {
                    if (company.id === this.popupManager.companyId) {
                        if (
                            company.managers.some(
                                preManager =>
                                    preManager.id ===
                                    this.popupManager.currentProfile.id
                            )
                        ) {
                            company.managers = company.managers.filter(
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

        openClientProfile(companyId) {
            this.$inertia.visit(
                // eslint-disable-next-line no-undef
                route('admin.clients.profile', { client: companyId })
            )

            // remove the new status from this row
            this.removeNewStatus(companyId)
        },

        async removeNewStatus(companyId) {
            const managers = this.companiesData.find(
                companyData => companyData.id === companyId
            ).managers

            const manager = managers.find(
                manager => manager.id === this.$page.manager.id
            )

            if (manager) {
                try {
                    await this.$apollo.mutate({
                        mutation: UpdateCompanyManagerMutation,
                        variables: {
                            id: manager.company_manager_pivot.id,
                            data: {
                                has_seen: true
                            }
                        }
                    })
                } catch (error) {
                    // Error
                    console.error(error)
                }
            }
        },

        searchClients() {
            // use lodash to call search event
            this.debouncedQueryForClient()
        },

        debouncedQueryForClient: window._.debounce(function() {
            // fetch data from api
            this.fetchClients()
        }, 1000),

        async fetchClients(managerUserId) {
            let query = SearchCompaniesDataQuery
            let variables = {
                userId: this.$page.user.id,
                query: this.inputSearchClient
            }

            if (managerUserId) {
                query = FetchClientsOfManager
                variables = {
                    userId: managerUserId
                }
            }

            try {
                this.isClientsListLoading = true
                const response = await this.$apollo.query({
                    // Query
                    query: query,
                    // Parameters
                    variables: variables
                })

                // set the clients list
                let responseData = null
                if (managerUserId) {
                    responseData =
                        response.data.fetchCompaniesDataForClientsOfManager
                } else {
                    responseData = response.data.searchCompaniesDataForClients
                }

                this.companiesData = responseData || []
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isClientsListLoading = false
            }
        }
    }
}
</script>

<template>
    <dashboard-admin-layout>
        <top-header></top-header>
        <div class="px-6 py-4 mb-10 max-w-screen-2xl">
            <!-- date filter dropdown -->
            <div class="w-full pb-5 md:w-1/2 lg:w-1/3">
                <select
                    v-model="currentMonth"
                    id="date-filter-dropdown"
                    class="w-full border-gray-300 rounded"
                    @change="filterInvoicesByMonth"
                >
                    <option :value="-1">All</option>
                    <option
                        v-for="month in recentMonths"
                        :key="month.value"
                        :value="month.value"
                    >
                        {{ month.name }}
                    </option>
                </select>
            </div>
            <!-- empty message -->
            <div v-if="!invoicesData.length">
                <empty-message class="mt-10">
                    No invoiceable item for the selected period
                </empty-message>
            </div>
            <!-- table section -->
            <div v-else class="w-full">
                <table-inner-custom
                    v-for="(invoice, index) in invoicesData"
                    :key="invoice.id"
                >
                    <template #header>
                        <tr
                            v-if="index == 0"
                            class="text-xs font-bold text-gray-600"
                        >
                            <td colspan="4" class="px-4 py-2 w-6/9">
                                COMPANY NAME
                            </td>
                            <td class="px-4 py-2 text-center w-1/9">
                                DUE QUANTITY
                            </td>
                            <td colspan="2" class="py-2 pr-4 pl-7 w-2/9">
                                TOTAL DUE AMOUNT
                            </td>
                        </tr>
                        <tr
                            v-if="index == 0"
                            class="h-1"
                            :class="[
                                isLoadingInvoices
                                    ? 'bg-splashr-blue-light animate-pulse'
                                    : 'bg-transparent'
                            ]"
                        >
                            <td colspan="9"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </template>

                    <template #leading-data>
                        <table-row-custom
                            class="group"
                            @click.native="
                                openInvoiceDetails(
                                    invoice.id,
                                    invoice.isCollapsed
                                )
                            "
                        >
                            <table-data-custom
                                colspan="4"
                                :is-checked="!invoice.isCollapsed"
                                class="w-6/9"
                            >
                                {{ invoice.name }}
                            </table-data-custom>
                            <table-data-custom
                                :is-checked="!invoice.isCollapsed"
                                class="text-center w-1/9"
                            >
                                {{ invoice.quantity }}
                            </table-data-custom>
                            <table-data-custom
                                colspan="2"
                                :is-checked="!invoice.isCollapsed"
                                class="w-2/9"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="ml-5">
                                        {{ invoice.total_due }}€
                                    </div>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-4 h-4 group-hover:block"
                                        :class="[
                                            invoice.isCollapsed
                                                ? 'hidden'
                                                : 'block transform rotate-180'
                                        ]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </table-data-custom>
                        </table-row-custom>
                    </template>

                    <template #leading-title v-if="!invoice.isCollapsed">
                        <tr
                            class="font-bold text-left text-gray-600 border-t border-b border-white text-xxs bg-splashr-gray"
                        >
                            <td class="px-4 py-1.5 ring-1 ring-white">
                                ORDER NAME
                            </td>
                            <td class="px-4 py-1.5 ring-1 ring-white">
                                VIDEO LABEL
                            </td>
                            <td
                                class="px-4 py-1.5 ring-1 ring-white text-center"
                            >
                                MANAGER
                            </td>
                            <td
                                class="px-4 py-1.5 ring-1 ring-white text-center"
                            >
                                COMPLETED ON
                            </td>
                            <td
                                class="px-4 py-1.5 ring-1 ring-white text-center"
                            >
                                CREDIT TYPE
                            </td>
                            <td
                                class="px-4 py-1.5 ring-1 ring-white text-center"
                            >
                                AMOUNT
                            </td>
                            <td
                                class="px-4 py-1.5 ring-1 ring-white text-center"
                            >
                                STATUS/REF
                            </td>
                        </tr>
                    </template>

                    <tr v-if="invoice.isVideoItemsLoading">
                        <td
                            class="w-full h-1.5 bg-splashr-blue-light col-span-full animate-pulse"
                            colspan="7"
                        ></td>
                    </tr>

                    <template v-if="!invoice.isCollapsed">
                        <table-row-custom
                            v-for="videoItem in invoice.videoItems.data"
                            :key="videoItem.id"
                            :class="{
                                'opacity-40 pointer-events-none':
                                    videoItem.isLoading
                            }"
                        >
                            <table-inner-data-custom>
                                {{ videoItem.video.name | shortText }}
                            </table-inner-data-custom>
                            <table-inner-data-custom>
                                {{ videoItem.name | shortText }}
                            </table-inner-data-custom>
                            <table-inner-data-custom class="text-center">
                                <profile-stack
                                    :profiles="videoItem.order.managers"
                                    :add-profile="false"
                                    :remove-option="false"
                                ></profile-stack>
                            </table-inner-data-custom>
                            <table-inner-data-custom class="text-center">
                                {{ videoItem.paid_on | showOnlyDate }}
                            </table-inner-data-custom>
                            <table-inner-data-custom class="text-center">
                                {{ videoItem.type | showCreditTypeText }}
                            </table-inner-data-custom>
                            <table-inner-data-custom class="text-center">
                                {{
                                    getDueAmountInEuro(
                                        videoItem.company.commitment,
                                        videoItem.type,
                                        videoItem.session_type
                                    )
                                }}€
                            </table-inner-data-custom>
                            <table-inner-data-custom class="relative w-36">
                                <div
                                    class="absolute top-0 bottom-0 left-0 right-0 flex items-center justify-center text-center"
                                    @click.self="
                                        closeRefInput(
                                            invoice.id,
                                            videoItem.id,
                                            videoItem.refStatus
                                        )
                                    "
                                >
                                    <button
                                        v-if="
                                            videoItem.refStatus ===
                                                refStatuses.due
                                        "
                                        class="font-bold text-red-500 underline focus:outline-none"
                                        @click="
                                            triggerRefStatus(
                                                invoice.id,
                                                videoItem
                                            )
                                        "
                                    >
                                        DUE
                                    </button>
                                    <button
                                        v-else-if="
                                            videoItem.refStatus ===
                                                refStatuses.paid
                                        "
                                        class="font-bold text-green-600 underline focus:outline-none"
                                        @click="
                                            triggerRefStatus(
                                                invoice.id,
                                                videoItem
                                            )
                                        "
                                    >
                                        PAID
                                    </button>
                                    <div
                                        v-else-if="
                                            videoItem.refStatus ===
                                                refStatuses.inputRef
                                        "
                                        class="flex mx-auto items-center px-1 py-0.5 space-x-1 bg-white rounded-sm ring-2 ring-gray-400 w-24"
                                    >
                                        <input
                                            v-model="videoItem.reference_number"
                                            type="text"
                                            class="w-16 px-1 py-0 text-xs font-bold bg-transparent border-none focus:outline-none focus:border-none focus:ring-0"
                                        />
                                        <plus-square-icon
                                            class="w-6 h-5 text-green-500"
                                            :class="{
                                                'opacity-40 pointer-events-none': !videoItem.reference_number
                                            }"
                                            @click.native="
                                                triggerRefStatus(
                                                    invoice.id,
                                                    videoItem
                                                )
                                            "
                                        ></plus-square-icon>
                                    </div>
                                    <div
                                        v-else-if="
                                            videoItem.refStatus ===
                                                refStatuses.deleteRef
                                        "
                                        class="flex mx-auto items-center px-1 py-0.5 space-x-1 bg-gray-200 rounded-sm ring-2 ring-gray-400 w-24"
                                    >
                                        <div
                                            class="w-16 px-1 py-0 text-xs font-bold text-left"
                                        >
                                            {{
                                                videoItem.reference_number
                                                    | veryShortText
                                            }}
                                        </div>
                                        <minus-square-icon
                                            class="w-6 h-5 text-red-500"
                                            @click.native="
                                                triggerRefStatus(
                                                    invoice.id,
                                                    videoItem
                                                )
                                            "
                                        ></minus-square-icon>
                                    </div>
                                </div>
                            </table-inner-data-custom>
                        </table-row-custom>
                    </template>
                </table-inner-custom>
            </div>
        </div>
    </dashboard-admin-layout>
</template>

<script>
import DashboardAdminLayout from '@/Layouts/DashboardAdminLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import TableInnerCustom from '@/Components/Table/TableInnerCustom'
import TableRowCustom from '@/Components/Table/TableRowCustom'
import TableDataCustom from '@/Components/Table/TableDataCustom'
import ProfileStack from '@/Components/ProfileStack'
import TableInnerDataCustom from '@/Components/Table/TableInnerDataCustom'
import {
    videoItemStatus,
    videoItemType,
    getVideoItemTypeString
} from '@/Utilities/VideoItems'
import { videoSessionType } from '@/Utilities/VideoStatus'
import { simpleDate } from '@/Utilities/RelativeTime'
import PlusSquareIcon from '@/Components/Icons/PlusSquareIcon'
import MinusSquareIcon from '@/Components/Icons/MinusSquareIcon'
import GetVideoItemsByCompanyAndMonth from '@/Graphql/Video/Queries/GetVideoItemsByCompanyAndMonth.gql'
import ToggleVideoItemStatusMutation from '@/Graphql/Video/Mutations/ToggleVideoItemStatus.gql'
import GetInvoicesByMonth from '@/Graphql/Invoice/Queries/GetInvoicesByMonth.gql'

export default {
    components: {
        DashboardAdminLayout,
        TopHeader,
        EmptyMessage: () =>
            import(
                /* webpackChunkName: 'EmptyMessage' */ '@/Components/EmptyMessage'
            ),
        TableInnerCustom,
        TableRowCustom,
        TableDataCustom,
        TableInnerDataCustom,
        ProfileStack,
        PlusSquareIcon,
        MinusSquareIcon
    },

    data() {
        return {
            currentMonth: -1,
            invoicesData: this.processInvoicesData(this.$page.invoices),
            refStatuses: {
                due: 0,
                inputRef: 1,
                deleteRef: 2,
                paid: 3
            },
            isLoadingInvoices: false
        }
    },

    computed: {
        recentMonths() {
            const monthNames = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ]

            const today = new Date()
            const last3Months = []

            for (let i = 0; i < 3; i++) {
                const month = today.getMonth() - i
                last3Months.push({
                    name: monthNames[month] + ' ' + today.getFullYear(),
                    value: month
                })
            }
            return last3Months
        }
    },

    filters: {
        showOnlyDate(timestamp) {
            if (!timestamp) {
                return ''
            }
            return simpleDate(timestamp)
        },
        showCreditTypeText(type) {
            return getVideoItemTypeString(type)
        },
        shortText(text) {
            if (!text) {
                return ''
            }
            if (text.length > 25) {
                return text.substring(0, 25) + '...'
            }
            return text
        },
        veryShortText(text) {
            if (!text) {
                return ''
            }
            if (text.length > 7) {
                return text.substring(0, 7) + '...'
            }
            return text
        }
    },

    methods: {
        processInvoicesData(invoices) {
            return invoices.map(invoice => {
                return {
                    ...invoice,
                    isCollapsed: true,
                    isVideoItemsLoading: false,
                    videoItems: {
                        data: [],
                        paginatorInfo: null
                    }
                }
            })
        },

        getDueAmountInEuro(commitment, type, sessionType) {
            switch (type) {
                case videoItemType.master:
                    return sessionType === videoSessionType.motion
                        ? commitment.motion_price_master
                        : commitment.acting_price_master
                case videoItemType.iteration:
                    return sessionType === videoSessionType.motion
                        ? commitment.motion_price_iteration
                        : commitment.acting_price_iteration
                case videoItemType.richContent:
                    return sessionType === videoSessionType.motion
                        ? commitment.motion_price_rich_content
                        : commitment.acting_price_rich_content
            }
            return 0
        },

        triggerRefStatus(invoiceId, videoItem) {
            switch (videoItem.refStatus) {
                case this.refStatuses.due:
                    this.changeRefStatus(
                        invoiceId,
                        videoItem.id,
                        this.refStatuses.inputRef
                    )
                    break
                case this.refStatuses.inputRef:
                    this.updateInvoiceAccordingToStatus(
                        invoiceId,
                        videoItem,
                        videoItemStatus.paid
                    )
                    break
                case this.refStatuses.paid:
                    this.changeRefStatus(
                        invoiceId,
                        videoItem.id,
                        this.refStatuses.deleteRef
                    )
                    break
                case this.refStatuses.deleteRef:
                    this.updateInvoiceAccordingToStatus(
                        invoiceId,
                        videoItem,
                        videoItemStatus.approved
                    )
                    break
            }
        },

        changeRefStatus(invoiceId, videoItemId, ref, isClearRefInput = false) {
            this.invoicesData.find(invoice => {
                if (invoice.id === invoiceId) {
                    invoice.videoItems.data.find(video => {
                        if (video.id === videoItemId) {
                            video.refStatus = ref
                            if (isClearRefInput) {
                                video.reference_number = ''
                            }
                        }
                    })
                }
            })
        },

        async updateInvoiceAccordingToStatus(
            invoiceId,
            videoItem,
            statusToUpdate
        ) {
            this.loadVideoItemRowProgress(invoiceId, videoItem.id, true)

            try {
                const response = await this.$apollo.mutate({
                    mutation: ToggleVideoItemStatusMutation,
                    variables: {
                        videoSessionType: videoItem.video.session_type,
                        videoItem: {
                            id: videoItem.id,
                            company_id: videoItem.company.id,
                            video_id: videoItem.video.id,
                            order_id: videoItem.order.id,
                            name: videoItem.name,
                            type: videoItem.type,
                            status: statusToUpdate,
                            reference_number:
                                statusToUpdate === videoItemStatus.paid
                                    ? videoItem.reference_number
                                    : null
                        }
                    }
                })

                if (response.data.toggleVideoItemStatus) {
                    let refStatusToUpdate = this.refStatuses.paid

                    let clearRefInput = false

                    if (statusToUpdate === videoItemStatus.approved) {
                        refStatusToUpdate = this.refStatuses.due
                        clearRefInput = true
                    }

                    this.changeRefStatus(
                        invoiceId,
                        videoItem.id,
                        refStatusToUpdate,
                        clearRefInput
                    )

                    this.updateInvoiceData(invoiceId, videoItem, statusToUpdate)
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            } finally {
                this.loadVideoItemRowProgress(invoiceId, videoItem.id, false)
            }
        },

        updateInvoiceData(invoiceId, videoItem, statusToUpdate) {
            this.invoicesData.find(invoice => {
                if (invoice.id === invoiceId) {
                    const dueAmountInEuro = this.getDueAmountInEuro(
                        videoItem.company.commitment,
                        videoItem.type,
                        videoItem.session_type
                    )

                    if (statusToUpdate === videoItemStatus.paid) {
                        invoice.total_due -= dueAmountInEuro
                        invoice.quantity -= 1
                    } else if (statusToUpdate === videoItemStatus.approved) {
                        invoice.total_due += dueAmountInEuro
                        invoice.quantity += 1
                    }
                }
            })
        },

        loadVideoItemRowProgress(invoiceId, videoItemId, isLoading) {
            this.invoicesData.find(invoice => {
                if (invoice.id === invoiceId) {
                    invoice.videoItems.data.find(video => {
                        if (video.id === videoItemId) {
                            video.isLoading = isLoading
                        }
                    })
                }
            })
        },

        async openInvoiceDetails(invoiceId, isCollapsed) {
            this.toggleCollapse(invoiceId, isCollapsed)

            this.loadVideoItemsProgressbar(invoiceId, true)

            try {
                const response = await this.$apollo.query({
                    query: GetVideoItemsByCompanyAndMonth,
                    variables: {
                        companyId: invoiceId,
                        status: [
                            videoItemStatus.approved,
                            videoItemStatus.paid
                        ],
                        month: this.currentMonth + 1
                    },
                    fetchPolicy: 'network-only'
                })

                if (response.data.videoItemsByCompanyAndMonth) {
                    this.invoicesData.find(invoice => {
                        if (invoice.id === invoiceId) {
                            invoice.videoItems = {
                                paginatorInfo:
                                    response.data.videoItemsByCompanyAndMonth
                                        .paginatorInfo,
                                data: response.data.videoItemsByCompanyAndMonth.data.map(
                                    item => {
                                        return {
                                            ...item,
                                            refStatus:
                                                item.status ===
                                                videoItemStatus.paid
                                                    ? this.refStatuses.paid
                                                    : this.refStatuses.due,
                                            isLoading: false
                                        }
                                    }
                                )
                            }
                        }
                    })
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            } finally {
                this.loadVideoItemsProgressbar(invoiceId, false)
            }
        },

        toggleCollapse(invoiceId, isCollapsed) {
            this.invoicesData.forEach(invoice => {
                if (invoice.id === invoiceId) {
                    invoice.isCollapsed = !isCollapsed
                } else {
                    invoice.isCollapsed = true
                }
            })
        },

        loadVideoItemsProgressbar(invoiceId, isLoading) {
            this.invoicesData.find(invoice => {
                if (invoice.id === invoiceId) {
                    invoice.isVideoItemsLoading = isLoading
                }
            })
        },

        async filterInvoicesByMonth() {
            this.isLoadingInvoices = true
            try {
                const response = await this.$apollo.query({
                    query: GetInvoicesByMonth,
                    variables: {
                        month: this.currentMonth + 1 // API month starts with 1
                    },
                    fetchPolicy: 'network-only'
                })

                if (response.data.invoicesByMonth) {
                    this.invoicesData = this.processInvoicesData(
                        response.data.invoicesByMonth
                    )
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            } finally {
                this.isLoadingInvoices = false
            }
        },

        closeRefInput(invoiceId, videoItemId, fromRefStatus) {
            switch (fromRefStatus) {
                case this.refStatuses.deleteRef:
                    this.changeRefStatus(
                        invoiceId,
                        videoItemId,
                        this.refStatuses.paid
                    )
                    break
                case this.refStatuses.inputRef:
                    this.changeRefStatus(
                        invoiceId,
                        videoItemId,
                        this.refStatuses.due
                    )
                    break
            }
        }
    }
}
</script>

<template>
    <app-layout>
        <div class="h-full pt-16 overflow-hidden sm:pt-20">
            <!-- sidebar for small devices -->
            <div class="sm:hidden">
                <button
                    class="flex w-full px-4 py-3 text-sm font-semibold text-center text-gray-200 bg-splashr-indigo-deep focus:outline-none focus:ring-2"
                    @click="isResponsiveSideNav = !isResponsiveSideNav"
                >
                    <p class="flex-1">{{ $page.currentSideNavName }}</p>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        class="w-5 h-5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>
                <div
                    v-if="isResponsiveSideNav && isSideNavVisible"
                    class="flex flex-col p-5 space-y-4 text-white bg-splashr-indigo-deep"
                >
                    <side-nav-link
                        :to="route('client.videos.index')"
                        :is-selected="
                            $page.currentSideNavRoute == 'client.videos'
                        "
                    >
                        <div class="flex items-center justify-between">
                            <p>All Videos</p>
                            <inertia-link
                                :href="
                                    route('videos.create', {
                                        company: $page.user.company_id
                                    })
                                "
                            >
                                <button-plus
                                    class="text-gray-900 bg-gray-100"
                                ></button-plus>
                            </inertia-link>
                        </div>
                    </side-nav-link>
                    <side-nav-inner
                        :to="route('client.campaigns.index')"
                        :is-selected="
                            $page.currentSideNavRoute == 'client.campaigns'
                        "
                    >
                        <template #title>
                            Campaigns
                        </template>

                        <side-nav-link
                            v-for="campaign in $page.campaigns"
                            :key="campaign.id"
                            :to="
                                route('client.campaigns.show', {
                                    campaign: campaign.id
                                })
                            "
                            :is-selected="
                                $page.currentCampaignId === campaign.id
                            "
                        >
                            {{ campaign.name }}
                        </side-nav-link>
                    </side-nav-inner>
                    <button
                        class="block w-full px-3 py-2 text-left transition rounded-md hover:ring-2 focus:outline-none focus:ring-2"
                        :class="{
                            'bg-opacity-25 bg-splashr-violet-light': isMediaLibrary
                        }"
                        @click="openAssetsLibrary"
                    >
                        Assets Library
                    </button>
                </div>
            </div>
            <!-- sidebar for large devices -->
            <div class="grid h-full grid-cols-1 overflow-hidden sm:grid-cols-6">
                <!-- sidebar -->
                <div
                    v-if="isSideNavVisible"
                    class="justify-between hidden p-5 overflow-hidden text-white sm:flex-col sm:flex sm:col-span-2 lg:col-span-1 bg-splashr-indigo-deep"
                >
                    <div class="flex flex-col space-y-4">
                        <side-nav-link
                            :to="route('client.videos.index')"
                            :is-selected="
                                $page.currentSideNavRoute == 'client.videos'
                            "
                            class="mt-4"
                        >
                            <div class="flex items-center justify-between">
                                <p>All Videos</p>
                                <inertia-link
                                    :href="
                                        route('videos.create', {
                                            company: $page.user.company_id
                                        })
                                    "
                                >
                                    <button-plus
                                        class="text-gray-900 bg-gray-100"
                                    ></button-plus>
                                </inertia-link>
                            </div>
                        </side-nav-link>
                        <side-nav-inner
                            :to="route('client.campaigns.index')"
                            :is-selected="
                                $page.currentSideNavRoute == 'client.campaigns'
                            "
                            :is-checked="
                                $page.campaigns && $page.campaigns.length == 0
                            "
                        >
                            <template #title>
                                Campaigns
                            </template>

                            <side-nav-link
                                v-for="campaign in $page.campaigns"
                                :key="campaign.id"
                                :to="
                                    route('client.campaigns.show', {
                                        campaign: campaign.id
                                    })
                                "
                                :is-selected="
                                    $page.currentCampaignId === campaign.id
                                "
                            >
                                {{ campaign.name }}
                            </side-nav-link>
                        </side-nav-inner>
                    </div>
                    <div>
                        <button
                            class="block w-full px-3 py-2 text-left transition rounded-md hover:ring-2 focus:outline-none focus:ring-2"
                            :class="{
                                'bg-opacity-25 bg-splashr-violet-light': isMediaLibrary
                            }"
                            @click="openAssetsLibrary"
                        >
                            Assets Library
                        </button>
                    </div>
                </div>

                <!-- body -->
                <div
                    class="overflow-y-auto"
                    :class="[
                        isSideNavVisible
                            ? 'sm:col-span-4 lg:col-span-5'
                            : 'col-span-6 px-8 relative'
                    ]"
                >
                    <inertia-link
                        :href="
                            route('admin.clients.profile', {
                                client: this.$page.company.id
                            })
                        "
                        class="absolute top-6 right-6"
                    >
                        <close-icon class="w-10 h-10"></close-icon>
                    </inertia-link>
                    <slot></slot>
                </div>
            </div>
        </div>

        <portal to="modal">
            <div v-if="isMediaLibrary">
                <jet-dialog-modal :show="isShowPickerDialog" max-width="7xl">
                    <template #content>
                        <google-picker-body
                            :parent-folder-id="$page.company.g_media_folder_id"
                            @picker-loaded="pickerLoaded"
                            @close-picker="closeMediaLibraryDialog"
                        ></google-picker-body>
                    </template>
                </jet-dialog-modal>
            </div>
        </portal>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import SideNavLink from '@/Components/SideNav/SideNavLink'
import ButtonPlus from '@/Components/ButtonPlus'
export default {
    components: {
        AppLayout,
        SideNavLink,
        ButtonPlus,
        SideNavInner: () =>
            import(
                /* webpackChunkName: 'SideNavInner' */ '@/Components/SideNav/SideNavInner'
            ),
        JetDialogModal: () =>
            import(
                /* webpackChunkName: 'JetDialogModal' */ '@/Jetstream/DialogModal'
            ),
        GooglePickerBody: () =>
            import(
                /* webpackChunkName: 'GooglePickerBody' */ '@/Components/MediaLibrary/GooglePickerBody'
            ),
        CloseIcon: () =>
            import(
                /* webpackChunkName: 'CloseIcon' */ '@/Components/Icons/CloseIcon'
            )
    },

    data() {
        return {
            isResponsiveSideNav: false,
            isMediaLibrary: false,
            isShowPickerDialog: false,
            isAssetsLoading: false
        }
    },

    computed: {
        isSideNavVisible() {
            if (!this.$page.isSideNavVisible) {
                return true
            }
            return this.$page.isSideNavVisible === 'false' ? false : true
        }
    },

    methods: {
        openAssetsLibrary() {
            this.isAssetsLoading = true
            this.isMediaLibrary = true
        },

        pickerLoaded() {
            this.isShowPickerDialog = true
            this.isAssetsLoading = false
        },

        closeMediaLibraryDialog() {
            this.isMediaLibrary = false
            this.isShowPickerDialog = false
        }
    }
}
</script>

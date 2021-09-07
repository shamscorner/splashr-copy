<template>
    <dashboard-layout>
        <!-- top header section -->
        <top-header
            back-nav
            :back-nav-route="
                $page.currentCampaignId
                    ? route('client.campaigns.show', {
                          campaign: $page.currentCampaignId
                      })
                    : route('client.videos.index')
            "
            :order-id="$page.order.id"
            :is-todo="false"
            :side-panel-title="$page.currentSideNavName"
            :session-type="$page.video.session_type"
        >
        </top-header>

        <!-- details section -->
        <div class="min-h-screen mt-2">
            <!-- tabs -->
            <nav class="flex justify-start px-6">
                <tab-button
                    v-for="tab in tabs"
                    :key="tab"
                    :is-selected="currentTab == tab"
                    @on-selected="currentTab = tab"
                    class="px-8"
                >
                    {{ tab }}
                </tab-button>
            </nav>

            <!-- tab details -->
            <div class="min-h-screen px-6 pt-5 pb-8 bg-gray-200">
                <keep-alive>
                    <video-summary
                        v-if="currentTab == 'Brief'"
                        :edit-url="{
                            general: route('videos.update', {
                                video: $page.video.id,
                                step: 'General',
                                edit: true
                            }),
                            assets: route('videos.update', {
                                video: $page.video.id,
                                step: 'Assets',
                                edit: true
                            }),
                            details: route('videos.update', {
                                video: $page.video.id,
                                step: 'Details',
                                edit: true
                            })
                        }"
                        :video="$page.video"
                        :folders="$page.assets"
                        class="max-w-6xl"
                    ></video-summary>
                </keep-alive>

                <keep-alive>
                    <order-documents
                        v-if="currentTab == 'Documents'"
                        :order-id="$page.order.id"
                        :documents="$page.order.creative_documents"
                        :is-client="true"
                    ></order-documents>
                </keep-alive>

                <keep-alive>
                    <div v-if="currentTab == 'Videos'">
                        <div v-if="frameIoVideos.isLoading" class="pl-6">
                            Loading . . .
                        </div>
                        <order-videos
                            :videos="clientOrderVideos"
                            :order-id="$page.order.id"
                            :is-client="true"
                        ></order-videos>
                    </div>
                </keep-alive>

                <keep-alive>
                    <div v-if="currentTab == 'Assets'">
                        <div v-if="!isAssetsAvailable" class="pl-6">
                            Loading . . .
                        </div>
                        <google-picker-body
                            v-show="isAssetsAvailable"
                            :dialog-mode="false"
                            :parent-folder-id="$page.assets.assetsFolderId"
                            :client-assets-parent-folder-id="
                                $page.company.g_media_folder_id
                            "
                            @picker-loaded="isAssetsAvailable = true"
                            @close-picker="currentTab = 'Brief'"
                            @picker-files="onAddFilesFromMediaLibrary"
                        ></google-picker-body>
                    </div>
                </keep-alive>
            </div>
        </div>
    </dashboard-layout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import TabButton from '@/Components/Tab/TabButton'
import { orderDetailsTabs } from '@/Utilities/index'
import FrameIoVideoMixin from '@/Mixins/FrameIoVideo'
import MakeDriveFileShortcutMutation from '@/Graphql/Google/Mutations/MakeShortcut.gql'

export default {
    mixins: [FrameIoVideoMixin],

    components: {
        DashboardLayout,
        TopHeader,
        TabButton,
        VideoSummary: () =>
            import(
                /* webpackChunkName: 'VideoSummary' */ '@/Components/VideoSummary/VideoSummary'
            ),
        OrderDocuments: () =>
            import(
                /* webpackChunkName: 'OrderDocuments' */ '@/Components/Order/OrderDocuments'
            ),
        GooglePickerBody: () =>
            import(
                /* webpackChunkName: 'GooglePickerBody' */ '@/Components/MediaLibrary/GooglePickerBody'
            ),
        OrderVideos: () =>
            import(
                /* webpackChunkName: 'OrderVideos' */ '@/Components/Order/OrderVideos'
            )
    },

    data() {
        return {
            tabs: orderDetailsTabs,
            currentTab: this.$page.currentTabName,
            isAssetsAvailable: false
        }
    },

    computed: {
        clientOrderVideos() {
            return this.frameIoVideos.value.filter(
                video => video.label !== 'in_progress' && video.label !== 'none'
            )
        }
    },

    watch: {
        currentTab() {
            if (this.isAssetsAvailable) {
                this.isAssetsAvailable = false
            }
        }
    },

    mounted() {
        // @mixin method: FrameIoVideoMixin
        if (this.$page.order.frameio_project_id) {
            this.getFrameIoVideosByProject(
                this.$page.order.frameio_project_id,
                this.$page.company.id,
                this.$page.order.video_id,
                this.$page.order.id,
                true
            )
        }
    },

    methods: {
        async onAddFilesFromMediaLibrary({ files }) {
            console.log(files)
            // submit
            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: MakeDriveFileShortcutMutation,
                    // Parameters
                    variables: {
                        assets: {
                            video: {
                                id: this.$page.order.video_id
                            },
                            assets: {
                                media: files.map(file => {
                                    return {
                                        id: file.id,
                                        isParentFolder: true
                                    }
                                })
                            }
                        }
                    }
                })

                console.log(response)
            } catch (error) {
                console.log(error)
                alert('Something went wrong!')
            }
        }
    }
}
</script>

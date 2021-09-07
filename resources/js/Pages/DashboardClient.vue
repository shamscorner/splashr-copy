<template>
    <dashboard-layout class="bg-gray-100">
        <div class="mb-10">
            <div
                class="flex items-baseline justify-between px-6 pt-4 pb-3 bg-white border-b border-gray-300"
            >
                <hover-input
                    v-model="campaignName"
                    v-if="
                        $page.currentSideNavRoute == 'client.campaigns' &&
                            $page.campaigns.length
                    "
                    classes="text-2xl"
                    @out-focus="onCampaignNameChanged"
                ></hover-input>

                <div v-else class="text-2xl font-semibold text-gray-700">
                    {{ $page.currentSideNavName }}
                </div>

                <!-- bell notification -->
                <bell-notification
                    class="text-gray-500"
                    @click.native="isDetailsPanel = true"
                ></bell-notification>
            </div>

            <h3
                v-if="!isFirstTime && $page.pendingVideos.length"
                class="mx-6 my-5 text-lg font-semibold"
            >
                In Progress
            </h3>
            <div class="flex flex-wrap mx-4">
                <!-- create your first video action box -->
                <action-box
                    v-if="isFirstTime"
                    :to="
                        route('videos.create', {
                            company: $page.user.company_id
                        })
                    "
                    class="flex flex-col items-center justify-center h-48 p-5 mx-2 my-5 text-center bg-splashr-violet-light hover:bg-indigo-600"
                >
                    <div
                        class="relative mx-auto mt-2 mb-4 bg-white rounded-full w-14 h-14"
                    >
                        <svg
                            aria-hidden="true"
                            focusable="false"
                            data-prefix="fas"
                            data-icon="play-circle"
                            class="absolute w-16 h-16 mx-auto text-indigo-400 -left-1 -top-1"
                            role="img"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512"
                        >
                            <path
                                fill="currentColor"
                                d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm115.7 272l-176 101c-15.8 8.8-35.7-2.5-35.7-21V152c0-18.4 19.8-29.8 35.7-21l176 107c16.4 9.2 16.4 32.9 0 42z"
                            ></path>
                        </svg>
                    </div>
                    <h2 class="text-lg text-white">
                        Create your first video
                    </h2>
                </action-box>

                <!-- pending video action box -->
                <media-thumbnail
                    v-for="pendingVideo in $page.pendingVideos"
                    :key="pendingVideo.id"
                    :media="pendingVideo"
                    class="mx-3 my-2.5"
                >
                </media-thumbnail>
            </div>

            <h3
                v-if="!isFirstTime && $page.unFinishedVideos.length != 0"
                class="mx-6 my-5 text-lg font-semibold"
            >
                Your Drafts
            </h3>
            <div class="flex flex-wrap mx-4">
                <media-thumbnail
                    v-for="unFinishedVideo in $page.unFinishedVideos"
                    :key="unFinishedVideo.id"
                    :media="unFinishedVideo"
                    draft
                    class="mx-3 my-2.5 opacity-70 hover:opacity-100 transition"
                    @delete-draft="setCurrentDeleteDraft"
                >
                </media-thumbnail>
            </div>

            <h3
                v-if="!isFirstTime && $page.recentVideos.length !== 0"
                class="mx-6 my-5 text-lg font-semibold"
            >
                Recent Videos
            </h3>
            <div class="flex flex-wrap mx-4">
                <media-thumbnail
                    v-for="recentVideo in $page.recentVideos"
                    :key="recentVideo.id"
                    :media="recentVideo"
                    class="mx-3 my-2.5"
                >
                </media-thumbnail>
            </div>
        </div>

        <portal to="modal">
            <side-panel
                v-if="isDetailsPanel"
                @on-close="isDetailsPanel = false"
            >
                <template #tabs>
                    <nav class="flex px-5">
                        <tab-button
                            v-for="tab in tabs"
                            :key="tab"
                            :is-selected="currentTab == tab"
                            :class="[tabs.length > 1 ? 'flex-1' : 'self-start']"
                        >
                            {{ tab }}
                        </tab-button>
                    </nav>
                </template>

                <template>
                    <keep-alive>
                        <activities
                            v-if="currentTab == 'Activities'"
                            :key="currentTab"
                            :tab-key="tabKey"
                            class="pb-16"
                        ></activities>
                    </keep-alive>
                </template>
            </side-panel>

            <jet-confirmation-modal
                :show="isConfirmationDialog"
                @close="isConfirmationDialog = false"
            >
                <template #title>
                    <h1 class="font-semibold">Delete Brief from Draft!</h1>
                </template>

                <template #content>
                    Are you sure you would like to delete this Brief?
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click.native="isConfirmationDialog = false"
                    >
                        Nevermind
                    </jet-secondary-button>

                    <jet-danger-button
                        class="ml-2 rounded-full"
                        @click.native="deleteDraft"
                        :class="{
                            'opacity-25': currentDraftToDelete.isProcessing
                        }"
                        :disabled="currentDraftToDelete.isProcessing"
                    >
                        Delete
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
        </portal>
    </dashboard-layout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout'
import BellNotification from '@/Components/Navbar/BellNotification'
import ActionBox from '@/Components/ActionBox'
import MediaThumbnail from '@/Components/MediaThumbnail'
import UpdateCampaignMutation from '@/Graphql/Video/Mutations/UpdateCampaign.gql'
import DeleteVideoMutation from '@/Graphql/Video/Mutations/DeleteVideo.gql'

export default {
    components: {
        DashboardLayout,
        BellNotification,
        ActionBox,
        MediaThumbnail,
        HoverInput: () =>
            import(
                /* webpackChunkName: 'HoverInput' */ '@/Components/HoverInput'
            ),
        SidePanel: () =>
            import(
                /* webpackChunkName: 'SidePanel' */ '@/Components/SidePanel'
            ),
        TabButton: () =>
            import(
                /* webpackChunkName: 'TabButton' */ '@/Components/Tab/TabButton'
            ),
        Activities: () =>
            import(
                /* webpackChunkName: 'Activities' */ '@/Components/Activities/Activities'
            ),
        JetConfirmationModal: () =>
            import(
                /* webpackChunkName: 'JetConfirmationModal' */ '@/Jetstream/ConfirmationModal'
            ),
        JetSecondaryButton: () =>
            import(
                /* webpackChunkName: 'JetSecondaryButton' */ '@/Jetstream/SecondaryButton'
            ),
        JetDangerButton: () =>
            import(
                /* webpackChunkName: 'JetDangerButton' */ '@/Jetstream/DangerButton'
            )
    },

    data() {
        return {
            campaignName: this.$page.currentSideNavName,
            isDetailsPanel: false,
            tabs: ['Activities'],
            currentTab: 'Activities',
            tabKey: 1,
            isConfirmationDialog: false,
            currentDraftToDelete: {
                value: null,
                isProcessing: false
            }
        }
    },

    computed: {
        isFirstTime() {
            if (
                this.$page.recentVideos.length == 0 &&
                this.$page.pendingVideos.length == 0 &&
                this.$page.unFinishedVideos.length == 0
            ) {
                return true
            }
            return false
        }
    },

    methods: {
        async onCampaignNameChanged(campaignName) {
            // update the campaign name
            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateCampaignMutation,
                    // Parameters
                    variables: {
                        id: this.$page.currentCampaignId,
                        campaign: {
                            name: campaignName
                        }
                    }
                })

                if (response.data.updateCampaign) {
                    this.$page.campaigns.find(campaign => {
                        if (campaign.id === response.data.updateCampaign.id) {
                            campaign.name = response.data.updateCampaign.name
                        }
                    })
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        setCurrentDeleteDraft(media) {
            this.isConfirmationDialog = true
            this.currentDraftToDelete.value = media
        },

        async deleteDraft() {
            this.currentDraftToDelete.isProcessing = true
            try {
                const response = await this.$apollo.mutate({
                    mutation: DeleteVideoMutation,
                    variables: {
                        id: this.currentDraftToDelete.value.id
                    }
                })

                if (response.data.deleteVideo) {
                    // remove video from unfinished videos
                    this.$page.unFinishedVideos = this.$page.unFinishedVideos.filter(
                        video => video.id !== this.currentDraftToDelete.value.id
                    )
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            } finally {
                this.currentDraftToDelete.isProcessing = false
                this.isConfirmationDialog = false
            }
        }
    }
}
</script>

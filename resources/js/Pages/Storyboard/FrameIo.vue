<template>
    <storyboard-layout
        no-left-panel
        :is-open-side-panel="isCommentsSidePanel"
        @close-side-panel="closeAllCommentPanels"
    >
        <div class="px-8 py-6">
            <!-- slide container -->
            <div class="flex aspect-w-16 aspect-h-9">
                <video
                    height="100%"
                    width="100%"
                    controls
                    crossorigin="anonymous"
                    playsinline
                    :poster="frameioAsset.image_full"
                    preload="metadata"
                    :src="frameioAsset.h264_1080_best"
                    ref="video-player"
                    class="focus:outline-none"
                ></video>
            </div>
            <!-- comment textarea container -->
            <div
                v-if="isCommentInputPanel == 1"
                class="flex justify-center p-3 mt-5 space-x-6"
            >
                <button
                    class="w-56 px-4 py-2 text-white transition bg-gray-500 rounded-full hover:bg-gray-600 focus:outline-none focus:ring-2"
                    @click="openCommentInputSection"
                >
                    ADD A COMMENT
                </button>
                <button
                    v-if="isAddedComment"
                    class="w-56 px-4 py-2 text-white transition bg-gray-400 rounded-full hover:bg-gray-500 focus:outline-none focus:ring-2"
                    @click="$inertia.replace(route('dashboard.client'))"
                >
                    CLOSE
                </button>
                <button
                    v-else
                    class="w-56 px-4 py-2 text-white transition rounded-full bg-splashr-blue-light hover:bg-splashr-violet-light focus:outline-none focus:ring-2"
                    @click="validateStep"
                >
                    VALIDATE THIS STEP
                </button>
            </div>
            <!-- input comment section -->
            <storyboard-input-textarea
                v-else-if="isCommentInputPanel == 2"
                v-model="inputComment.text"
                :input-comment="inputComment"
                @input="pauseVideo"
                @send="sendComment"
                @close="isCommentInputPanel = 1"
            >
                <template #info>
                    <div
                        class="flex items-center px-3 py-1 space-x-2 text-sm text-white rounded-md bg-splashr-violet-light"
                    >
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
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <div>
                            {{ currentVideoTimestamp | timeFormat }}
                        </div>
                        <input
                            v-model="inputComment.isTimestamp"
                            type="checkbox"
                            name="is_timestamp"
                            class="text-transparent bg-transparent border-gray-300 ring-0 focus:ring-0"
                        />
                    </div>
                </template>
            </storyboard-input-textarea>

            <!-- validate this step section -->
            <div v-else-if="isCommentInputPanel == 3" class="mt-5 text-center">
                <h1 class="text-lg font-bold">
                    Congratulation, your video is completed and final.
                </h1>
                <p>
                    You can now either download your video or upload it in one
                    of our selected social Ad platforms.
                </p>
                <div class="mt-5">
                    <div class="relative inline-block group">
                        <jet-secondary-button class="relative ml-4 text-lg">
                            <div class="flex items-center space-x-2">
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
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    />
                                </svg>
                                <div>DOWNLOAD</div>
                            </div>
                        </jet-secondary-button>

                        <div
                            class="absolute flex flex-col hidden w-40 p-5 space-y-5 text-left bg-white border-gray-300 rounded shadow-md bottom-12 left-5 group-hover:block"
                        >
                            <button
                                v-if="frameioAsset.downloads.h264_1080_best"
                                class="px-2 py-1 focus:outline-none hover:underline"
                                @click="downloadVideo('h264_1080_best')"
                            >
                                1920x1080
                            </button>
                            <button
                                v-if="frameioAsset.downloads.h264_720"
                                class="px-2 py-1 focus:outline-none hover:underline"
                                @click="downloadVideo('h264_720')"
                            >
                                1280x720
                            </button>
                            <button
                                v-if="frameioAsset.downloads.h264_540"
                                class="px-2 py-1 focus:outline-none hover:underline"
                                @click="downloadVideo('h264_540')"
                            >
                                960x540
                            </button>
                            <button
                                v-if="frameioAsset.downloads.h264_360"
                                class="px-2 py-1 focus:outline-none hover:underline"
                                @click="downloadVideo('h264_360')"
                            >
                                640x360
                            </button>
                        </div>
                    </div>

                    <!-- <div class="relative inline-block group">
                        <jet-secondary-button class="relative ml-4 text-lg">
                            <div class="flex items-center space-x-2">
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
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                    />
                                </svg>
                                <div>UPLOAD</div>
                            </div>
                        </jet-secondary-button>

                        <div
                            class="absolute flex flex-col hidden w-40 p-5 space-y-5 bg-white border-gray-300 rounded shadow-md bottom-12 left-5 group-hover:block"
                        >
                            <button
                                class="flex items-center px-2 py-1 space-x-3 focus:outline-none"
                            >
                                <img
                                    src="/images/icon-youtube.jpg"
                                    class="w-8"
                                />
                                <div class="hover:underline">
                                    Youtube
                                </div>
                            </button>
                            <button
                                class="flex items-center px-2 py-1 space-x-3 focus:outline-none"
                            >
                                <img
                                    src="/images/icon-facebook.jpg"
                                    class="w-8"
                                />
                                <div class="hover:underline">
                                    Facebook
                                </div>
                            </button>
                            <button
                                class="flex items-center px-2 py-1 space-x-3 focus:outline-none"
                            >
                                <img
                                    src="/images/icon-tiktok.svg"
                                    class="w-8"
                                />
                                <div class="hover:underline">TikTok</div>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- side panel -->
        <template #side-panel>
            <storyboard-side-panel :title="$page.currentSideNavName">
                <template #navs>
                    <tab-button
                        :is-selected="currentSidePanelTab == 'comments'"
                        @on-selected="currentSidePanelTab = 'comments'"
                        class="flex-1"
                    >
                        Comments
                    </tab-button>
                    <tab-button
                        :is-selected="currentSidePanelTab == 'activities'"
                        @on-selected="currentSidePanelTab = 'activities'"
                        class="flex-1"
                    >
                    </tab-button>
                </template>

                <template #tab-details>
                    <div
                        v-if="currentSidePanelTab == 'comments'"
                        class="px-4 py-1"
                    >
                        <p
                            v-if="isCommentsLoading"
                            class="mt-4 text-center text-gray-600"
                        >
                            Loading . . .
                        </p>
                        <div v-else>
                            <frame-io-comment
                                v-for="comment in processedComments"
                                :key="comment.id"
                                :comment="comment"
                                :fps="frameioAsset.fps"
                                :user="currentUser"
                                @reply="onReply"
                                @edit="onCommentEdit"
                                @delete="onCommentDelete"
                            >
                                <template v-if="comment.has_replies">
                                    <frame-io-comment
                                        v-for="reply in comment.replies"
                                        :key="reply.id"
                                        :comment="reply"
                                        :fps="frameioAsset.fps"
                                        :user="currentUser"
                                        @reply="onReply"
                                        @edit="onCommentEdit"
                                        @delete="onCommentDelete"
                                    ></frame-io-comment>
                                </template>
                            </frame-io-comment>
                        </div>
                    </div>
                </template>
            </storyboard-side-panel>
        </template>
    </storyboard-layout>
</template>

<script>
import StoryboardLayout from '@/Components/Storyboard/StoryboardLayout'
import FrameioService from '@/Services/FrameioService'
import { getTimeFormat } from '@/Utilities/ConvertTimeFormatFromSecond'
import { userType } from '@/Utilities/User'
import { videoStatus } from '@/Utilities/VideoStatus'
import { activityDescription } from '@/Utilities/Activity'
import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'
import CreateActivityMutation from '@/Graphql/Order/Mutations/CreateActivity.gql'

export default {
    components: {
        StoryboardLayout,
        StoryboardInputTextarea: () =>
            import(
                /* webpackChunkName: 'StoryboardInputTextarea' */ '@/Components/Storyboard/StoryboardInputTextarea'
            ),
        StoryboardSidePanel: () =>
            import(
                /* webpackChunkName: 'StoryboardSidePanel' */ '@/Components/Storyboard/StoryboardSidePanel'
            ),
        TabButton: () =>
            import(
                /* webpackChunkName: 'TabButton' */ '@/Components/Tab/TabButton'
            ),
        FrameIoComment: () =>
            import(
                /* webpackChunkName: 'FrameIoComment' */ '@/Components/FrameIoComment'
            ),
        JetSecondaryButton: () =>
            import(
                /* webpackChunkName: 'JetSecondaryButton' */ '@/Jetstream/SecondaryButton'
            )
    },

    data() {
        return {
            isCommentInputPanel: 1,
            isCommentsSidePanel: false,
            isAddedComment: false,
            currentVideoTimestamp: 0,
            inputComment: {
                text: '',
                processing: false,
                error: '',
                isTimestamp: false
            },
            currentSidePanelTab: 'comments',
            isCommentsLoading: false,
            frameioVideo: {
                // eslint-disable-next-line no-undef
                id: route().params.id,
                processing: false
            },
            frameioAsset: {},
            comments: [],
            currentUser: null
        }
    },

    computed: {
        processedComments() {
            return this.comments
                .filter(comment => comment.parent_id == null)
                .map(comment => {
                    return {
                        ...comment,
                        replies: this.getReplies(comment.id)
                    }
                })
        },

        replies() {
            return this.comments.filter(comment => comment.parent_id != null)
        }
    },

    mounted() {
        // get asset details
        this.getFrameIoVideoAssetDetails()
        // get comment details
        this.getComments()
        // get the current video timestamp when video pauses
        this.$refs['video-player'].onpause = event => {
            this.currentVideoTimestamp = event.target.currentTime
        }
    },

    filters: {
        timeFormat(val) {
            return getTimeFormat(val)
        }
    },

    methods: {
        pauseVideo() {
            const videoPlayer = this.$refs['video-player']

            if (!videoPlayer.paused) {
                videoPlayer.pause()
            }
        },

        async getFrameIoVideoAssetDetails() {
            try {
                this.frameioVideo.processing = true

                const response = await FrameioService.getAssetDetailsById(
                    this.frameioVideo.id
                )

                if (response.data) {
                    this.frameioAsset = {
                        h264_1080_best: response.data.h264_1080_best,
                        fps: response.data.fps,
                        image_full: response.data.image_full,
                        label: response.data.label,
                        downloads: response.data.downloads
                    }

                    if (this.frameioAsset.label === 'approved') {
                        this.isCommentInputPanel = 3
                    }
                }
                this.frameioVideo.processing = false
            } catch (error) {
                console.log(error)
            }
        },

        async getComments() {
            try {
                this.isCommentsLoading = true

                // get user
                let response = await FrameioService.getCurrentAuthenticatedUser()
                if (response.data) {
                    this.currentUser = response.data
                }

                // get comments
                response = await FrameioService.getAllCommentsWithReplies(
                    this.frameioVideo.id
                )

                if (response.data) {
                    this.comments = response.data

                    // open the comments by default if there are comments
                    if (this.comments.length) {
                        this.openCommentInputSection()
                    }
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isCommentsLoading = false
            }
        },

        getReplies(parentId) {
            if (!parentId) {
                return []
            }
            return this.replies.filter(reply => reply.parent_id === parentId)
        },

        openCommentInputSection() {
            this.isCommentInputPanel = 2
            this.isCommentsSidePanel = true
        },

        closeAllCommentPanels() {
            this.isCommentsSidePanel = false
            this.isCommentInputPanel = 1
        },

        async sendComment() {
            if (this.inputComment.text == '') {
                this.inputComment.error = 'You can not send an empty message!'
                return
            }

            // submit
            this.inputComment.processing = true
            try {
                const response = await FrameioService.addComment(
                    this.frameioVideo.id,
                    this.inputComment.text,
                    this.inputComment.isTimestamp
                        ? this.currentVideoTimestamp * this.frameioAsset.fps
                        : null
                )
                if (response.data) {
                    this.comments.unshift(response.data)
                    // clear input
                    this.inputComment.text = ''
                    this.inputComment.error = ''
                    // clear video timestamp checkbox
                    this.inputComment.isTimestamp = false

                    // hide the validate option
                    this.isAddedComment = true

                    this.updateVideoStatusAndPendingSide()

                    this.createActivity(
                        activityDescription.clientCommentedOnVideo
                    )
                } else {
                    this.inputComment.error =
                        'Oops! Something went wrong. Please try again.'
                }
            } catch (error) {
                console.log(error)
                this.inputComment.error = 'Something went wrong'
            } finally {
                this.inputComment.processing = false
            }
        },

        onReply(comment) {
            this.comments.push(comment)

            this.updateVideoStatusAndPendingSide()

            this.createActivity(activityDescription.clientCommentedOnVideo)
        },

        onCommentEdit(currentComment) {
            this.comments.forEach(comment => {
                if (comment.id === currentComment.id) {
                    comment.text = currentComment.text
                }
            })
            // change status and pending side
            this.updateVideoStatusAndPendingSide()
        },

        onCommentDelete(deletedComment) {
            this.comments = this.comments.filter(
                comment => comment.id != deletedComment.id
            )
        },

        async updateVideoStatusAndPendingSide() {
            try {
                // submit video data
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateVideoMutation,
                    // Parameters
                    variables: {
                        id: this.$page.videoId,
                        video: {
                            status: videoStatus.video,
                            pending_side: userType.manager
                        }
                    }
                })
            } catch (error) {
                console.log(error)
            }
        },

        async createActivity(description) {
            await this.$apollo.mutate({
                mutation: CreateActivityMutation,
                variables: {
                    activity: {
                        description: description,
                        // eslint-disable-next-line no-undef
                        order_id: route().params.order,
                        user_id: this.$page.user.id
                    }
                }
            })
        },

        validateStep() {
            this.isCommentInputPanel = 3
            // show the close button
            this.isAddedComment = true

            this.updateVideoStatusAndPendingSide()

            this.createActivity(activityDescription.validatedVideo)
        },

        downloadVideo(resolutionProperty) {
            window.open(this.frameioAsset.downloads[resolutionProperty])
        }
    }
}
</script>

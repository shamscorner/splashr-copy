<template>
    <storyboard-layout
        no-left-panel
        :is-open-side-panel="isCommentsSidePanel"
        @close-side-panel="closeAllCommentPanels"
    >
        <div class="px-8 py-6">
            <!-- slide container -->
            <div
                class="flex bg-gray-300 aspect-w-16 aspect-h-10 2xl:aspect-h-9"
            >
                <iframe
                    frameBorder="0"
                    style="background-color: white"
                    :src="`https://drive.google.com/file/d/${gDoc.id}/preview`"
                    class="w-full h-full"
                ></iframe>
            </div>
            <!-- comment textarea container -->
            <div
                v-if="!isCommentInputPanel"
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
                v-else
                v-model="inputComment.text"
                :input-comment="inputComment"
                @send="sendComment"
                @close="isCommentInputPanel = false"
            >
                <template #info>
                    <div></div>
                </template>
            </storyboard-input-textarea>
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
                            <google-comment
                                v-for="comment in comments"
                                :key="comment.id"
                                :comment="comment"
                                :presentation-id="gDoc.id"
                                @delete="onCommentDelete"
                                @update="onCommentUpdate"
                                @reply="onReplyOnComment"
                            >
                                <template v-if="comment.replies.length">
                                    <google-comment
                                        v-for="reply in comment.replies"
                                        :key="reply.id"
                                        :comment="reply"
                                        :presentation-id="gDoc.id"
                                        :parent-comment-id="comment.id"
                                        @delete-reply="onReplyDelete"
                                        @update-reply="onReplyUpdate"
                                        @reply="onReplyOnComment"
                                    >
                                    </google-comment>
                                </template>
                            </google-comment>
                        </div>
                        <button-text
                            v-if="!!nextCommentsPageToken"
                            class="block w-full my-5 text-center text-gray-500"
                            :class="{
                                'text-gray-300 pointer-events-none': isLoadingMoreComments
                            }"
                            @click.native="
                                fetchCommentsForThisPresentation(true)
                            "
                        >
                            {{
                                isLoadingMoreComments
                                    ? 'Loading'
                                    : 'More comments'
                            }}
                        </button-text>
                    </div>
                </template>
            </storyboard-side-panel>
        </template>
    </storyboard-layout>
</template>

<script>
import StoryboardLayout from '@/Components/Storyboard/StoryboardLayout'
import GDocApiService from '@/Services/GDocApiService'
import GoogleFileCommentMixin from '@/Mixins/GoogleFileComment'
import { videoStatus } from '@/Utilities/VideoStatus'
import { activityDescription } from '@/Utilities/Activity'

export default {
    mixins: [GoogleFileCommentMixin],

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
        GoogleComment: () =>
            import(
                /* webpackChunkName: 'GoogleComment' */ '@/Components/GoogleComment'
            ),
        ButtonText: () =>
            import(
                /* webpackChunkName: 'ButtonText' */ '@/Components/ButtonText'
            )
    },

    data() {
        return {
            isCommentInputPanel: false,
            isCommentsSidePanel: false,
            isAddedComment: false,
            inputComment: {
                text: '',
                processing: false,
                error: '',
                isTimestamp: false
            },
            currentSidePanelTab: 'comments',
            isCommentsLoading: false,
            gDoc: {
                // eslint-disable-next-line no-undef
                id: route().params.id,
                processing: false
            },
            comments: [],
            nextCommentsPageToken: null,
            isLoadingMoreComments: false,
            videoStatus: videoStatus.doc,
            activityDescription:
                activityDescription.clientCommentedOnCreativeIdeas
        }
    },

    mounted() {
        this.fetchCommentsForThisDoc()
    },

    methods: {
        async fetchCommentsForThisDoc(isLoadMoreComments) {
            try {
                if (!isLoadMoreComments) {
                    // show the main loading
                    this.isCommentsLoading = true
                } else {
                    this.isLoadingMoreComments = true
                }

                const response = await GDocApiService.getCommentsOfAGoogleDoc(
                    this.gDoc.id,
                    this.nextCommentsPageToken
                )
                // console.log(response.data);
                if (response.data) {
                    if (isLoadMoreComments) {
                        this.comments = this.comments.concat(
                            response.data.comments
                        )
                        this.isLoadingMoreComments = false
                    } else {
                        this.comments = response.data.comments
                        this.isCommentsLoading = false
                    }
                    this.nextCommentsPageToken = response.data.nextPageToken

                    // if there are comments open the comment section by default
                    if (this.comments.length) {
                        this.openCommentInputSection()
                    }
                }
            } catch (error) {
                console.log(error)
            }
        },

        openCommentInputSection() {
            this.isCommentInputPanel = true
            this.isCommentsSidePanel = true
        },

        closeAllCommentPanels() {
            this.isCommentsSidePanel = false
            this.isCommentInputPanel = false
        },

        async sendComment() {
            if (this.inputComment.text == '') {
                return
            }

            try {
                this.inputComment.processing = true
                const response = await GDocApiService.createComment(
                    this.gDoc.id,
                    null,
                    this.inputComment.text
                )

                if (response.data) {
                    this.comments.unshift(response.data)
                    this.inputComment.error = ''
                    this.inputComment.text = ''

                    // hide the validate option
                    this.isAddedComment = true
                } else {
                    this.inputComment.error =
                        'Oops! Something went wrong. Try again.'
                }
                this.inputComment.processing = false

                this.updateVideoStatusAndPendingSide(
                    this.$page.videoId,
                    this.videoStatus
                )

                this.createActivity(
                    // eslint-disable-next-line no-undef
                    route().params.order,
                    activityDescription.clientCommentedOnCreativeIdeas
                )
            } catch (error) {
                console.log(error)
            }
        },

        validateStep() {
            // show the close button
            this.isAddedComment = true

            this.updateVideoStatusAndPendingSide(
                this.$page.videoId,
                this.videoStatus
            )

            this.createActivity(
                // eslint-disable-next-line no-undef
                route().params.order,
                activityDescription.validatedCreativeIdeas
            )
        }
    }
}
</script>

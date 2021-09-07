<template>
    <storyboard-layout
        :is-open-side-panel="isCommentsSidePanel"
        @close-side-panel="closeAllCommentPanels"
    >
        <template #left-panel>
            <button
                v-for="(slide, index) in slides"
                :key="index"
                class="group focus:outline-none"
                @click="
                    currentSlide = {
                        index: index,
                        ...slide
                    }
                "
            >
                <img
                    v-if="slide.thumbnail"
                    :src="slide.thumbnail"
                    class="object-cover transition border group-hover:ring-2"
                    :class="[
                        index === currentSlide.index
                            ? 'border-gray-400 shadow-lg w-52 h-24'
                            : 'w-36 h-20 opacity-70'
                    ]"
                />
                <div v-else class="h-20 bg-gray-200 w-36 animate-pulse"></div>
                <div
                    class="mt-2 text-gray-400 transition group-hover:text-indigo-500"
                >
                    Slide {{ index + 1 }}
                </div>
            </button>
        </template>

        <!-- main contents section -->
        <div class="px-8 py-6">
            <!-- slide container -->
            <div
                class="flex bg-gray-300 aspect-w-16 aspect-h-9"
                :class="{ 'animate-pulse': isThumbnailLoading }"
            >
                <div
                    v-if="isThumbnailLoading"
                    class="flex items-center justify-center w-full h-full text-center text-gray-500"
                >
                    Slide is loading . . .
                </div>
                <img
                    v-else-if="slides.length > 0 && !presentation.processing"
                    :src="currentSlide.thumbnail"
                    class="w-full h-full"
                    alt="Current Slide"
                />
            </div>
            <!-- next and previous button -->
            <div class="flex justify-center mt-4 space-x-8">
                <button
                    class="text-gray-400 transition hover:text-gray-900 focus:outline-none focus:ring-2"
                    :disabled="isPreviousButtonDisable"
                    :class="{
                        'opacity-25': isPreviousButtonDisable
                    }"
                    @click="loadPreviousSlide"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        class="w-8 h-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                        />
                    </svg>
                </button>
                <button
                    class="text-gray-400 transition hover:text-gray-900 focus:outline-none focus:ring-2"
                    :disabled="isNextButtonDisable"
                    :class="{ 'opacity-25': isNextButtonDisable }"
                    @click="loadNextSlide"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        class="w-8 h-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7"
                        />
                    </svg>
                </button>
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
                    <div
                        class="px-3 py-1 text-sm text-white rounded-md bg-splashr-violet-light"
                    >
                        Slide
                        {{ currentSlide ? currentSlide.index + 1 : '...' }}
                    </div>
                </template>
            </storyboard-input-textarea>
        </div>

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
                                :presentation-id="presentation.id"
                                @delete="onCommentDelete"
                                @update="onCommentUpdate"
                                @reply="onReplyOnComment"
                            >
                                <template v-if="comment.replies.length">
                                    <google-comment
                                        v-for="reply in comment.replies"
                                        :key="reply.id"
                                        :comment="reply"
                                        :presentation-id="presentation.id"
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
import GDocApiService from '@/Services/GDocApiService'
import StoryboardLayout from '@/Components/Storyboard/StoryboardLayout'
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
            currentSidePanelTab: 'comments',
            presentation: {
                // eslint-disable-next-line no-undef
                id: route().params.id,
                processing: false
            },
            slides: [],
            currentSlide: null,
            isNextButtonDisable: false,
            isPreviousButtonDisable: false,
            inputComment: {
                text: '',
                processing: false,
                error: ''
            },
            comments: [],
            nextCommentsPageToken: null,
            isCommentsLoading: false,
            isThumbnailLoading: false,
            isLoadingMoreComments: false,
            isCommentInputPanel: false,
            isCommentsSidePanel: false,
            isAddedComment: false,
            videoStatus: videoStatus.slide,
            activityDescription: activityDescription.clientCommentedOnStoryboard
        }
    },

    watch: {
        currentSlide: {
            immediate: true,
            handler() {
                if (this.currentSlide) {
                    if (this.currentSlide.index == 0) {
                        this.isPreviousButtonDisable = true
                    } else if (
                        this.currentSlide.index ==
                        this.slides.length - 1
                    ) {
                        this.isNextButtonDisable = true
                    } else {
                        this.isPreviousButtonDisable = false
                        this.isNextButtonDisable = false
                    }

                    // get the thumbnail for the current slide
                    this.getThumbnailForCurrentSlide()

                    // fetch comments
                    this.fetchCommentsForThisPresentation(false)
                }
            }
        }
    },

    mounted() {
        this.getSlidesByPresentationId()
    },

    methods: {
        openCommentInputSection() {
            this.isCommentInputPanel = true
            this.isCommentsSidePanel = true
        },

        closeAllCommentPanels() {
            this.isCommentsSidePanel = false
            this.isCommentInputPanel = false
        },

        async getSlidesByPresentationId() {
            try {
                this.presentation.processing = true

                const response = await GDocApiService.getSlideObjectIdOfAPresentation(
                    this.presentation.id
                )

                this.slides = response.data

                if (this.slides.length > 0) {
                    // set the first slide as the current slide initially
                    this.setCurrentSlide(0)
                    // open the comments section if there are comments
                    this.openCommentInputSection()
                }

                this.presentation.processing = false

                // get other thumbnails
                this.slides.forEach(slide => {
                    this.getThumbnailBySlideObjectId(slide)
                })
            } catch (error) {
                console.log(error)
            }
        },

        async getThumbnailBySlideObjectId(currentSlide) {
            try {
                const response = await GDocApiService.getThumbnailOfASlide(
                    this.presentation.id,
                    currentSlide.objectId
                )
                this.slides.find(slide => {
                    if (slide.objectId === currentSlide.objectId) {
                        slide.thumbnail = response.data.url
                    }
                })
            } catch (error) {
                console.log(error)
            }
        },

        async getThumbnailForCurrentSlide() {
            // check already in the cache or not
            // if it is on the cache, then don't fetch again
            if (this.isThumbnailCached()) {
                this.currentSlide.thumbnail = this.slides.find(
                    slide => slide.objectId === this.currentSlide.objectId
                ).thumbnail
                return
            }

            this.isThumbnailLoading = true
            try {
                const response = await GDocApiService.getThumbnailOfASlide(
                    this.presentation.id,
                    this.currentSlide.objectId
                )
                this.currentSlide.thumbnail = response.data.url
            } catch (error) {
                console.log(error)
            } finally {
                this.isThumbnailLoading = false
            }
        },

        isThumbnailCached() {
            return this.slides.some(
                slide =>
                    slide.objectId === this.currentSlide.objectId &&
                    slide.thumbnail
            )
        },

        loadPreviousSlide() {
            if (this.currentSlide.index == 0) {
                return
            }
            this.setCurrentSlide(this.currentSlide.index - 1)
        },

        loadNextSlide() {
            if (this.currentSlide.index == this.slides.length - 1) {
                return
            }
            this.setCurrentSlide(this.currentSlide.index + 1)
        },

        setCurrentSlide(index) {
            this.currentSlide = {
                ...this.slides[index],
                index: index
            }
        },

        async fetchCommentsForThisPresentation(isLoadMoreComments) {
            try {
                if (!isLoadMoreComments) {
                    // show the main loading
                    this.isCommentsLoading = true
                } else {
                    this.isLoadingMoreComments = true
                }

                const response = await GDocApiService.getCommentsOfAPageInAPresentation(
                    this.presentation.id,
                    this.currentSlide.objectId,
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
                }
            } catch (error) {
                console.log(error)
            }
        },

        async sendComment() {
            if (this.inputComment.text == '') {
                return
            }

            try {
                this.inputComment.processing = true
                const response = await GDocApiService.createComment(
                    this.presentation.id,
                    this.currentSlide.objectId,
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
                    activityDescription.clientCommentedOnStoryboard
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
                activityDescription.validatedStoryboard
            )
        }
    }
}
</script>

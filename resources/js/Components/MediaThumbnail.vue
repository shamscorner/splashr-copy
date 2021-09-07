<template>
    <inertia-link
        :href="getVideoDetailsUrl"
        class="relative block p-1 transition w-44 group"
        style="z-index: 10;"
        @click.prevent="openMediaThumbnail"
    >
        <div
            v-if="!media.is_visited"
            class="absolute w-3 h-3 rounded-full bottom-3 right-2 bg-splashr-blue-light"
        ></div>
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 180 153"
            class="absolute top-0 left-0 w-full h-full"
            style="z-index: -5;"
        >
            <g fill="none" fill-rule="evenodd">
                <g fill="#29243A" fill-rule="nonzero">
                    <g>
                        <path
                            d="M6.4 0h35.883c1.166 0 2.318.255 3.375.748l19.136 8.917c1.058.493 2.21.749 3.376.749H173.6c3.535 0 6.4 2.869 6.4 6.408v129.77c0 3.539-2.865 6.408-6.4 6.408H6.4c-3.535 0-6.4-2.87-6.4-6.408V6.408C0 2.87 2.865 0 6.4 0z"
                            transform="translate(-680 -630) translate(680 630)"
                        />
                    </g>
                </g>
            </g>
        </svg>
        <div
            class="w-full mt-4 italic bg-white rounded-md h-22"
            :class="videoStatusColor"
        >
            <div
                v-if="isLoading"
                class="flex items-center justify-center bg-gray-800 h-22"
            >
                <loading-icon class="w-8 h-8 text-gray-400"></loading-icon>
            </div>

            <div
                v-else-if="!frameIoVideos.value.length && !isLoading"
                class="flex flex-col items-center justify-center p-2 space-y-1"
            >
                <component :is="statusInfo.component" class="w-12"></component>
                <div
                    v-html="statusInfo.label"
                    class="flex items-center h-8 text-xs text-center"
                ></div>
            </div>

            <div v-else class="bg-gray-800">
                <!-- 1 video -->
                <div v-if="frameIoVideos.value.length == 1">
                    <img
                        :src="frameIoVideos.value[0].thumb"
                        alt="Thumb"
                        class="object-cover w-full rounded-md h-22"
                    />
                </div>
                <!-- 2 videos -->
                <div
                    v-else-if="frameIoVideos.value.length == 2"
                    class="flex gap-1"
                >
                    <img
                        :src="frameIoVideos.value[0].thumb"
                        alt="Thumb"
                        class="object-cover rounded-md w-29 h-22"
                    />
                    <img
                        :src="frameIoVideos.value[1].thumb"
                        alt="Thumb"
                        class="object-cover w-12 rounded-md h-22"
                    />
                </div>
                <!-- 3 videos -->
                <div
                    v-else-if="frameIoVideos.value.length == 3"
                    class="flex gap-1"
                >
                    <img
                        :src="frameIoVideos.value[0].thumb"
                        alt="Thumb"
                        class="object-cover rounded-md w-29 h-22"
                    />
                    <div class="flex flex-col gap-1">
                        <img
                            :src="frameIoVideos.value[1].thumb"
                            alt="Thumb"
                            class="object-cover w-12 h-10 rounded-md"
                        />
                        <img
                            :src="frameIoVideos.value[2].thumb"
                            alt="Thumb"
                            class="object-cover w-12 rounded-md h-11"
                        />
                    </div>
                </div>
                <!-- 3+ videos -->
                <div
                    v-else-if="frameIoVideos.value.length > 3"
                    class="flex gap-1"
                >
                    <img
                        :src="frameIoVideos.value[0].thumb"
                        alt="Thumb"
                        class="object-cover rounded-md w-29 h-22"
                    />
                    <div class="flex flex-col gap-1">
                        <img
                            :src="frameIoVideos.value[1].thumb"
                            alt="Thumb"
                            class="object-cover w-12 h-10 rounded-md"
                        />
                        <div
                            class="relative flex items-center justify-center w-12 rounded-md h-11"
                        >
                            <div
                                class="absolute mr-2 text-sm font-semibold text-gray-600"
                            >
                                {{ frameIoVideos.value.length - 2 }}+
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 29 15"
                                class="w-10"
                            >
                                <g fill="none" fill-rule="evenodd">
                                    <g fill="#E1E1E1" fill-rule="nonzero">
                                        <g>
                                            <path
                                                d="M165.24 76l-6.922 4.877v-3.383c0-.686-.556-1.242-1.242-1.242h-18.834c-.686 0-1.242.556-1.242 1.242v11.685c0 .685.556 1.241 1.242 1.241h18.835c.685 0 1.241-.556 1.241-1.241v-3.165l6.922 4.406V76z"
                                                transform="translate(-617 -466) translate(480 390)"
                                            />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-1.5 py-1 text-white h-12">
            <div class="text-sm truncate">{{ media.name }}</div>
            <div
                v-if="frameIoVideos.value.length"
                class="text-xs text-gray-300"
            >
                {{ frameIoVideos.value.length }}
                {{ frameIoVideos.value.length > 1 ? 'videos' : 'video' }}
            </div>
        </div>

        <button
            v-if="draft"
            class="hidden group-hover:block absolute w-5 h-5 top-6 right-2 p-0.5 text-white rounded-full bg-splashr-indigo-deep hover:bg-red-500 transition focus:outline-none focus:bg-red-600"
            @click.prevent="$emit('delete-draft', media)"
        >
            <close-icon></close-icon>
        </button>
    </inertia-link>
</template>

<script>
import CloseIcon from '@/Components/Icons/CloseIcon'
import { videoStatus } from '@/Utilities/VideoStatus'
import { orderDetailsTabs } from '@/Utilities/index'
import FrameIoVideoMixin from '@/Mixins/FrameIoVideo'

export default {
    mixins: [FrameIoVideoMixin],

    components: {
        CloseIcon,
        LoadingIcon: () =>
            import(
                /* webpackChunkName: 'LoadingIcon' */ '@/Components/Icons/LoadingIcon'
            ),
        PendingCreativeProposal: () =>
            import(
                /* webpackChunkName: 'PendingCreativeProposal' */ '@/Components/Icons/PendingCreativeProposal'
            ),
        CreativeProposalReceived: () =>
            import(
                /* webpackChunkName: 'CreativeProposalReceived' */ '@/Components/Icons/CreativeProposalReceived'
            ),
        PendingVideoProposal: () =>
            import(
                /* webpackChunkName: 'PendingVideoProposal' */ '@/Components/Icons/PendingVideoProposal'
            ),
        DraftIcon: () =>
            import(
                /* webpackChunkName: 'DraftIcon' */ '@/Components/Icons/DraftIcon'
            )
    },

    props: {
        media: {
            type: Object,
            required: false
        },
        draft: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    data() {
        return {
            isLoading: false
        }
    },

    computed: {
        statusInfo() {
            if (this.draft) {
                return {
                    component: 'draft-icon',
                    label: `<div>Incomplete brief</div>`
                }
            }

            switch (this.media.status) {
                case 2:
                    return {
                        component: 'pending-creative-proposal',
                        label: `<div>Pending <span class="font-semibold">Creative Proposal</span> from your account manager</div>`
                    }
                case 3:
                case 5:
                    return {
                        component: 'creative-proposal-received',
                        label: `<div><span class="font-semibold">Creative Proposal</span> received!</div>`
                    }
                case 4:
                case 6:
                    return {
                        component: 'pending-video-proposal',
                        label: `<div>Pending <span class="font-semibold">Video Proposal</span> from your account manager</div>`
                    }
                default:
                    return {
                        component: '',
                        label: ''
                    }
            }
        },

        isVideoProposal() {
            return (
                this.media.status == videoStatus.video ||
                this.media.status == videoStatus.finished
            )
        },

        videoStatusColor() {
            switch (this.media.status) {
                case videoStatus.doc:
                case videoStatus.slide:
                    return 'text-splashr-violet-light'
                default:
                    return ''
            }
        },

        getVideoDetailsUrl() {
            if (this.draft) {
                // eslint-disable-next-line no-undef
                return route('videos.steps', {
                    video: this.media.id,
                    step: 'General'
                })
            }

            let params = {
                video: this.media.id,
                currentTab: orderDetailsTabs.brief
            }

            if (this.media.status == videoStatus.slide) {
                params.currentTab = orderDetailsTabs.documents
            } else if (this.media.status == videoStatus.doc) {
                params.currentTab = orderDetailsTabs.documents
            } else if (this.media.status == videoStatus.video) {
                params.currentTab = orderDetailsTabs.videos
            }

            if (this.$page.currentCampaignId) {
                params.campaign = this.$page.currentCampaignId
            }

            // eslint-disable-next-line no-undef
            return route('videos.show', params)
        }
    },

    async mounted() {
        // * fetch the videos of this media from frameio
        if (this.isVideoProposal && !this.draft) {
            if (this.media.order && this.media.order.frameio_project_id) {
                this.isLoading = true
                await this.getFrameIoVideosByProject(
                    this.media.order.frameio_project_id,
                    this.$page.company.id,
                    this.media.order.video_id,
                    this.media.order.id,
                    true
                )
                this.isLoading = false
            }
        }
    },

    methods: {
        openMediaThumbnail() {
            if (this.draft) {
                this.$inertia.replace(this.getVideoDetailsUrl)
                return
            }

            this.$inertia.visit(this.getVideoDetailsUrl)
        }
    }
}
</script>

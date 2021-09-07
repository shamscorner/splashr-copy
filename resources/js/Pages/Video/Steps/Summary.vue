<template>
    <video-step-layout :active="currentPageNumber" @submit="submitFormData">
        <video-summary
            :edit-url="{
                general: route('videos.update', {
                    video: $page.video.id,
                    step: 'General',
                    sidebar: this.$page.isSideNavVisible
                        ? this.$page.isSideNavVisible
                        : 'true',
                    edit: true
                }),
                assets: route('videos.update', {
                    video: $page.video.id,
                    step: 'Assets',
                    sidebar: this.$page.isSideNavVisible
                        ? this.$page.isSideNavVisible
                        : 'true',
                    edit: true
                }),
                details: route('videos.update', {
                    video: $page.video.id,
                    step: 'Details',
                    sidebar: this.$page.isSideNavVisible
                        ? this.$page.isSideNavVisible
                        : 'true',
                    edit: true
                })
            }"
            :video="$page.video"
            :folders="$page.data.assets"
            class="max-w-6xl"
        ></video-summary>
    </video-step-layout>
</template>

<script>
import VideoStepLayout from '@/Layouts/VideoStepLayout'
import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'
import VideoStepInOrderProcessMixin from '@/Mixins/VideoStepInOrderProcess'
import VideoSummary from '@/Components/VideoSummary/VideoSummary'

export default {
    mixins: [VideoStepInOrderProcessMixin],

    components: {
        VideoStepLayout,
        VideoSummary
    },

    data() {
        return {
            currentPageNumber: 4
        }
    },

    computed: {
        publishingPlatforms() {
            return this.$page.video.platforms
                .map(platform => platform.name)
                .join(', ')
        },
        videoPurposes() {
            return this.$page.video.purposes
                .map(purpose => purpose.name)
                .join(', ')
        }
    },

    methods: {
        async submit(payload) {
            // submit
            await this.$apollo.mutate({
                // Mutation
                mutation: UpdateVideoMutation,
                // Parameters
                variables: {
                    id: this.$page.video.id,
                    video: {
                        name: payload.videoName
                    }
                }
            })
            return true
        }
    }
}
</script>

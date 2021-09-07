<template>
    <div>
        <empty-message v-if="!videos.length" class="mt-10">
            No video found.<br />
            Videos will be added from your Frame.io project. <br />
            Be sure to add a status to your videos on Frame.io
        </empty-message>
        <div v-else class="flex flex-wrap">
            <video-box
                v-for="video in videos"
                :key="video.id"
                :video="{
                    ...video,
                    videoBoxThumbnail: video.thumb,
                    redirectTo: getVideoUrl(video.id)
                }"
                :is-client="isClient"
                @toggle-type="toggleType"
                class="mx-2.5 my-2.5"
            ></video-box>
        </div>
    </div>
</template>

<script>
import VideoBox from '@/Components/VideoBox'
import EmptyMessage from '@/Components/EmptyMessage'

export default {
    components: {
        VideoBox,
        EmptyMessage
    },

    props: {
        videos: {
            type: Array,
            required: true
        },
        isClient: {
            type: Boolean,
            required: false,
            default: false
        },
        orderId: {
            type: String,
            required: false,
            default: ''
        }
    },

    methods: {
        getVideoUrl(videoId) {
            if (this.isClient) {
                // eslint-disable-next-line no-undef
                return route('orders.video.frame-io', {
                    order: this.orderId,
                    id: videoId
                })
            }
            return `https://app.frame.io/player/${videoId}`
        },

        toggleType(payload) {
            this.$emit('toggle-type', payload)
        }
    }
}
</script>

<template>
    <div
        class="inline px-4 py-1 text-lg font-semibold capitalize rounded-full"
        :class="[
            sessionType === videoSessionType.motion
                ? 'bg-splashr-blue-light text-splashr-violet-deep'
                : 'bg-splashr-violet-light text-white'
        ]"
    >
        {{ sessionType }}
    </div>
</template>

<script>
import { videoSessionType } from '@/Utilities/VideoStatus'

export default {
    props: {
        sessionType: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            videoSessionType
        }
    }
}
</script>

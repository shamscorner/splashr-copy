<template>
    <div
        class="relative block pb-6 pl-10 pr-4 border-l border-gray-400 focus:outline-none"
    >
        <div class="text-lg font-bold">
            {{ title }}
        </div>
        <div class="text-black">
            <slot></slot>
        </div>
        <div class="text-sm text-gray-500 mt-0.5">
            <slot name="footer"></slot>
        </div>
        <jet-avatar
            :src="leadingImage"
            alt="Profile"
            :name="title"
            class="absolute top-0 w-12 h-12 font-semibold -left-6"
        ></jet-avatar>
    </div>
</template>

<script>
import JetAvatar from '@/Jetstream/Avatar'

export default {
    components: {
        JetAvatar
    },

    props: {
        title: {
            type: String,
            required: true
        },
        leadingImage: {
            type: String,
            required: false,
            default: ''
        }
    }
}
</script>

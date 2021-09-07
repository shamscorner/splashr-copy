<template>
    <div class="p-3 mt-5 border rounded-lg">
        <div class="flex items-start space-x-3">
            <jet-avatar
                :src="$page.user.profile_photo_url"
                :alt="`${$page.user.first_name} ${$page.user.last_name}`"
                :name="`${$page.user.first_name} ${$page.user.last_name}`"
                class="w-10 h-10 font-semibold border"
            ></jet-avatar>
            <textarea
                :value="value"
                rows="2"
                placeholder="Add a comment"
                class="flex-1 placeholder-gray-400 border-none focus:outline-none focus:ring-0"
                @input="$emit('input', $event.target.value)"
            ></textarea>
            <button
                class="text-gray-500 transition hover:text-gray-900 focus:outline-none focus:ring-1"
                @click="$emit('close')"
            >
                <close-icon class="w-6 h-6"></close-icon>
            </button>
        </div>
        <div class="flex items-center justify-between mt-3">
            <slot name="info"></slot>
            <button
                class="px-6 py-1 text-lg text-white transition bg-purple-900 rounded-full hover:bg-splashr-violet-deep focus:outline-none focus:ring-2"
                :class="{
                    'opacity-25': inputComment.processing
                }"
                :disabled="inputComment.processing"
                @click="$emit('send')"
            >
                Send
            </button>
        </div>
    </div>
</template>

<script>
import JetAvatar from '@/Jetstream/Avatar'

export default {
    components: {
        JetAvatar,
        CloseIcon: () =>
            import(
                /* webpackChunkName: 'CloseIcon' */ '@/Components/Icons/CloseIcon'
            )
    },

    props: {
        value: {
            type: String,
            required: false,
            default: ''
        },

        inputComment: {
            type: Object,
            required: true
        }
    }
}
</script>

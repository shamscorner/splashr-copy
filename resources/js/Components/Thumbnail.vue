<template>
    <div class="bg-gray-200 h-28">
        <img
            v-if="src"
            :src="src"
            :alt="caption"
            class="object-cover w-full h-full"
        />
        <div
            v-else
            class="flex items-center justify-center w-full h-full animate-pulse"
        >
            Loading...
        </div>
        <p v-if="caption" class="mt-2 text-sm text-center">
            {{ caption | short }}
        </p>
    </div>
</template>

<script>
export default {
    props: {
        src: {
            type: String,
            required: true
        },
        caption: {
            type: String,
            required: false,
            default: ''
        }
    },

    filters: {
        short(str) {
            return str.substring(0, 12) + '...'
        }
    }
}
</script>

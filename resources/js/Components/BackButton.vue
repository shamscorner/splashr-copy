<template>
    <button
        class="p-2 text-gray-400 transition hover:text-gray-700 focus:outline-none focus:ring-2"
    >
        <loading-icon v-if="isLoading" class="w-5 h-5"></loading-icon>
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="w-5 h-5"
        >
            <path
                fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd"
            />
        </svg>
    </button>
</template>

<script>
export default {
    components: {
        LoadingIcon: () =>
            import(
                /* webpackChunkName: 'LoadingIcon' */ '@/Components/Icons/LoadingIcon'
            )
    },

    props: {
        isLoading: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

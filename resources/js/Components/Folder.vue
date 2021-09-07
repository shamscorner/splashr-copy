<template>
    <button
        class="flex items-center gap-3 px-3 py-2 font-semibold text-gray-600 bg-white border rounded-md hover:ring-2"
    >
        <svg
            v-if="!isLoading"
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="w-8 h-8"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
            />
        </svg>

        <loading-icon v-else class="w-7 h-7"></loading-icon>

        <div>
            <slot></slot>
        </div>
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

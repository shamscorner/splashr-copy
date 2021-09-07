<template>
    <div
        class="absolute top-0 bottom-0 left-0 right-0 z-50 overflow-hidden bg-gray-900 bg-opacity-60"
    >
        <div
            class="relative grid h-screen grid-cols-1 md:grid-cols-2 xl:grid-cols-3"
            @click.self="$emit('on-close')"
        >
            <div
                class="h-full overflow-hidden bg-white shadow-lg md:col-start-2 md:col-end-3 xl:col-start-3 xl:col-end-4"
            >
                <div class="flex justify-end px-4 pt-4 pb-2">
                    <button
                        class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2"
                        @click="$emit('on-close')"
                    >
                        <close-icon class="w-6 h-6"></close-icon>
                    </button>
                </div>
                <!-- header -->
                <div class="px-4">
                    <h1 class="text-xl font-semibold">
                        <slot name="header"></slot>
                    </h1>
                </div>
                <div class="flex flex-col h-full">
                    <!-- tabs section -->
                    <div class="mt-5 border-b">
                        <slot name="tabs"></slot>
                    </div>
                    <!-- tab content -->
                    <div class="flex-1 overflow-y-auto">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CloseIcon from '@/Components/Icons/CloseIcon'

export default {
    components: {
        CloseIcon
    },

    mounted() {
        document.addEventListener('keyup', event => {
            // close the popup if the user presses escape button
            if (event.key == 'Escape') {
                this.$emit('on-close')
            }
        })
    }
}
</script>

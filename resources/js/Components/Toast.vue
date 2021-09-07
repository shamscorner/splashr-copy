<template>
    <portal to="modal" class="relative">
        <div
            class="absolute flex items-center justify-center w-full text-white bottom-6"
            style="z-index: 99999;"
        >
            <div
                class="px-5 py-3 rounded-md shadow-lg animate-bounce"
                :class="getBackgroundClass"
            >
                <slot></slot>
            </div>
        </div>
    </portal>
</template>

<script>
export default {
    props: {
        role: {
            type: String,
            required: false,
            default: 'success'
        }
    },

    computed: {
        getBackgroundClass() {
            if (this.role === 'danger') {
                return 'bg-red-500'
            }
            return 'bg-green-500'
        }
    },

    mounted() {
        setTimeout(() => {
            this.$emit('close')
        }, 5000)
    }
}
</script>

<template>
    <div
        class="relative flex flex-col p-4 space-y-6 bg-white border rounded-md lg:p-8"
    >
        <inertia-link
            v-if="editUrl"
            :href="editUrl"
            class="ml-auto uppercase lg:absolute lg:right-8 lg:top-8"
        >
            <jet-secondary-button>Edit</jet-secondary-button>
        </inertia-link>
        <slot></slot>
    </div>
</template>

<script>
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

export default {
    components: {
        JetSecondaryButton
    },

    props: {
        editUrl: {
            type: String,
            required: false,
            default: '#'
        }
    }
}
</script>

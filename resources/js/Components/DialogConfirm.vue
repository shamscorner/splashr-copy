<template>
    <jet-confirmation-modal :show="show" @close="$emit('on-close')">
        <template #title>
            <h1 class="font-semibold">Remove Manager!</h1>
        </template>

        <template #content>
            Do you really want to remove this manager?
        </template>

        <template #footer>
            <jet-secondary-button @click.native="$emit('on-close')">
                Nevermind
            </jet-secondary-button>

            <jet-danger-button
                class="ml-2 rounded-full"
                @click.native="$emit('on-remove')"
            >
                Remove
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetDangerButton from '@/Jetstream/DangerButton'

export default {
    components: {
        JetConfirmationModal,
        JetSecondaryButton,
        JetDangerButton
    },

    props: {
        show: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

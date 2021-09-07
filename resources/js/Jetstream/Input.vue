<template>
    <input
        class="placeholder-gray-500 rounded shadow-sm placeholder-opacity-60"
        :class="[isValid ? 'border-gray-400' : 'border-red-500']"
        :value="value"
        @input="$emit('input', $event.target.value)"
        ref="input"
    />
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            required: false,
            default: ''
        },
        isValid: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    methods: {
        focus() {
            this.$refs.input.focus()
        }
    }
}
</script>

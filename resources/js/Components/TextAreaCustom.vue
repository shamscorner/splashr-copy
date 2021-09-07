<template>
    <textarea
        class="p-2 placeholder-gray-500 border-gray-400 rounded shadow-sm placeholder-opacity-60 focus:outline-none"
        :value="value"
        :rows="rows"
        :cols="cols"
        @input="$emit('input', $event.target.value)"
        ref="textarea"
    ></textarea>
</template>

<script>
export default {
    props: {
        rows: {
            type: Number,
            required: false,
            default: 5
        },
        cols: {
            type: Number,
            required: false,
            default: 20
        },
        value: {
            type: String,
            required: false,
            default: ''
        }
    },

    methods: {
        focus() {
            this.$refs.textarea.focus()
        }
    }
}
</script>

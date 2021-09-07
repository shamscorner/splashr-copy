<template>
    <div class="flex items-center w-full space-x-2 lg:w-2/3">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="w-5 h-5 mt-0.5 text-gray-400"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
            />
        </svg>
        <input
            type="text"
            class="w-full placeholder-gray-500 transition border-gray-400 border-opacity-0 rounded placeholder-opacity-60 hover:border-opacity-100"
            :class="classes"
            :value="value"
            @input="$emit('input', $event.target.value)"
            ref="input"
            @focus="focus"
            @click="selectAllText"
            @blur="$emit('out-focus', $event.target.value)"
        />
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            required: false,
            default: ''
        },
        classes: {
            type: String,
            required: false,
            default: ''
        }
    },

    methods: {
        focus() {
            this.$refs.input.focus()
        },

        selectAllText(event) {
            event.target.setSelectionRange(0, event.target.value.length)
        }
    }
}
</script>

<template>
    <div
        class="flex items-center w-full pl-4 text-gray-400 border-2 rounded sm:w-1/2 md:w-1/3 focus-within:text-gray-600 hover:border-gray-500"
    >
        <div>
            <svg
                v-if="!hideIcon"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-6 h-6"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
            </svg>
            <slot name="icon"></slot>
        </div>
        <input
            class="w-full text-lg placeholder-gray-400 border-none focus:outline-none focus:ring-0"
            type="text"
            :placeholder="url"
            :value="value"
            @input="$emit('input', $event.target.value)"
        />
    </div>
</template>

<script>
export default {
    props: {
        placeholder: {
            type: String,
            required: false,
            default: ''
        },
        hideIcon: {
            type: Boolean,
            required: false,
            default: false
        },
        value: {
            type: String,
            required: false,
            default: ''
        }
    },

    data() {
        return {
            url: this.placeholder ? this.placeholder : this.value
        }
    }
}
</script>

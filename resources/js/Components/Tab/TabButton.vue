<template>
    <button
        class="px-4 py-2 font-semibold text-gray-500 transition border-b-4 border-transparent hover:bg-gray-100 focus:outline-none focus:border-purple-600"
        :class="{
            'text-gray-900 border-purple-500': isSelected
        }"
        @click="$emit('on-selected')"
    >
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        isSelected: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

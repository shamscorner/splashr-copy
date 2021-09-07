<template>
    <inertia-link
        :href="to"
        class="block px-3 py-2 transition rounded-md hover:ring-2 focus:outline-none focus:ring-2"
        :class="{ 'bg-opacity-25 bg-splashr-violet-light': isSelected }"
    >
        <slot></slot>
    </inertia-link>
</template>

<script>
export default {
    props: {
        to: {
            type: String,
            required: false,
            default: '#'
        },
        isSelected: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

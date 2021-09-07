<template>
    <inertia-link
        :href="to"
        class="block transition rounded-md ring-offset-2 hover:ring-2 focus:outline-none focus:ring-2"
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
        }
    }
}
</script>

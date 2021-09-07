<template>
    <inertia-link
        :href="to"
        class="flex flex-col items-center px-5 py-4 space-y-2 text-center transition rounded group hover:ring-1 focus:outline-none focus:ring-1"
    >
        <slot name="icon"></slot>
        <div class="font-bold group-hover:underline">
            <slot></slot>
        </div>
    </inertia-link>
</template>

<script>
export default {
    props: {
        to: {
            type: String,
            required: false,
            default: ''
        }
    }
}
</script>

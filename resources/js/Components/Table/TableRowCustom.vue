<template>
    <tr
        class="transition hover:cursor-pointer hover:bg-splashr-gray-light"
        :class="[active ? 'bg-gray-300' : 'bg-gray-100']"
    >
        <slot></slot>
    </tr>
</template>

<script>
export default {
    props: {
        active: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

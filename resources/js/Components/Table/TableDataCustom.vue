<template>
    <td
        class="px-4 py-3 ring-1 ring-white"
        :class="{
            'bg-splashr-gray border-r border-white font-bold': isChecked
        }"
    >
        <slot></slot>
    </td>
</template>

<script>
export default {
    props: {
        isChecked: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

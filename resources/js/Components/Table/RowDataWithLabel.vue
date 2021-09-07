<template>
    <div class="flex justify-between">
        <div class="pr-4">
            <slot></slot>
        </div>
        <div
            v-if="label"
            class="h-8 px-3 pt-2 pb-0.5 text-xs text-white bg-splashr-blue-light rounded uppercase"
        >
            {{ label }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        label: {
            type: String,
            required: false,
            default: ''
        }
    }
}
</script>

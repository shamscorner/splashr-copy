<template>
    <span
        v-if="inputBox.message.text"
        class="text-sm"
        :class="[inputBox.message.error ? 'text-red-500' : 'text-gray-600']"
    >
        {{ inputBox.message.text }}
    </span>
</template>

<script>
export default {
    props: {
        inputBox: {
            type: Object,
            required: true
        }
    }
};
</script>

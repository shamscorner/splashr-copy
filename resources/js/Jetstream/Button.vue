<template>
    <button
        :type="type"
        class="inline-flex items-center px-6 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-full hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
        :class="extraClasses"
    >
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'submit'
        },
        extraClasses: {
            type: String,
            required: false,
            default: ''
        }
    }
}
</script>

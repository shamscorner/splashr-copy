<template>
    <button
        :type="type"
        class="inline-flex items-center px-6 py-2 text-xs font-semibold tracking-widest text-blue-700 uppercase transition duration-150 ease-in-out bg-white border border-blue-600 rounded-full shadow-sm hover:text-blue-900 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 active:text-blue-800 active:bg-blue-50"
    >
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'button'
        }
    }
}
</script>

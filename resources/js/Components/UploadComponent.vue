<template>
    <button
        class="flex flex-col items-center p-4 text-center uppercase transition bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-2 hover:ring-2"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="w-12 h-12 p-2 my-5 bg-white rounded-full text-splashr-violet-light"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
            />
        </svg>
        <h1 class="text-xl font-semibold text-gray-700">
            {{ title }}
        </h1>
        <h3 class="mt-2 text-sm text-gray-500">formats: {{ format }}</h3>
    </button>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },

        format: {
            type: String,
            required: false,
            default: 'txt'
        }
    }
}
</script>

<template>
    <div
        class="relative w-32 h-10 font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-400 rounded-full shadow-sm text-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300 active:bg-blue-700"
    >
        <div class="absolute inset-0 flex items-center justify-center">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-5 h-5 mr-2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                />
            </svg>
            <div>
                <slot></slot>
            </div>
        </div>
        <input
            type="file"
            :accept="acceptType"
            multiple
            @change="$emit('files-uploaded', $event.target.files)"
            class="w-full h-full opacity-0 cursor-pointer"
        />
    </div>
</template>

<script>
export default {
    props: {
        acceptType: {
            type: String,
            required: false,
            default: 'file'
        }
    }
}
</script>

<template>
    <div class="flex items-center space-x-3">
        <button
            class="flex items-center h-12 px-4 space-x-3 transition border-2 rounded hover:bg-gray-50 hover:border-gray-500 focus:outline-none focus:ring-2"
            @click="$emit('reset')"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-5 h-5 text-gray-400"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                />
                <line
                    x1="3"
                    y1="0"
                    x2="22"
                    y2="22"
                    stroke="currentColor"
                    stroke-width="3"
                    class="text-red-300"
                />
            </svg>
        </button>
        <button
            class="flex items-center h-12 px-4 space-x-3 transition border-2 rounded hover:bg-gray-50 hover:border-gray-500 focus:outline-none focus:ring-2"
            @click="$emit('filter')"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-5 h-5 text-gray-400"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                />
            </svg>
            <div class="font-semibold">Filter</div>
        </button>
    </div>
</template>

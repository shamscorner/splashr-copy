<template>
    <div
        class="w-48 bg-white border rounded-md hover:ring-2 hover:cursor-pointer h-38"
        :class="{ 'ring-2 ring-gray-500': selectable && isSelected }"
        @mouseover="isActionItemsVisible = true"
        @mouseleave="isActionItemsVisible = false"
    >
        <div class="relative">
            <img
                v-if="file.src"
                class="object-cover w-full h-28 rounded-tl-md rounded-tr-md"
                :src="file.src"
            />
            <div
                v-else-if="file.isUploading"
                class="flex items-center justify-center w-full text-lg font-semibold bg-gray-300 h-28 animate-pulse"
            >
                {{ file.loadingMessage ? file.loadingMessage : 'Uploading...' }}
            </div>
            <div v-else class="flex items-center justify-center w-full h-28">
                <div class="w-20 h-20 p-4 bg-gray-200 rounded-full">
                    <img
                        :src="getPreviewImage(file.ext, file.type)"
                        class="w-full h-full"
                    />
                </div>
            </div>
            <div
                class="absolute bottom-0 left-0 px-2 py-1 text-sm font-bold text-white uppercase"
                :class="extColorType(file.ext)"
            >
                {{ file.ext ? file.ext : 'File' }}
            </div>
            <button
                v-if="isActionItemsVisible"
                class="absolute p-1 text-gray-600 transition bg-gray-200 border border-gray-500 rounded-full top-2 right-2 hover:text-white hover:bg-red-400 hover:border-red-400 focus:outline-none focus:text-white focus:bg-red-400 focus:border-red-400"
                @click="isDeleteDialogVisible = true"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="w-5 h-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                </svg>
            </button>
            <div
                v-if="
                    (selectable && isSelected) ||
                        (selectable && isActionItemsVisible)
                "
                class="absolute p-0.5 text-white transition border-2 border-white rounded-full bg-gray-500 top-2 left-2"
                :class="{ 'bg-splashr-blue-light': isSelected }"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="w-5 h-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="4"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
            </div>
            <!-- are you sure dialog -->
            <div
                v-if="isDeleteDialogVisible"
                class="absolute top-0 bottom-0 left-0 right-0 flex flex-col items-center justify-center text-center bg-gray-600 bg-opacity-90"
            >
                <p class="mb-4 text-xl text-white">Are you sure?</p>
                <div class="flex space-x-4">
                    <jet-button
                        class="bg-pink-600 hover:bg-pink-700 active:bg-pink-900 focus:ring-pink-300"
                        type="button"
                        @click.native="removeFile(file)"
                    >
                        Yes
                    </jet-button>
                    <jet-button
                        type="button"
                        @click.native="isDeleteDialogVisible = false"
                    >
                        No
                    </jet-button>
                </div>
            </div>
        </div>
        <p class="p-2 truncate">{{ file.name }}</p>
    </div>
</template>

<script>
export default {
    components: {
        JetButton: () =>
            import(/* webpackChunkName: 'JetButton' */ '@/Jetstream/Button')
    },

    props: {
        file: {
            type: Object,
            required: true
        },
        isSelected: {
            type: Boolean,
            required: false,
            default: false
        },
        selectable: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    data() {
        return {
            isActionItemsVisible: false,
            isDeleteDialogVisible: false
        }
    },

    methods: {
        removeFile(file) {
            this.isDeleteDialogVisible = false
            this.$emit('remove-file', file)
        },

        getPreviewImage(ext, type) {
            if (type.split('/')[0] == 'audio') {
                return '/images/tune-icon.png'
            }

            switch (ext.toLowerCase()) {
                case 'pdf':
                    return '/images/acrobat.png'
                case 'ttf':
                case 'otf':
                    return '/images/letter-icon.png'
                case 'ai':
                    return '/images/adobe-illustrator-icon.png'
                case 'mp4':
                    return '/images/icon-video.svg'
                default:
                    return '/images/file-icon.png'
            }
        },

        extColorType(ext) {
            switch (ext.toLowerCase()) {
                case 'png':
                case 'jpg':
                case 'jpeg':
                    return 'bg-green-500'
                case 'ai':
                case 'eps':
                    return 'bg-purple-500'
                case 'svg':
                case 'svg+xml':
                    return 'bg-pink-500'
                case 'pdf':
                    return 'bg-red-500'
                case 'ttf':
                case 'otf':
                    return 'bg-gray-600'
                case 'mp4':
                    return 'bg-yellow-600'
                default:
                    return 'bg-blue-500'
            }
        }
    }
}
</script>

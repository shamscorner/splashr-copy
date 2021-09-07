<template>
    <div class="max-w-6xl border-2 rounded-md">
        <div class="flex items-center justify-between p-3 border-b">
            <div>
                <h2 class="text-lg font-semibold">{{ titleText }}</h2>
                <p class="text-sm text-gray-500">
                    Formats: {{ formats.join(', ').toUpperCase() }}
                </p>
            </div>
            <jet-secondary-button
                class="font-bold"
                @click.native="
                    $emit('on-open-media-library-dialog', acceptTarget)
                "
            >
                <div class="flex items-center">
                    <loading-icon
                        v-if="isLoading"
                        class="w-5 h-5"
                    ></loading-icon>
                    <div class="px-3">Add</div>
                </div>
            </jet-secondary-button>
        </div>

        <no-file-yet v-if="!files.length">
            <p class="mt-0.5">
                Click the <span class="font-bold">ADD</span> button above to add
                your files
            </p>
        </no-file-yet>

        <div v-else class="flex flex-wrap p-2 overflow-y-auto max-h-56">
            <upload-file-preview
                v-for="(file, index) in files"
                :key="index"
                :file="file"
                @remove-file="removeFile"
                class="mx-2 my-2"
            ></upload-file-preview>
        </div>
    </div>
</template>

<script>
import NoFileYet from '@/Components/NoFileYet'
import UploadFilePreview from '@/Components/UploadFilePreview'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

export default {
    components: {
        NoFileYet,
        UploadFilePreview,
        JetSecondaryButton,
        LoadingIcon: () =>
            import(
                /* webpackChunkName: 'LoadingIcon' */ '@/Components/Icons/LoadingIcon'
            )
    },

    props: {
        titleText: {
            type: String,
            required: false,
            default: 'Images'
        },
        formats: {
            type: Array,
            required: false,
            default: function() {
                return ['png', 'jpg', 'jpeg', 'svg']
            }
        },
        acceptTarget: {
            type: String,
            required: false,
            default: ''
        },
        files: {
            type: Array,
            required: false,
            default: () => {
                return []
            }
        },
        isLoading: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    methods: {
        removeFile(fileToRemove) {
            this.$emit('remove-file', {
                file: fileToRemove,
                acceptTarget: this.acceptTarget
            })
        }
    }
}
</script>

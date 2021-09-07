<template>
    <video-step-layout :active="currentPageNumber" @submit="submitFormData">
        <h2 class="font-semibold">
            Import Your Assets
        </h2>

        <upload-box
            title-text="LOGOS"
            :formats="getFileAcceptFormat('logos')"
            accept-target="logos"
            :files="form.logos.files"
            :is-loading="form.logos.isLoading"
            @on-open-media-library-dialog="openMediaLibraryDialog"
            @remove-file="removeFileFromUploadBox"
        ></upload-box>

        <upload-box
            title-text="IMAGES / VIDEOS"
            :formats="getFileAcceptFormat('videos')"
            accept-target="media"
            :files="form.media.files"
            :is-loading="form.media.isLoading"
            @on-open-media-library-dialog="openMediaLibraryDialog"
            @remove-file="removeFileFromUploadBox"
        ></upload-box>

        <upload-box
            title-text="GRAPHIC CHARTERS"
            :formats="getFileAcceptFormat('graphics')"
            accept-target="graphics"
            :files="form.graphics.files"
            :is-loading="form.graphics.isLoading"
            @on-open-media-library-dialog="openMediaLibraryDialog"
            @remove-file="removeFileFromUploadBox"
        ></upload-box>

        <upload-box
            title-text="FONTS"
            :formats="getFileAcceptFormat('fonts')"
            accept-target="fonts"
            :files="form.fonts.files"
            :is-loading="form.fonts.isLoading"
            @on-open-media-library-dialog="openMediaLibraryDialog"
            @remove-file="removeFileFromUploadBox"
        ></upload-box>

        <upload-box
            title-text="SOUNDS"
            :formats="getFileAcceptFormat('sounds')"
            accept-target="sounds"
            :files="form.sounds.files"
            :is-loading="form.sounds.isLoading"
            @on-open-media-library-dialog="openMediaLibraryDialog"
            @remove-file="removeFileFromUploadBox"
        ></upload-box>

        <portal to="modal">
            <div v-if="isMediaLibrary">
                <jet-dialog-modal :show="isShowPickerDialog" max-width="7xl">
                    <template #content>
                        <google-picker-body
                            :parent-folder-id="targetAssetsFolder.id"
                            :accept-target="acceptTarget"
                            @picker-files="onAddFilesFromMediaLibrary"
                            @picker-loaded="pickerFilesDoneLoading"
                            @close-picker="closeMediaLibraryDialog"
                        ></google-picker-body>
                    </template>
                </jet-dialog-modal>
            </div>
        </portal>
    </video-step-layout>
</template>

<script>
import VideoStepLayout from '@/Layouts/VideoStepLayout'
import UploadBox from '@/Components/UploadBox'
import { getFileAcceptFormat } from '@/Utilities/AssetLibraryFileTypes'
import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'
import VideoStepInOrderProcessMixin from '@/Mixins/VideoStepInOrderProcess'
import GooglePickerMixin from '@/Mixins/GooglePicker'
import GoogleDriveService from '@/Services/GoogleDriveService'

export default {
    mixins: [VideoStepInOrderProcessMixin, GooglePickerMixin],

    components: {
        VideoStepLayout,
        UploadBox,
        JetDialogModal: () =>
            import(
                /* webpackChunkName: 'JetDialogModal' */ '@/Jetstream/DialogModal'
            ),
        GooglePickerBody: () =>
            import(
                /* webpackChunkName: 'GooglePickerBody' */ '@/Components/MediaLibrary/GooglePickerBody'
            )
    },

    data() {
        return {
            currentPageNumber: 2,
            isMediaLibrary: false,
            isShowPickerDialog: false,
            acceptTarget: '',
            targetAssetsFolder: {
                id: '',
                name: ''
            },
            form: {
                logos: {
                    files: [],
                    isLoading: false
                },
                graphics: {
                    files: [],
                    isLoading: false
                },
                fonts: {
                    files: [],
                    isLoading: false
                },
                sounds: {
                    files: [],
                    isLoading: false
                },
                media: {
                    files: [],
                    isLoading: false
                }
            }
        }
    },

    mounted() {
        // fetch all the assets for this video
        const acceptTargets = [
            {
                id: this.$page.data.assets.logosFolderId,
                target: 'logos'
            },
            {
                id: this.$page.data.assets.graphicChaptersFolderId,
                target: 'graphics'
            },
            {
                id: this.$page.data.assets.fontsFolderId,
                target: 'fonts'
            },
            {
                id: this.$page.data.assets.soundsFolderId,
                target: 'sounds'
            },
            {
                id: this.$page.data.assets.assetsFolderId,
                target: 'media'
            }
        ]
        acceptTargets.forEach(target => {
            if (target.id) {
                this.fetchFilesForThisVideo(target.id, target.target)
            }
        })
    },

    methods: {
        closeMediaLibraryDialog() {
            this.isMediaLibrary = false
            this.isShowPickerDialog = false
        },

        async fetchFilesForThisVideo(folderId, acceptTarget) {
            const response = await GoogleDriveService.fetchFilesFromAFolder(
                folderId,
                '',
                true
            )
            if (response.data.files) {
                const files = response.data.files.map(file => {
                    return {
                        ext: '',
                        id: file.shortcutDetails.targetId,
                        isSelected: false,
                        name: file.name,
                        src: '',
                        type: file.shortcutDetails.targetMimeType,
                        isUploading: true,
                        loadingMessage: 'Loading...',
                        shortcutId: file.id
                    }
                })

                this.onAddFilesFromMediaLibrary(
                    {
                        files: files,
                        acceptTarget: acceptTarget
                    },
                    false
                )
            }
        },

        openMediaLibraryDialog(acceptTarget) {
            // start progress bar
            this.form[acceptTarget].isLoading = true

            // set the target folder id and name
            const targets = {
                logos: 'Logos',
                graphics: 'Graphic Charters',
                fonts: 'Fonts',
                sounds: 'Sounds',
                media: ''
            }
            this.targetAssetsFolder = this.findTargetAssetsFolder(
                targets[acceptTarget]
            )

            // show the picker
            this.acceptTarget = acceptTarget
            this.isMediaLibrary = true
        },

        findTargetAssetsFolder(name) {
            if (!name) {
                return {
                    id: this.$page.company.g_media_folder_id,
                    name: ''
                }
            }
            return this.$page.data.company_assets.find(
                folder => folder.name === name
            )
        },

        getFileAcceptFormat(type) {
            return getFileAcceptFormat(type)
        },

        async submit(payload) {
            // submit video data
            await this.$apollo.mutate({
                // Mutation
                mutation: UpdateVideoMutation,
                // Parameters
                variables: {
                    id: this.$page.video.id,
                    video: {
                        name: payload.videoName,
                        session_type: this.$page.video.session_type
                    }
                }
            })
            return true
        }
    }
}
</script>

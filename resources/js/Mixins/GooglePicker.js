import GoogleDriveService from '@/Services/GoogleDriveService'
import MakeDriveFileShortcutMutation from '@/Graphql/Google/Mutations/MakeShortcut.gql'

export default {
    methods: {
        async getFileThumbnail(file, acceptTarget) {
            let data = null
            if (this.isFolder(file.type)) {
                data = {
                    file: {
                        ext: 'Folder',
                        src: '/images/icon-folder.jpg',
                        type: file.type
                    }
                }
            } else {
                const response = await GoogleDriveService.getAFileById(file.id)
                data = response.data
            }
            // update to the files
            this.decideFileType(data, file.id, acceptTarget)
        },

        decideFileType(data, fileId, acceptTarget) {
            if (acceptTarget != 'videos') {
                this.updateFileDataFromResponse(
                    this.form[acceptTarget].files,
                    fileId,
                    data
                )
            }
        },

        updateFileDataFromResponse(files, fileId, data) {
            files.find(file => {
                if (file.id === fileId) {
                    if (data.file) {
                        file.ext = data.file.ext
                        file.src = data.file.src
                        file.type = data.file.type
                    }
                    file.isUploading = false
                }
            })
        },

        removeFileFromUploadBox({ file, acceptTarget }) {
            this.form[acceptTarget].files = this.filterFiles(
                this.form[acceptTarget].files,
                file
            )
            // remove from the google drive as well
            if (file.shortcutId) {
                GoogleDriveService.deleteFile(file.shortcutId)
            }
        },

        filterFiles(files, fileToRemove) {
            return files.filter(file => file.id !== fileToRemove.id)
        },

        onAddFilesFromMediaLibrary({ acceptTarget, files }, isNew = true) {
            // process files
            if (isNew) {
                files = this.processFiles(files)
            }

            this.form[acceptTarget].files = this.removeDuplicateAndAppendFiles(
                files,
                this.form[acceptTarget].files,
                isNew
            )

            // get thumbnails
            files.forEach(file => {
                this.getFileThumbnail(file, acceptTarget)
            })
            // make shortcuts in the project directory
            if (isNew) {
                this.makeShortcutsInDrive(files, acceptTarget)
            }
        },

        processFiles(files) {
            return files.map(file => {
                let ext = 'Folder'
                if (!this.isFolder(file.mimeType)) {
                    const fileNameArr = file.name.split('.')
                    ext = fileNameArr[fileNameArr.length - 1]
                }

                return {
                    ext: ext,
                    id: file.id,
                    isSelected: false,
                    name: file.name,
                    src: '',
                    type: file.mimeType,
                    isUploading: true,
                    loadingMessage: 'Loading...',
                    shortcutId: null
                }
            })
        },

        isFolder(mimeType) {
            return mimeType == 'application/vnd.google-apps.folder'
        },

        removeDuplicateAndAppendFiles(files, formFiles, isNew) {
            if (isNew) {
                files = files.filter(file => {
                    if (formFiles.some(formFile => formFile.id === file.id)) {
                        return false
                    } else {
                        return true
                    }
                })
            }
            return files.concat(formFiles)
        },

        pickerFilesDoneLoading(acceptTarget) {
            // show the picker dialog
            this.isShowPickerDialog = true

            // stop loading progressbar
            this.form[acceptTarget].isLoading = false
        },

        async makeShortcutsInDrive(files, acceptTarget) {
            let assets = {}
            assets[acceptTarget] = this.filterFileData(files, acceptTarget)
            // submit
            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: MakeDriveFileShortcutMutation,
                    // Parameters
                    variables: {
                        assets: {
                            video: {
                                id: this.$page.video.id
                            },
                            assets: assets
                        }
                    }
                })

                if (response.data.syncAssetsToProject) {
                    const shortcuts = JSON.parse(
                        response.data.syncAssetsToProject
                    )
                    this.updateShortcutData(shortcuts, acceptTarget)
                }
            } catch (error) {
                console.log(error)
                alert('Something went wrong!')
            }
        },

        filterFileData(files, acceptTarget) {
            return files.map(file => {
                return {
                    id: file.id,
                    isParentFolder: acceptTarget === 'media' ? true : false
                }
            })
        },

        updateShortcutData(shortcuts, acceptTarget) {
            shortcuts.forEach(shortcut => {
                this.form[acceptTarget].files.find(file => {
                    if (file.id === shortcut.id) {
                        file.shortcutId = shortcut.shortcut_id
                    }
                })
            })
        }
    }
}

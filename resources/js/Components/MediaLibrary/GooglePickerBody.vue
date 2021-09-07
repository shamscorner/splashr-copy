<template>
    <div>
        <!-- toast alert -->
        <toast v-if="isToast.show" @close="isToast.show = false">
            {{ isToast.message }}
        </toast>

        <div class="relative pb-7 h-700">
            <button
                v-if="dialogMode"
                class="absolute bottom-0 right-0 px-5 py-2 text-xs font-semibold bg-gray-100 border border-gray-300 hover:border-gray-400 focus:outline-none focus:border-gray-500"
                style="z-index: 9999;"
                @click="closePicker"
            >
                Close
            </button>
            <!-- tab bar -->
            <div class="flex border-b border-gray-300">
                <button
                    class="px-3 py-2 text-gray-400 transition border-b-4 border-transparent hover:bg-purple-50 focus:outline-none focus:bg-purple-50 focus:text-purple-600"
                    :class="{
                        'border-purple-500 text-purple-500 font-semibold':
                            currentPickerTab == tabNames[0]
                    }"
                    @click="showPicker(1)"
                >
                    {{ tabNames[0] }}
                </button>
                <button
                    class="px-3 py-2 text-gray-400 transition border-b-4 border-transparent hover:bg-purple-50 focus:outline-none focus:bg-purple-50 focus:text-purple-600"
                    :class="{
                        'border-purple-500 text-purple-500 font-semibold':
                            currentPickerTab == tabNames[1]
                    }"
                    @click="showPicker(2)"
                >
                    {{ tabNames[1] }}
                </button>
                <button
                    v-if="!isClientAssetsTab && isUserDriveTab"
                    class="px-3 py-2 text-gray-400 transition border-b-4 border-transparent hover:bg-purple-50 focus:outline-none focus:bg-purple-50 focus:text-purple-600"
                    :class="{
                        'border-purple-500 text-purple-500 font-semibold':
                            currentPickerTab == tabNames[2]
                    }"
                    @click="openUserGDrivePicker"
                >
                    {{ tabNames[2] }}
                </button>
                <button
                    v-else-if="isClientAssetsTab"
                    class="px-3 py-2 text-gray-400 transition border-b-4 border-transparent hover:bg-purple-50 focus:outline-none focus:bg-purple-50 focus:text-purple-600"
                    :class="{
                        'border-purple-500 text-purple-500 font-semibold':
                            currentPickerTab == tabNames[2]
                    }"
                    @click="showPicker(4)"
                >
                    {{ tabNames[2] }}
                </button>
            </div>

            <!-- tab content -->
            <div class="relative w-full h-full" ref="tabContent"></div>
        </div>
    </div>
</template>

<script>
/* eslint-disable no-undef */
import GoogleDriveService from '@/Services/GoogleDriveService'

export default {
    props: {
        parentFolderId: {
            type: String,
            required: true
        },

        acceptTarget: {
            type: String,
            required: false,
            default: ''
        },

        acceptMimetype: {
            type: String,
            required: false,
            default: ''
        },

        dialogMode: {
            type: Boolean,
            required: false,
            default: true
        },

        tabNames: {
            type: Array,
            required: false,
            default: () => {
                return ['Media Library', 'Upload', 'Google Drive']
            }
        },

        isUserDriveTab: {
            type: Boolean,
            required: false,
            default: true
        },

        isClientAssetsTab: {
            type: Boolean,
            required: false,
            default: false
        },

        clientAssetsParentFolderId: {
            type: String,
            required: false,
            default: ''
        },
        isPickerLoading: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    data() {
        return {
            token: null,
            tempAttachments: [],
            picker: {
                mediaLibrary: null,
                uploadFile: null,
                gDrive: null,
                clientAssetsPicker: null
            },
            pickerDialogContainer: null,
            currentPickerTab: this.tabNames[0],
            userGDrive: {
                token: null,
                email: null
            },
            isToast: {
                show: false,
                message: ''
            }
        }
    },

    async mounted() {
        // set google drive javascript api
        let gDrive = document.createElement('script')
        gDrive.setAttribute('type', 'text/javascript')
        gDrive.setAttribute('src', 'https://apis.google.com/js/api.js')
        document.head.appendChild(gDrive)

        // get google drive api token
        this.getGDriveTokenAndSetup()
    },

    methods: {
        async getGDriveTokenAndSetup() {
            this.$emit('update:is-picker-loading', true)

            try {
                // get token from server
                const response = await GoogleDriveService.fetchToken()
                this.token = response.data

                this.openDrivePicker()
            } catch (error) {
                console.error(error)
            }
        },

        openDrivePicker() {
            const isGapiLoadedInterval = setInterval(() => {
                if (typeof gapi !== 'undefined' && gapi !== null) {
                    gapi.load('picker', () => {
                        // create media library or project library picker
                        this.picker.mediaLibrary = new google.picker.PickerBuilder()
                            .enableFeature(
                                google.picker.Feature.MULTISELECT_ENABLED
                            )
                            .enableFeature(google.picker.Feature.NAV_HIDDEN)
                            .setOAuthToken(this.token.token.access_token)
                            .addView(
                                new google.picker.DocsView()
                                    .setParent(this.parentFolderId)
                                    .setIncludeFolders(true)
                                    .setLabel('Media Library')
                                    .setMode(google.picker.DocsViewMode.GRID)
                            )
                            // .setDeveloperKey(
                            //     process.env.MIX_GOOGLE_DEVELOPER_KEY
                            // )
                            .setCallback(this.pickerCallbackForMediaLibrary)
                            .build()
                        this.picker.mediaLibrary.setVisible(true)

                        // create upload file picker
                        this.picker.uploadFile = new google.picker.PickerBuilder()
                            .enableFeature(
                                google.picker.Feature.MULTISELECT_ENABLED
                            )
                            .enableFeature(google.picker.Feature.NAV_HIDDEN)
                            .setOAuthToken(this.token.token.access_token)
                            .addView(
                                new google.picker.DocsUploadView()
                                    .setIncludeFolders(true)
                                    .setParent(this.parentFolderId)
                            )
                            // .setDeveloperKey(
                            //     process.env.MIX_GOOGLE_DEVELOPER_KEY
                            // )
                            // .setSelectableMimeTypes('image/png')
                            .setCallback(this.pickerCallbackForMediaLibrary)
                            .build()
                        this.picker.uploadFile.setVisible(true)

                        if (this.isClientAssetsTab) {
                            // create client assets picker
                            this.picker.clientAssetsPicker = new google.picker.PickerBuilder()
                                .enableFeature(
                                    google.picker.Feature.MULTISELECT_ENABLED
                                )
                                .enableFeature(google.picker.Feature.NAV_HIDDEN)
                                .setOAuthToken(this.token.token.access_token)
                                .addView(
                                    new google.picker.DocsView()
                                        .setParent(
                                            this.clientAssetsParentFolderId
                                        )
                                        .setIncludeFolders(true)
                                        .setLabel('Media Library')
                                        .setMode(
                                            google.picker.DocsViewMode.GRID
                                        )
                                )
                                // .setDeveloperKey(
                                //     process.env.MIX_GOOGLE_DEVELOPER_KEY
                                // )
                                .setCallback(this.pickerCallbackForMediaLibrary)
                                .build()
                            this.picker.clientAssetsPicker.setVisible(true)
                        }

                        // add picker builder to tab content container
                        const nodeList = document.querySelectorAll(
                            '.picker-dialog'
                        )
                        nodeList.forEach(node => {
                            this.$refs.tabContent.append(node)
                        })

                        // show the first tab and hide others
                        this.picker.uploadFile.setVisible(false)
                        if (this.isClientAssetsTab) {
                            this.picker.clientAssetsPicker.setVisible(false)
                        }

                        // open dialog
                        this.$emit('picker-loaded', this.acceptTarget)

                        // load client auth2
                        if (!this.isClientAssetsTab && this.isUserDriveTab) {
                            gapi.load('client:auth2', () => {
                                gapi.auth2.init({
                                    client_id: process.env.MIX_GOOGLE_CLIENT_ID
                                })
                            })
                        }

                        this.$emit('update:is-picker-loading', false)
                    })

                    clearInterval(isGapiLoadedInterval)
                }
            }, 1000)
        },

        async openUserGDrivePicker() {
            // if it is already loaded, do not load again
            if (this.picker.gDrive) {
                this.showPicker(3)
                return
            }

            // check whether the token available in the cookie or not
            // if not then fetch token
            const gUserToken = this.getCookie('G_USER_TOKEN_SPLASHR')
            const gUserEmail = this.getCookie('G_USER_EMAIL_SPLASHR')

            if (gUserToken && gUserEmail) {
                this.userGDrive.token = gUserToken
                this.userGDrive.email = gUserEmail
            } else {
                const googleAuth = gapi.auth2.getAuthInstance()

                await googleAuth.signIn({
                    scope:
                        'https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.file'
                })

                const user = googleAuth.currentUser.get()
                const authResponse = user.getAuthResponse(true)
                this.userGDrive.token = authResponse.access_token

                this.userGDrive.email = user.getBasicProfile().getEmail()

                this.writeCookie(
                    'G_USER_TOKEN_SPLASHR',
                    this.userGDrive.token,
                    authResponse.expires_at
                )
                this.writeCookie(
                    'G_USER_EMAIL_SPLASHR',
                    this.userGDrive.email,
                    authResponse.expires_at
                )
            }

            // create permission for this email in the app destination folder
            this.createPermission()

            // load the client
            this.loadClient()
        },

        writeCookie(key, value, expiresAt) {
            let date = new Date()
            date.setTime(expiresAt)

            window.document.cookie =
                key +
                '=' +
                value +
                '; expires=' +
                date.toUTCString() +
                '; path=/'

            return value
        },

        getCookie(cname) {
            const name = cname + '='
            const decodedCookie = decodeURIComponent(document.cookie)
            const ca = decodedCookie.split(';')
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i]
                while (c.charAt(0) == ' ') {
                    c = c.substring(1)
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length)
                }
            }
            return ''
        },

        async loadClient() {
            // set api key
            gapi.client.setApiKey(process.env.MIX_GOOGLE_API_KEY)

            try {
                await gapi.client.load(
                    'https://content.googleapis.com/discovery/v1/apis/drive/v3/rest'
                )
                console.log('GAPI client loaded for API')

                // create user google drive picker
                this.createUserGDrivePicker()
            } catch (error) {
                console.error('Error loading GAPI client for API', error)
            }
        },

        createUserGDrivePicker() {
            const docsView = new google.picker.DocsView()
                .setIncludeFolders(true)
                .setOwnedByMe(true)
                .setLabel('Google Drive')

            this.picker.gDrive = new google.picker.PickerBuilder()
                .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                .enableFeature(google.picker.Feature.NAV_HIDDEN)
                .setOAuthToken(this.userGDrive.token)
                .addView(docsView)
                // .setDeveloperKey(process.env.MIX_GOOGLE_DEVELOPER_KEY)
                .setCallback(this.pickerCallbackForUserGoogleDrive)
                .build()
            this.picker.gDrive.setVisible(true)

            // add picker builder to tab content container
            this.picker.mediaLibrary.setVisible(true)
            this.picker.uploadFile.setVisible(true)
            const nodeList = document.querySelectorAll('.picker-dialog')
            nodeList.forEach(node => {
                this.$refs.tabContent.append(node)
            })

            this.picker.mediaLibrary.setVisible(false)
            this.picker.uploadFile.setVisible(false)

            this.currentPickerTab = this.tabNames[2]
        },

        showPicker(type) {
            switch (type) {
                case 1:
                    // media library
                    this.currentPickerTab = this.tabNames[0]
                    this.setPickerVisibility(true, false, false)
                    break
                case 2:
                    // upload file
                    this.currentPickerTab = this.tabNames[1]
                    this.setPickerVisibility(false, true, false)
                    break
                case 3:
                    // google drive
                    this.currentPickerTab = this.tabNames[2]
                    this.setPickerVisibility(false, false, true)
                    break
                case 4:
                    // client assets library
                    this.currentPickerTab = this.tabNames[2]
                    this.setPickerVisibility(false, false, true)
                    break
            }
        },

        setPickerVisibility(
            isMediaLibrary,
            isUploadFile,
            isClientAssetsOrUserGDrive
        ) {
            if (this.picker.mediaLibrary) {
                this.picker.mediaLibrary.setVisible(isMediaLibrary)
            }
            if (this.picker.uploadFile) {
                this.picker.uploadFile.setVisible(isUploadFile)
            }
            if (this.isClientAssetsTab) {
                this.picker.clientAssetsPicker.setVisible(
                    isClientAssetsOrUserGDrive
                )
            } else {
                if (this.picker.gDrive) {
                    this.picker.gDrive.setVisible(isClientAssetsOrUserGDrive)
                }
            }
        },

        async pickerCallbackForMediaLibrary(data) {
            switch (data[google.picker.Response.ACTION]) {
                case google.picker.Action.PICKED:
                    console.log('picker callback: ', data.docs)
                    this.$emit('picker-files', {
                        acceptTarget: this.acceptTarget,
                        files: data.docs
                    })
                    this.closePicker()
                    break

                case google.picker.Action.CANCEL:
                    this.closePicker()
                    break
            }
        },

        async pickerCallbackForUserGoogleDrive(data) {
            console.log('picker files in user google drive callback: ', data)

            switch (data[google.picker.Response.ACTION]) {
                case google.picker.Action.PICKED:
                    // show toast
                    this.isToast = {
                        show: true,
                        message: 'Copying files...'
                    }
                    // show the picker again
                    this.showPicker(3)
                    // copy files from user google drive to app google drive
                    data.docs.forEach(doc => {
                        console.log('doc: ', doc)
                        this.copyFileFromUserGDrive(doc)
                    })
                    break

                case google.picker.Action.CANCEL:
                    this.closePicker()
                    break
            }
        },

        async copyFileFromUserGDrive(file) {
            try {
                // Create permission for the file in user's drive
                const permissionResponse = await gapi.client.drive.permissions.create(
                    {
                        fileId: file.id,
                        resource: {
                            role: 'writer',
                            type: 'user',
                            emailAddress:
                                process.env.MIX_GOOGLE_SERVICE_ACCOUNT_EMAIL
                        },
                        supportsAllDrives: true,
                        sendNotificationEmail: false
                    }
                )
                console.log('permission file response: ', permissionResponse)

                // Create a clone of the file
                const cloned = (
                    await gapi.client.drive.files.copy({
                        fileId: file.id,
                        supportsAllDrives: true
                    })
                ).result
                console.log('cloned file response: ', cloned)

                // Move the cloned file to new folder
                const response = await gapi.client.drive.files.update({
                    fileId: cloned.id,
                    addParents: this.clientAssetsParentFolderId,
                    removeParents: file.parentId,
                    supportsAllDrives: true,
                    resource: {
                        name: file.name
                    }
                })
                console.log('update file response: ', response.result)

                // update file data by the cloned data
                file.id = cloned.id
                file.parentId = this.clientAssetsParentFolderId

                this.$emit('picker-files', {
                    acceptTarget: this.acceptTarget,
                    files: [file]
                })
            } catch (error) {
                console.log(error)
            }
        },

        closePicker() {
            this.$emit('close-picker')
        },

        async createPermission() {
            try {
                const response = await GoogleDriveService.createPermission(
                    this.parentFolderId,
                    this.userGDrive.email
                )
                console.log('Permission created for user', response.data)
            } catch (error) {
                console.error(error)
            }
        }
    }
}
</script>

<style>
.picker-dialog {
    border: none !important;
    box-shadow: none !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
}
.picker-dialog-bg {
    display: none;
}
.picker-dialog-content {
    width: 100% !important;
    height: 100% !important;
}
</style>

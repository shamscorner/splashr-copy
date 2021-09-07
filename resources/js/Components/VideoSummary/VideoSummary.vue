<template>
    <div class="flex flex-col space-y-5">
        <!-- general step -->
        <summary-box :edit-url="editUrl.general">
            <div v-if="video.session_type && sessionBadge">
                <session-badge :session-type="video.session_type">
                </session-badge>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Product
                </p>
                <p class="w-full lg:w-2/3">
                    {{ video.service_offer }}
                </p>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Your Audience
                </p>
                <p class="w-full lg:w-2/3">
                    {{ video.audience }}
                </p>
            </div>
        </summary-box>

        <!-- assets step -->
        <summary-box :edit-url="editUrl.assets">
            <div>
                <p class="mb-2 font-semibold text-splashr-violet-deep">
                    Your Logos
                </p>
                <div v-if="assets.isLoading.logos">
                    Loading...
                </div>
                <div
                    v-else-if="
                        !assets.isLoading.logos &&
                            assets.assets.logos.length == 0
                    "
                >
                    No logos found
                </div>
                <div
                    v-else
                    class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-6 xl:grid-cols-8"
                >
                    <thumbnail
                        v-for="logo in assets.assets.logos"
                        :key="logo.id"
                        :src="logo.src ? logo.src : ''"
                        :caption="logo.name"
                    >
                    </thumbnail>
                </div>
            </div>
            <div>
                <p class="mt-8 mb-1 font-semibold text-splashr-violet-deep">
                    Your Fonts
                </p>
                <ul>
                    <li v-for="font in assets.assets.fonts" :key="font.id">
                        {{ font.name }}
                    </li>
                    <li v-if="assets.isLoading.fonts">
                        Loading...
                    </li>
                    <li
                        v-else-if="
                            !assets.isLoading.fonts &&
                                assets.assets.fonts.length == 0
                        "
                    >
                        No fonts found
                    </li>
                </ul>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Your Graphic Charters
                </p>
                <ul>
                    <li
                        v-for="graphicCharter in assets.assets.graphics"
                        :key="graphicCharter.id"
                    >
                        {{ graphicCharter.name }}
                    </li>
                    <li v-if="assets.isLoading.graphics">
                        Loading...
                    </li>
                    <li
                        v-else-if="
                            !assets.isLoading.graphics &&
                                assets.assets.graphics.length == 0
                        "
                    >
                        No graphic charters found
                    </li>
                </ul>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Your SoundTracks
                </p>
                <ul>
                    <li v-for="sound in assets.assets.sounds" :key="sound.id">
                        {{ sound.name }}
                    </li>
                    <li v-if="assets.isLoading.sounds">
                        Loading...
                    </li>
                    <li
                        v-else-if="
                            !assets.isLoading.sounds &&
                                assets.assets.sounds.length == 0
                        "
                    >
                        No soundtracks found
                    </li>
                </ul>
            </div>
        </summary-box>

        <!-- details step -->
        <summary-box :edit-url="editUrl.details">
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Your Purpose(s) of this video
                </p>
                <ul v-if="video.purposes.length">
                    <li v-for="purpose in video.purposes" :key="purpose.id">
                        {{ purpose.name }}
                    </li>
                </ul>
                <div class="text-gray-400" v-else>
                    Not selected
                </div>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Video Publishing Platform(s)
                </p>
                <ul v-if="video.platforms.length">
                    <li v-for="platform in video.platforms" :key="platform.id">
                        {{ platform.name }}
                    </li>
                </ul>
                <div class="text-gray-400" v-else>
                    Not selected
                </div>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Key Message of This Video
                </p>
                <p v-if="video.key_message" class="w-full lg:w-2/3">
                    {{ video.key_message }}
                </p>
                <div v-else class="text-gray-400">
                    No key message
                </div>
            </div>
            <div v-if="video.languages">
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Video Publishing Platform(s)
                </p>
                <ul>
                    <li
                        v-for="(language, index) in JSON.parse(video.languages)"
                        :key="index"
                    >
                        {{ language }}
                    </li>
                </ul>
            </div>
            <div v-if="video.session_type === videoSessionType.acting">
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Personal preferences regarding the actor/actrice
                </p>
                <p class="w-full lg:w-2/3">
                    {{ video.actor_pref }}
                </p>
            </div>
            <div v-if="video.session_type === videoSessionType.motion">
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Voice-over in this video?
                </p>
                <p>
                    {{
                        video.is_voice_over
                            ? 'Yes (Additional cost of 250 €)'
                            : 'No'
                    }}
                </p>
            </div>
            <div>
                <p class="mb-1 font-semibold text-splashr-violet-deep">
                    Your Specific Comment or Requirements
                </p>
                <p v-if="video.other_requirements" class="w-full lg:w-2/3">
                    {{ video.other_requirements }}
                </p>
                <div v-else class="text-gray-400">
                    Not selected
                </div>
            </div>
        </summary-box>
    </div>
</template>

<script>
import SummaryBox from '@/Components/SummaryBox'
import Thumbnail from '@/Components/Thumbnail'
import SessionBadge from '@/Components/VideoSummary/SessionBadge'
import GoogleDriveService from '@/Services/GoogleDriveService'
import { getAcceptTargetByFileType } from '@/Utilities/AssetLibraryFileTypes'
import { videoSessionType } from '@/Utilities/VideoStatus'

export default {
    components: {
        SummaryBox,
        Thumbnail,
        SessionBadge
    },

    props: {
        editUrl: {
            type: Object,
            required: false,
            default: () => {
                return {
                    general: '',
                    assets: '',
                    details: ''
                }
            }
        },
        video: {
            type: Object,
            required: true
        },
        folders: {
            type: Object,
            required: true
        },
        sessionBadge: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    data() {
        return {
            videoSessionType,
            assets: {
                assets: {
                    fonts: [],
                    graphics: [],
                    sounds: [],
                    logos: []
                },
                isLoading: {
                    fonts: false,
                    graphics: false,
                    sounds: false,
                    logo: false
                }
            }
        }
    },

    mounted() {
        // fetch logos
        this.fetchFilesForThisVideo(this.folders.logosFolderId, 'logos')

        // fetch fonts
        this.fetchFilesForThisVideo(this.folders.fontsFolderId, 'fonts')

        // fetch graphic charters
        this.fetchFilesForThisVideo(
            this.folders.graphicChaptersFolderId,
            'graphics'
        )

        // fetch sounds
        this.fetchFilesForThisVideo(this.folders.soundsFolderId, 'sounds')
    },

    methods: {
        async fetchFilesForThisVideo(folderId, acceptTarget) {
            if (!folderId) {
                return
            }

            this.loadProgressbar(acceptTarget, true)

            try {
                const response = await GoogleDriveService.fetchFilesFromAFolder(
                    folderId,
                    '',
                    true
                )
                if (response.data.files) {
                    this.addAssetFiles(response.data.files, acceptTarget)
                }
            } catch (error) {
                console.log(error)
            } finally {
                // stop the progressbar
                this.loadProgressbar(acceptTarget, false)
            }
        },

        addAssetFiles(files, acceptTarget) {
            // show files
            switch (acceptTarget) {
                case 'logos':
                    this.assets.assets.logos = files.concat(
                        this.assets.assets.logos
                    )
                    // fetch thumbnails
                    files.forEach(file => {
                        this.fetchThumbnailForLogos(
                            file.id,
                            file.shortcutDetails.targetId
                        )
                    })
                    break
                case 'graphics':
                    this.assets.assets.graphics = files.concat(
                        this.assets.assets.graphics
                    )
                    break
                case 'fonts':
                    this.assets.assets.fonts = files.concat(
                        this.assets.assets.fonts
                    )
                    break
                case 'sounds':
                    this.assets.assets.sounds = files.concat(
                        this.assets.assets.sounds
                    )
                    break
                case 'videos':
                    break
                default:
                    files.forEach(file => {
                        this.addAssetFiles(
                            [file],
                            getAcceptTargetByFileType(
                                file.shortcutDetails.targetMimeType
                            )
                        )
                    })
            }
        },

        async fetchThumbnailForLogos(logoId, targetId) {
            const response = await GoogleDriveService.getAFileById(targetId)
            // console.log(response)
            if (response.data.file) {
                this.assets.assets.logos.find(logo => {
                    if (logo.id === logoId) {
                        logo.src = response.data.file.src
                    }
                })
            }
        },

        loadProgressbar(acceptTarget, isLoading) {
            this.assets.isLoading[acceptTarget] = isLoading
        }
    }
}
</script>

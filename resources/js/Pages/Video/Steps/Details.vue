<template>
    <video-step-layout :active="currentPageNumber" @submit="submitFormData">
        <!-- main purpose -->
        <div>
            <jet-label
                class="font-semibold"
                value="What is the main purpose of this video?"
            />
            <div
                v-for="purpose of $page.data.purposes"
                :key="purpose.id"
                class="flex items-center mt-3"
            >
                <input
                    type="checkbox"
                    class="w-5 h-5 rounded"
                    :value="purpose.id"
                    v-model="form.purposeTarget.value"
                />
                <p class="ml-3 text-sm text-gray-500">
                    {{ purpose.name }}
                </p>
            </div>
        </div>

        <!-- publish target -->
        <div>
            <jet-label
                class="font-semibold"
                value="Where would you like to publish your video?"
            />
            <div
                v-for="platform of $page.data.platforms"
                :key="platform.id"
                class="flex items-center mt-3"
            >
                <input
                    type="checkbox"
                    class="w-5 h-5 rounded"
                    :value="platform.id"
                    v-model="form.platformTarget.value"
                />
                <p class="ml-3 text-sm text-gray-500">
                    {{ platform.name }}
                </p>
            </div>
            <div class="flex items-center mt-3">
                <input
                    v-model="isOtherPlatformOptionSelected"
                    type="checkbox"
                    class="w-5 h-5 rounded"
                />
                <p class="ml-3 text-sm text-gray-500">
                    Other
                </p>
            </div>
            <div v-if="isOtherPlatformOptionSelected" class="mt-4">
                <jet-input
                    v-model="form.platformTarget.other"
                    type="text"
                    class="block w-full lg:w-3/5 mt-1.5"
                    placeholder="Please mention here"
                />
                <p class="mt-1 text-xs">
                    Use comma (,) to separate multiple platforms. For Example,
                    (Linkedin, Whatsapp)
                </p>
            </div>
        </div>

        <!-- key message input -->
        <div>
            <jet-label
                class="mb-1 font-semibold"
                for="key-message"
                value="What is the key message of the video/this campaign?"
            />
            <text-area-custom
                id="key-message"
                class="block w-full lg:w-4/5"
                v-model="form.keyMessage.value"
                placeholder="Type your key message here"
            ></text-area-custom>
        </div>

        <!-- languages -->
        <div>
            <jet-label
                class="font-semibold"
                value="What is the language of the videos?"
            />
            <div
                v-for="language of $page.data.languages"
                :key="language.id"
                class="flex items-center mt-3"
            >
                <input
                    type="checkbox"
                    class="w-5 h-5 rounded"
                    :value="language.name"
                    v-model="form.languageTarget.value"
                />
                <p class="ml-3 text-sm text-gray-500">
                    {{ language.name }}
                </p>
            </div>
            <div class="flex items-center mt-3">
                <input
                    v-model="isOtherLanguageOptionSelected"
                    type="checkbox"
                    class="w-5 h-5 rounded"
                />
                <p class="ml-3 text-sm text-gray-500">
                    Other
                </p>
            </div>
            <div v-if="isOtherLanguageOptionSelected" class="mt-4">
                <jet-input
                    v-model="form.languageTarget.other"
                    type="text"
                    class="block w-full lg:w-3/5 mt-1.5"
                    placeholder="Please mention here"
                />
                <p class="mt-1 text-xs">
                    Use comma (,) to separate multiple languages. For Example,
                    (Russian, Turkish)
                </p>
            </div>
        </div>

        <!-- actor/actrice preferences -->
        <div v-if="$page.video.session_type === videoSessionType.acting">
            <jet-label
                class="mb-1 font-semibold"
                for="actor-preferences"
                value="Do you have any preferences regarding the actor/actrice?"
            />
            <text-area-custom
                id="actor-preferences"
                class="block w-full lg:w-4/5"
                v-model="form.actorPrefs.value"
                placeholder="Type your preferences here"
            ></text-area-custom>
        </div>

        <!-- voice over radio input -->
        <div
            v-if="$page.video.session_type === videoSessionType.motion"
            class="flex flex-col space-y-4"
        >
            <jet-label
                class="font-semibold"
                value="Will you want us to add a voice-over to this video?"
            />
            <div class="flex items-center space-x-4">
                <input
                    type="radio"
                    name="is-voice-over"
                    v-model="form.isVoiceOver"
                    :value="true"
                    class="w-5 h-5 rounded-full"
                />
                <p class="text-sm">Yes (Additional cost of 250 €)</p>
            </div>
            <div class="flex items-center space-x-4">
                <input
                    type="radio"
                    name="is-voice-over"
                    v-model="form.isVoiceOver"
                    :value="false"
                    class="w-5 h-5 rounded-full"
                />
                <p class="text-sm">No</p>
            </div>
        </div>

        <!-- any other requirements textarea -->
        <div>
            <jet-label
                class="mb-1 font-semibold"
                for="video-requirements"
                value="Do you have any specific comment or requirement in regards to the animation of the video?"
            />
            <text-area-custom
                id="video-requirements"
                class="block w-full lg:w-4/5"
                v-model="form.videoRequirements.value"
                placeholder="Type comment or requirements here"
            ></text-area-custom>
        </div>
    </video-step-layout>
</template>

<script>
import VideoStepLayout from '@/Layouts/VideoStepLayout'
import JetLabel from '@/Jetstream/Label'
import JetInput from '@/Jetstream/Input'
import TextAreaCustom from '@/Components/TextAreaCustom'
import VideoStepInOrderProcessMixin from '@/Mixins/VideoStepInOrderProcess'
import { videoSessionType } from '@/Utilities/VideoStatus'
import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'

export default {
    mixins: [VideoStepInOrderProcessMixin],

    components: {
        VideoStepLayout,
        JetLabel,
        JetInput,
        TextAreaCustom
    },

    data() {
        return {
            videoSessionType,
            currentPageNumber: 3,
            form: {
                videoTitle: this.$page.video.name,
                keyMessage: {
                    value: this.$page.video.key_message,
                    isRemember: false
                },
                isVoiceOver: this.$page.video.is_voice_over,
                videoRequirements: {
                    value: this.$page.video.other_requirements,
                    isRemember: false
                },
                purposeTarget: {
                    value: this.$page.video.purposes.map(purpose => purpose.id),
                    isRemember: false
                },
                platformTarget: {
                    value: this.$page.video.platforms.map(
                        platform => platform.id
                    ),
                    isRemember: false,
                    other: ''
                },
                languageTarget: {
                    value: this.$page.video.languages
                        ? JSON.parse(this.$page.video.languages)
                        : [],
                    isRemember: false,
                    other: ''
                },
                actorPrefs: {
                    value: this.$page.video.actor_pref,
                    isRemember: false
                }
            },
            isOtherPlatformOptionSelected: false,
            isOtherLanguageOptionSelected: false
        }
    },

    watch: {
        isOtherPlatformOptionSelected(isSelected) {
            if (!isSelected) {
                this.form.platformTarget.other = ''
            }
        },

        isOtherLanguageOptionSelected(isSelected) {
            if (!isSelected) {
                this.form.languageTarget.other = ''
            }
        }
    },

    mounted() {
        this.preparePlatformsData()

        this.prepareLanguagesData()
    },

    methods: {
        async submit(payload) {
            let variables = {
                id: this.$page.video.id,
                video: {
                    name: payload.videoName,
                    session_type: this.$page.video.session_type,
                    key_message: this.form.keyMessage.value
                        ? this.form.keyMessage.value
                        : '',
                    other_requirements: this.form.videoRequirements.value
                        ? this.form.videoRequirements.value
                        : '',
                    is_voice_over: this.form.isVoiceOver,
                    purposes: {
                        sync: this.form.purposeTarget.value
                    },
                    platforms: this.getVideoPlatforms(),
                    languages: this.getVideoLanguages(),
                    actor_pref: this.form.actorPrefs.value
                }
            }

            // submit video data
            await this.$apollo.mutate({
                // Mutation
                mutation: UpdateVideoMutation,
                // Parameters
                variables: variables
            })

            return true
        },

        preparePlatformsData() {
            const platformIds = this.getPlatformIds()

            const otherPlatforms = this.$page.video.platforms.filter(
                videoPlatform => !platformIds.includes(videoPlatform.id)
            )

            if (otherPlatforms.length) {
                // check the other platforms checkbox
                this.isOtherPlatformOptionSelected = true
                // show the other selected data into the input
                this.form.platformTarget.other = otherPlatforms
                    .map(otherPlatform => otherPlatform.name)
                    .join(', ')
            }
        },

        getVideoPlatforms() {
            if (
                this.isOtherPlatformOptionSelected &&
                this.form.platformTarget.other
            ) {
                // don't include the platforms which are already included as others
                const videoPlatformNames = this.$page.video.platforms.map(
                    platform => platform.name
                )

                const dataPlatformNames = this.$page.data.platforms.map(
                    platform => platform.name
                )

                const otherPlatformsArray = this.form.platformTarget.other
                    .split(',')
                    .map(item => item.trim())

                const otherPlatformsCreate = otherPlatformsArray.filter(
                    item =>
                        !videoPlatformNames.includes(item) &&
                        !dataPlatformNames.includes(item)
                )

                const createPayload = otherPlatformsCreate.map(item => {
                    return {
                        name: item,
                        is_verified: false
                    }
                })

                const otherPlatformsSync = otherPlatformsArray
                    .filter(
                        item =>
                            videoPlatformNames.includes(item) &&
                            !dataPlatformNames.includes(item)
                    )
                    .map(platformName => {
                        return this.$page.video.platforms.find(
                            videoPlatform => videoPlatform.name === platformName
                        ).id
                    })

                const syncPayload = this.getVideoPlatformExceptOther().concat(
                    otherPlatformsSync
                )

                return {
                    create: createPayload,
                    sync: syncPayload
                }
            }

            return {
                sync: this.getVideoPlatformExceptOther()
            }
        },

        getVideoPlatformExceptOther() {
            const platformIds = this.getPlatformIds()

            return this.form.platformTarget.value.filter(platform =>
                platformIds.includes(platform)
            )
        },

        prepareLanguagesData() {
            const languagesData = this.getLanguagesOptions()

            let otherLanguages = JSON.parse(this.$page.video.languages)

            if (otherLanguages) {
                otherLanguages = otherLanguages.filter(
                    language => !languagesData.includes(language)
                )

                if (otherLanguages.length) {
                    this.isOtherLanguageOptionSelected = true
                    this.form.languageTarget.other = otherLanguages.join(', ')
                }
            }
        },

        getVideoLanguages() {
            if (
                this.isOtherLanguageOptionSelected &&
                this.form.languageTarget.other.trim()
            ) {
                const otherLanguages = this.form.languageTarget.other
                    .trim()
                    .split(',')
                    .map(item => item.trim())

                return JSON.stringify([
                    ...new Set(
                        this.form.languageTarget.value.concat(otherLanguages)
                    )
                ])
            }

            const languagesData = this.getLanguagesOptions()

            return JSON.stringify(
                this.form.languageTarget.value.filter(language =>
                    languagesData.includes(language)
                )
            )
        },

        getPlatformIds() {
            return this.$page.data.platforms.map(
                dataPlatform => dataPlatform.id
            )
        },

        getLanguagesOptions() {
            return this.$page.data.languages.map(language => language.name)
        }
    }
}
</script>

<template>
    <video-step-layout :active="currentPageNumber" @submit="submitFormData">
        <toast
            v-if="isToast.show"
            :role="isToast.role"
            @close="isToast.show = false"
        >
            {{ isToast.message }}
        </toast>
        <!-- attach campaign -->
        <div>
            <jet-label
                class="font-semibold"
                for="attach-campaign"
                value="Would you like to attach this video to a specific campaign?"
            />
            <select
                v-if="!newCampaign.isOpen"
                v-model="form.campaignId"
                id="attach-campaign"
                class="w-full lg:w-2/5 mt-1.5 border-gray-400 rounded"
                @change="showCreateNewCampaignSectionIfSelected"
            >
                <option disabled :value="null">
                    Select a campaign
                </option>
                <option
                    v-for="campaign in $page.data.campaigns"
                    :key="campaign.id"
                    :value="campaign.id"
                    :class="{
                        'text-gray-400': form.campaignId === campaign.id
                    }"
                >
                    {{ campaign.name }}
                </option>
                <option value="createNewCampaign" class="font-semibold"
                    >+ Create a new campaign</option
                >
            </select>
            <div v-else class="mt-1.5">
                <jet-input
                    type="text"
                    class="block w-full lg:w-3/5 mt-1.5"
                    :class="{ 'border-red-500': !isCampaignNameValid }"
                    placeholder="Enter a campaign name"
                    v-model="newCampaign.name"
                />
                <div class="mt-2">
                    <button
                        type="button"
                        class="p-2 mr-2 text-gray-500 transition hover:text-gray-800 focus:outline-none focus:ring-2"
                        @click="addNewCampaign"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="p-2 text-gray-500 transition hover:text-gray-800 focus:outline-none focus:ring-2"
                        @click="closeAddCampaignSection"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- video session type -->
        <div>
            <jet-label
                class="font-semibold"
                value="Select the video session type"
            />
            <div
                class="mt-2 border border-transparent rounded-md lg:w-3/5"
                :class="{ 'border-red-500': !videoSessionTypes.isValid }"
            >
                <div
                    class="flex items-start px-4 py-2.5 space-x-4 transition border-l border-t border-r rounded-tl-md rounded-tr-md hover:cursor-pointer hover:bg-splashr-violet-soft-glow group"
                    :class="[
                        form.sessionType === videoSessionTypes.motion
                            ? 'border-splashr-violet-light bg-splashr-violet-soft-glow'
                            : 'border-gray-400'
                    ]"
                    @click="setSessionType(videoSessionTypes.motion)"
                >
                    <input
                        v-model="form.sessionType"
                        type="radio"
                        :value="videoSessionTypes.motion"
                        name="session_type"
                        class="w-5 h-5 mt-1.5 pointer-events-none"
                    />
                    <div>
                        <div
                            class="text-lg font-semibold text-gray-700 group-hover:text-splashr-violet-deep"
                        >
                            Motion
                        </div>
                        <div
                            class="text-sm text-gray-500 group-hover:text-splashr-violet-deep"
                        >
                            You provide the creative assets and we do our magic.
                        </div>
                    </div>
                </div>
                <div
                    :class="[
                        form.sessionType
                            ? 'bg-splashr-violet-light'
                            : 'bg-gray-400'
                    ]"
                    style="height: 1px;"
                ></div>
                <div
                    class="flex items-start px-4 py-2.5 space-x-4 transition border-b border-l border-r border-gray-400 rounded-bl-md rounded-br-md hover:cursor-pointer hover:bg-splashr-violet-soft-glow group"
                    :class="[
                        form.sessionType === videoSessionTypes.acting
                            ? 'border-splashr-violet-light bg-splashr-violet-soft-glow'
                            : 'border-gray-400'
                    ]"
                    @click="setSessionType(videoSessionTypes.acting)"
                >
                    <input
                        v-model="form.sessionType"
                        type="radio"
                        :value="videoSessionTypes.acting"
                        name="session_type"
                        class="w-5 h-5 mt-1.5 pointer-events-none"
                    />
                    <div>
                        <div
                            class="text-lg font-semibold text-gray-700 group-hover:text-splashr-violet-deep"
                        >
                            Acting
                        </div>
                        <div
                            class="text-sm text-gray-500 group-hover:text-splashr-violet-deep"
                        >
                            We create your videos with professional actors and
                            film crew.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- product / service offer -->
        <div>
            <jet-label
                class="mb-1 font-semibold"
                for="service-offer"
                value="Describe your product or service offer"
            />
            <text-area-custom
                id="service-offer"
                class="block w-full lg:w-4/5"
                v-model="form.serviceOffer.value"
                :placeholder="
                    $page.data.remembered.serviceOffer
                        ? $page.data.remembered.serviceOffer
                        : 'Type your product or service offer here'
                "
            ></text-area-custom>
            <div class="flex items-center mt-3">
                <input
                    type="checkbox"
                    v-model="form.serviceOffer.isRemember"
                    class="w-5 h-5 rounded"
                />
                <p class="ml-3 text-sm text-gray-500">
                    Remember this answer
                </p>
            </div>
        </div>

        <!-- audience -->
        <div>
            <jet-label
                class="mb-1 font-semibold"
                for="target-audience"
                value="Describe your target audience"
            />
            <text-area-custom
                id="target-audience"
                class="block w-full lg:w-4/5"
                v-model="form.audience.value"
                :placeholder="
                    $page.data.remembered.audience
                        ? $page.data.remembered.audience
                        : 'Type something about your audience'
                "
            ></text-area-custom>
            <div class="flex items-center mt-3">
                <input
                    type="checkbox"
                    v-model="form.audience.isRemember"
                    class="w-5 h-5 rounded"
                />
                <p class="ml-3 text-sm text-gray-500">
                    Remember this answer
                </p>
            </div>
        </div>
    </video-step-layout>
</template>

<script>
import VideoStepLayout from '@/Layouts/VideoStepLayout'
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
import TextAreaCustom from '@/Components/TextAreaCustom'
import VideoStepInOrderProcessMixin from '@/Mixins/VideoStepInOrderProcess'
import { videoSessionType } from '@/Utilities/VideoStatus'
import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'
import CreateCampaignMutation from '@/Graphql/Video/Mutations/CreateCampaign.gql'
import RememberAnswersMutation from '@/Graphql/Video/Mutations/RememberAnswers.gql'

export default {
    mixins: [VideoStepInOrderProcessMixin],

    components: {
        VideoStepLayout,
        JetInput,
        JetLabel,
        TextAreaCustom
    },

    data() {
        return {
            currentPageNumber: 1,

            videoSessionTypes: {
                motion: videoSessionType.motion,
                acting: videoSessionType.acting,
                isValid: true
            },

            isToast: {
                show: false,
                role: 'success',
                message: ''
            },

            form: {
                campaignId: this.$page.video.campaign_id,
                sessionType: this.$page.video.session_type,
                serviceOffer: {
                    value: this.$page.video.service_offer,
                    isRemember: false
                },
                audience: {
                    value: this.$page.video.audience,
                    isRemember: false
                }
            },

            newCampaign: {
                name: '',
                isOpen: false
            }
        }
    },

    computed: {
        isCampaignNameValid() {
            return this.newCampaign.name.length > 255 ? false : true
        },
        isFormValid() {
            if (!this.form.sessionType) {
                return false
            }

            return true
        }
    },

    methods: {
        setSessionType(type) {
            this.form.sessionType = type
            this.videoSessionTypes.isValid = true
        },

        async submit(payload) {
            if (!this.isFormValid) {
                if (!this.form.sessionType) {
                    this.videoSessionTypes.isValid = false
                    this.isToast.message = 'Please select a video session type!'
                    this.isToast.role = 'danger'
                    this.isToast.show = true
                }
                return false
            }

            this.saveRememberAnswers()

            let variables = {
                id: this.$page.video.id,
                video: {
                    name: payload.videoName,
                    campaign_id: this.form.campaignId,
                    session_type: this.form.sessionType,
                    service_offer: this.form.serviceOffer.value
                        ? this.form.serviceOffer.value
                        : this.$page.data.remembered.serviceOffer
                        ? this.$page.data.remembered.serviceOffer
                        : '',
                    audience: this.form.audience.value
                        ? this.form.audience.value
                        : this.$page.data.remembered.audience
                        ? this.$page.data.remembered.audience
                        : ''
                }
            }

            // submit
            await this.$apollo.mutate({
                // Mutation
                mutation: UpdateVideoMutation,
                // Parameters
                variables: variables
            })

            return true
        },

        saveRememberAnswers() {
            // @mixin method: getSaveAnswerData(input)
            const serviceOffer = this.getSaveAnswerData('serviceOffer')
            const audience = this.getSaveAnswerData('audience')

            this.$apollo.mutate({
                // Mutation
                mutation: RememberAnswersMutation,
                // Parameters
                variables: {
                    answers: {
                        client_id: this.$page.video.client_id,
                        answers: JSON.stringify({
                            serviceOffer: serviceOffer,
                            audience: audience
                        })
                    }
                }
            })
        },

        showCreateNewCampaignSectionIfSelected() {
            if (this.form.campaignId === 'createNewCampaign') {
                this.newCampaign.isOpen = true
            }
        },

        closeAddCampaignSection() {
            this.newCampaign.isOpen = false
            this.form.campaignId = this.$page.video.campaign_id
            this.newCampaign.name = ''
        },

        async addNewCampaign() {
            if (this.newCampaign.name == '') {
                this.isToast.message =
                    'Campaign name is required. Please insert a name.'
                this.isToast.role = 'danger'
                this.isToast.show = true
                return
            }

            if (!this.isCampaignNameValid) {
                return
            }

            try {
                const response = await this.$apollo.mutate({
                    mutation: CreateCampaignMutation,
                    variables: {
                        campaign: {
                            name: this.newCampaign.name,
                            clients: {
                                connect: [this.$page.video.client_id]
                            }
                        }
                    }
                })

                // clear the input
                this.newCampaign.name = ''

                // push that select into the campaigns data
                this.$page.data.campaigns.push(response.data.createCampaign)
                this.form.campaignId = response.data.createCampaign.id

                // hide the input campaign section
                this.newCampaign.isOpen = false
            } catch (error) {
                console.log(error)
                alert('Oops! Something went wrong.')
            }
        }
    }
}
</script>

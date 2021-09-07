<template>
    <a
        :href="video.redirectTo"
        target="_blank"
        class="relative block w-48 text-left text-white transition rounded-md h-38 hover:ring-2 ring-indigo-500"
    >
        <div
            v-if="label"
            class="absolute z-30 shadow-lg rounded flex items-center space-x-1.5 px-1.5 py-0.5 bg-gray-800 top-1.5 right-1.5"
        >
            <div class="w-2 h-2 rounded-full" :class="labelColorClass"></div>
            <div class="text-xs capitalize">{{ label }}</div>
        </div>
        <div
            class="bg-gray-800 rounded-md"
            :class="{
                'opacity-60': label === missingVideoLabel
            }"
        >
            <img
                :src="video.videoBoxThumbnail"
                alt="Thumbnail"
                class="object-cover w-full h-24 rounded-tl-md rounded-tr-md"
            />
            <div class="pt-2 pb-2 pl-2 pr-1">
                <div class="text-sm font-semibold truncate">
                    {{ video.name }}
                </div>
                <div class="flex justify-between mt-1 text-gray-300 text-xxs">
                    <div>
                        {{ video.updated_at | diffForHumansTime }}
                    </div>
                    <div
                        v-if="label !== missingVideoLabel"
                        class="flex items-center"
                        :class="{
                            'opacity-30 pointer-events-none': isTypeLoading
                        }"
                    >
                        <div>Iteration</div>
                        <toggle-button
                            v-if="!isClient"
                            :check="!isMasterType"
                            class="z-40 w-10 h-4"
                            @click.native.prevent="toggleType"
                        ></toggle-button>
                    </div>
                </div>
            </div>
        </div>
    </a>
</template>

<script>
import { diffForHumansTime } from '@/Utilities/RelativeTime'
import { videoItemType, videoItemStatus } from '@/Utilities/VideoItems'

export default {
    components: {
        ToggleButton: () =>
            import(
                /* webpackChunkName: 'ToggleButton' */ '@/Components/ToggleButton'
            )
    },

    props: {
        video: {
            type: Object,
            required: true
        },
        isClient: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    data() {
        return {
            videoItemStatus,
            isTypeLoading: false,
            missingVideoLabel: 'Missing'
        }
    },

    computed: {
        isMasterType() {
            return this.video.video_item_type === videoItemType.master
                ? true
                : false
        },

        label() {
            if (!this.video.label) {
                return ''
            }
            if (this.video.label === 'none') {
                return this.missingVideoLabel
            }
            return this.video.label
                .split('_')
                .join(' ')
                .replace(/\w\S*/g, function(txt) {
                    return (
                        txt.charAt(0).toUpperCase() +
                        txt.substr(1).toLowerCase()
                    )
                })
        },

        labelColorClass() {
            if (!this.video.label) {
                return ''
            }
            switch (this.video.label) {
                case 'in_progress':
                    return 'bg-red-400'
                case 'approved':
                    return 'bg-green-300'
                case 'needs_review':
                    return 'bg-yellow-400'
                case 'paid':
                    return 'bg-purple-400'
                default:
                    return 'bg-gray-400'
            }
        }
    },

    filters: {
        diffForHumansTime(timeStamp) {
            if (!timeStamp) {
                return ''
            }
            return diffForHumansTime(timeStamp)
        }
    },

    methods: {
        toggleType() {
            this.isTypeLoading = true

            setTimeout(() => {
                this.isTypeLoading = false
            }, 2000)

            this.$emit('toggle-type', {
                video: this.video,
                isChecked: this.isMasterType
            })
        }
    }
}
</script>

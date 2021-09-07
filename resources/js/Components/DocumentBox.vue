<template>
    <a
        :href="document.redirectTo"
        target="_blank"
        class="block w-48 p-3 text-left text-white transition bg-gray-800 rounded-md h-38 hover:ring-2 ring-indigo-500"
        @click="$emit('clicked')"
    >
        <div
            class="flex items-center justify-center h-20 bg-gray-200 rounded-md"
            :class="details.bgClass"
        >
            <component :is="details.icon" class="w-20 h-20 "></component>
        </div>
        <div class="relative flex items-center justify-between p-1 mt-2 mb-1">
            <div
                v-if="document.is_changed && isClient"
                class="absolute w-3 h-3 rounded-full bottom-2 -right-1 bg-splashr-blue-light"
            ></div>
            <div>
                <div class="text-sm font-semibold truncate">
                    {{ details.title }}
                </div>
                <div class="mt-0.5 text-gray-300 text-xxs">
                    Updated {{ document.updated_at | diffForHumansTime }}
                </div>
            </div>
            <button
                v-if="!isClient"
                class="text-gray-200 transition hover:text-green-400 focus:outline-none focus:text-green-400"
                :class="{ 'animate-spin': document.isLoading }"
                @click="$emit('on-sync', document)"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    class="w-4 h-4"
                >
                    <g fill="none" fill-rule="evenodd">
                        <g>
                            <g>
                                <g>
                                    <path
                                        d="M0 0H20V20H0z"
                                        transform="translate(-342 -491) translate(175 371) translate(167 120)"
                                    />
                                    <path
                                        fill="currentColor"
                                        fill-rule="nonzero"
                                        d="M19.674 12.083c0 .044-.004.074-.013.091-.555 2.327-1.718 4.213-3.49 5.658C14.402 19.277 12.327 20 9.949 20c-1.267 0-2.494-.239-3.678-.716-1.185-.478-2.242-1.159-3.171-2.044l-1.68 1.68c-.165.164-.36.247-.586.247-.225 0-.42-.083-.586-.248-.165-.165-.247-.36-.247-.586V12.5c0-.226.082-.421.247-.586.165-.165.36-.247.586-.247h5.834c.225 0 .42.082.586.247.165.165.247.36.247.586 0 .226-.082.421-.247.586L5.469 14.87c.616.573 1.315 1.015 2.096 1.328.781.312 1.593.469 2.435.469 1.163 0 2.248-.282 3.255-.847 1.007-.564 1.814-1.34 2.422-2.33.096-.148.326-.656.69-1.524.07-.2.2-.3.39-.3h2.5c.114 0 .211.042.294.124.082.083.123.18.123.293zM20 1.667V7.5c0 .226-.082.421-.247.586-.165.165-.36.247-.586.247h-5.834c-.225 0-.42-.082-.586-.247-.165-.165-.247-.36-.247-.586 0-.226.082-.421.247-.586l1.797-1.797C13.26 3.928 11.744 3.333 10 3.333c-1.163 0-2.248.282-3.255.847-1.007.564-1.814 1.34-2.422 2.33-.096.148-.326.656-.69 1.524-.07.2-.2.3-.39.3H.65c-.113 0-.21-.042-.293-.124-.082-.083-.124-.18-.124-.293v-.091C.8 5.499 1.97 3.613 3.75 2.168 5.53.723 7.613 0 10 0c1.267 0 2.5.24 3.698.723 1.198.481 2.261 1.16 3.19 2.037l1.693-1.68c.165-.164.36-.247.586-.247.225 0 .42.083.586.248.165.165.247.36.247.586z"
                                        transform="translate(-342 -491) translate(175 371) translate(167 120)"
                                    />
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </button>
        </div>
    </a>
</template>

<script>
import { documentTypes } from '@/Utilities/Document'
import { diffForHumansTime } from '@/Utilities/RelativeTime'
import BackdropGoogleDoc from '@/Components/Icons/BackdropGoogleDoc'
import BackdropGoogleSlide from '@/Components/Icons/BackdropGoogleSlide'

export default {
    components: {
        BackdropGoogleDoc,
        BackdropGoogleSlide
    },

    props: {
        document: {
            type: Object,
            required: true
        },
        isClient: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    computed: {
        details() {
            switch (this.document.type) {
                case documentTypes.creativeIdea:
                    return {
                        title: 'Creative Ideas',
                        bgClass: 'bg-splashr-bg-doc',
                        icon: 'backdrop-google-doc'
                    }
                case documentTypes.storyboard:
                    return {
                        title: 'Storyboard',
                        bgClass: 'bg-splashr-bg-slide',
                        icon: 'backdrop-google-slide'
                    }
            }

            return {
                title: 'Creative Ideas',
                bgClass: 'splashr-bg-doc',
                icon: 'backdrop-google-doc'
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
    }
}
</script>

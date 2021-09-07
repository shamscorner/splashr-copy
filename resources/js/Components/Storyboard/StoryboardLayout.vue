<template>
    <app-layout>
        <div class="h-full pt-16 overflow-hidden sm:pt-20">
            <top-header
                back-nav
                :back-nav-route="
                    route('videos.show', {
                        video: this.$page.videoId,
                        currentTab: this.$page.fromTab
                    })
                "
            ></top-header>
            <div class="grid h-full grid-cols-12 2xl:px-10">
                <!-- left slides section -->
                <div
                    v-if="!noLeftPanel"
                    class="flex flex-col items-center h-full col-span-2 px-5 pt-5 space-y-5 overflow-y-auto text-center border-r pb-28"
                >
                    <slot name="left-panel"></slot>
                </div>
                <!-- main slide container section -->
                <div
                    class="pb-16 overflow-y-auto"
                    :class="[
                        isOpenSidePanel
                            ? noLeftPanel
                                ? 'col-start-2 col-span-8'
                                : 'col-span-7'
                            : noLeftPanel
                            ? 'col-start-3 col-end-11'
                            : 'col-start-4 col-end-12'
                    ]"
                >
                    <slot></slot>
                </div>
                <!-- side panel section -->
                <div
                    v-if="isOpenSidePanel"
                    class="h-full col-span-3 overflow-hidden border-l"
                >
                    <div class="flex justify-end px-4 pt-4 pb-2">
                        <button
                            class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2"
                            @click="$emit('close-side-panel')"
                        >
                            <close-icon class="w-5 h-5"></close-icon>
                        </button>
                    </div>
                    <slot name="side-panel"></slot>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import CloseIcon from '@/Components/Icons/CloseIcon'

export default {
    components: {
        AppLayout,
        TopHeader,
        CloseIcon
    },

    props: {
        isOpenSidePanel: {
            type: Boolean,
            required: false,
            default: false
        },
        noLeftPanel: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

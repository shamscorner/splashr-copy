<template>
    <inertia-link
        :href="
            $page.isEdit
                ? route('videos.update', {
                      video: $page.video.id,
                      step: stepHref,
                      sidebar: this.$page.isSideNavVisible
                          ? this.$page.isSideNavVisible
                          : 'true',
                      edit: true
                  })
                : route('videos.steps', {
                      video: $page.video.id,
                      step: stepHref,
                      sidebar: this.$page.isSideNavVisible
                          ? this.$page.isSideNavVisible
                          : 'true'
                  })
        "
        preserve-scroll
        preserve-state
        class="flex items-center space-x-3 group"
    >
        <div
            class="flex items-center justify-center w-10 h-10 transition border rounded-full border-splashr-violet-light"
            :class="[
                isActive
                    ? 'bg-splashr-violet-light group-hover:bg-purple-600 text-white'
                    : 'text-splashr-violet-light group-hover:border-purple-600'
            ]"
        >
            <p v-if="!isCompleted" class="font-semibold">
                {{ count }}
            </p>
            <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="w-5 h-5"
            >
                <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                />
            </svg>
        </div>
        <p
            class="transition text-splashr-violet-light group-hover:text-purple-600"
        >
            {{ step }}
        </p>
    </inertia-link>
</template>

<script>
import { calculateStepHref } from '@/Utilities/CalculateStepHref'

export default {
    props: {
        count: {
            type: Number,
            required: false,
            default: 1
        },

        isActive: {
            type: Boolean,
            required: false,
            default: false
        },

        isCompleted: {
            type: Boolean,
            required: false,
            default: false
        },

        step: {
            type: String,
            required: false,
            default: ''
        }
    },

    computed: {
        stepHref() {
            return calculateStepHref(this.step)
        }
    }
}
</script>

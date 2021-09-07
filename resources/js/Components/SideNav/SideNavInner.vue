<template>
    <div>
        <inertia-link
            :href="to"
            class="flex items-center justify-between w-full px-3 py-2 transition rounded-md hover:ring-2 focus:outline-none focus:ring-2"
            :class="{ 'bg-opacity-25 bg-splashr-violet-light': isChecked }"
            @click="isOpen = !isOpen"
        >
            <p>
                <slot name="title">Title</slot>
            </p>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-5 h-5"
                :class="{ 'transform rotate-90': isOpen }"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </inertia-link>
        <div v-if="isOpen" class="flex flex-col px-2 py-4 space-y-4">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isSelected: {
            type: Boolean,
            required: false,
            default: false
        },
        to: {
            type: String,
            required: false,
            default: ''
        },
        isChecked: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    data() {
        return {
            isOpen: this.isSelected
        }
    }
}
</script>

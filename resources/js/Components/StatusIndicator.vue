<template>
    <div class="absolute top-0 bottom-0 left-0 right-0 border-b border-white">
        <div class="flex justify-between w-full h-full">
            <div
                v-if="side == userType.client"
                class="w-3 bg-red-400 border-t border-b border-l border-white"
            ></div>
            <div class="flex items-center justify-center w-full px-2 text-sm">
                {{ status }}
            </div>
            <div
                v-if="side == userType.manager"
                class="w-3 border-t border-b border-r border-white bg-splashr-blue-light"
            ></div>
        </div>
    </div>
</template>

<script>
import { userType } from '@/Utilities/User'

export default {
    props: {
        side: {
            type: Number,
            required: false,
            default: 1
        },
        status: {
            type: String,
            required: false,
            default: 'Status'
        }
    },

    data() {
        return {
            userType
        }
    }
}
</script>

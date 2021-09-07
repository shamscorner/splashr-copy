<template>
    <div
        class="flex items-center -space-x-4"
        @mouseenter="isPlusButtonEnable = true"
        @mouseleave="isPlusButtonEnable = false"
    >
        <!-- plus button -->
        <div v-if="addProfile" class="z-40 h-5 w-7">
            <button
                v-if="isPlusButtonEnable"
                class="z-50 w-5 h-5 text-white bg-gray-400 rounded-full hover:bg-gray-500 focus:outline-none"
                title="Add manager in this order"
                @click.stop="$emit('on-add', extra)"
            >
                <plus-icon></plus-icon>
            </button>
        </div>
        <!-- if no profile -->
        <div
            v-if="profiles.length == 0"
            class="relative w-10 h-10 text-gray-400 group"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="w-10 h-10 p-1 border-2 border-gray-400 rounded-full"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                />
            </svg>
        </div>

        <!-- profiles -->
        <div v-else class="flex items-center -space-x-2">
            <div
                v-for="profile in profiles"
                :key="profile.id"
                class="relative w-10 h-10 group"
            >
                <jet-avatar
                    :src="profile.user.profile_photo_url"
                    :alt="
                        `${profile.user.first_name} ${profile.user.last_name}`
                    "
                    :name="
                        `${profile.user.first_name} ${profile.user.last_name}`
                    "
                    class="w-10 h-10 font-semibold border-2 border-white"
                    :title="
                        `${profile.user.first_name} ${profile.user.last_name} - ${profile.user.email}`
                    "
                ></jet-avatar>
                <button
                    v-if="removeOption"
                    class="absolute right-0 z-50 text-red-400 transition bg-white rounded-full opacity-0 -top-1 group-hover:opacity-100 focus:outline-none"
                    title="Remove manger from this order"
                    @click.stop="
                        $emit('on-remove', {
                            ...extra,
                            profile: profile
                        })
                    "
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="w-5 h-5"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import PlusIcon from '@/Components/Icons/PlusIcon'
import JetAvatar from '@/Jetstream/Avatar'

export default {
    components: {
        PlusIcon,
        JetAvatar
    },

    props: {
        profiles: {
            type: Array,
            required: false,
            default: () => {
                return []
            }
        },
        extra: {
            type: Object,
            required: false,
            default: () => {
                return null
            }
        },
        addProfile: {
            type: Boolean,
            required: false,
            default: true
        },
        removeOption: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    data() {
        return {
            isPlusButtonEnable: false
        }
    }
}
</script>

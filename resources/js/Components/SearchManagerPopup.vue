<template>
    <div
        class="absolute top-0 bottom-0 left-0 right-0 z-50 flex flex-col items-center p-5 bg-gray-900 bg-opacity-60"
        @click.self="$emit('on-close')"
    >
        <button
            class="absolute top-20 right-10 lg:right-40 focus:outline-none focus:ring-2 focus:ring-gray-300"
            @click="$emit('on-close')"
        >
            <close-icon
                class="w-10 h-10 text-white md:w-16 md:h-16"
            ></close-icon>
        </button>

        <input-searchable
            v-model="inputSearchManagers"
            placeholder="Search Managers"
            class="w-full py-2 bg-white border-gray-500 rounded-lg mt-36 md:w-4/5 lg:w-1/2"
            @keyup.native="searchManagers"
        ></input-searchable>
        <div
            v-if="isLoading"
            class="w-full h-1.5 md:w-4/5 lg:w-1/2 bg-splashr-blue-light rounded-full animate-pulse"
        ></div>
        <empty-row-message
            v-if="!searchedManagers.length && isSearched"
            class="rounded-lg md:w-4/5 lg:w-1/2"
        >
            No managers found!
        </empty-row-message>
        <div
            v-else-if="searchedManagers.length && isSearched"
            class="flex flex-col w-full mt-2 overflow-y-auto bg-white divide-y rounded-lg cursor-pointer max-h-96 md:w-4/5 lg:w-1/2"
        >
            <div
                v-for="manager in searchedManagers"
                :key="manager.id"
                class="flex items-center p-4 space-x-4 transition hover:bg-gray-200"
                @click="$emit('on-selected', manager)"
            >
                <jet-avatar
                    :src="manager.user.profile_photo_url"
                    :alt="
                        `${manager.user.first_name} ${manager.user.last_name}`
                    "
                    :name="
                        `${manager.user.first_name} ${manager.user.last_name}`
                    "
                    class="w-10 h-10 text-lg font-semibold"
                ></jet-avatar>
                <div>
                    <div class="font-semibold">
                        {{
                            manager.user.first_name +
                                ' ' +
                                manager.user.last_name
                        }}
                    </div>
                    <div class="text-gray-500">
                        {{ manager.user.email }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputSearchable from '@/Components/InputSearchable'
import CloseIcon from '@/Components/Icons/CloseIcon'
import EmptyRowMessage from '@/Components/Table/EmptyRowMessage'
import JetAvatar from '@/Jetstream/Avatar'
import SearchManagers from '@/Graphql/Manager/Queries/SearchManagers.gql'

export default {
    components: {
        InputSearchable,
        CloseIcon,
        EmptyRowMessage,
        JetAvatar
    },

    data() {
        return {
            inputSearchManagers: '',
            searchedManagers: [],
            isLoading: false,
            isSearched: false
        }
    },

    mounted() {
        document.addEventListener('keyup', event => {
            // close the popup if the user presses escape button
            if (event.key == 'Escape') {
                this.$emit('on-close')
            }
        })
    },

    methods: {
        searchManagers() {
            // use lodash to call search event
            this.debouncedQueryForManagers()
        },

        debouncedQueryForManagers: window._.debounce(function() {
            // fetch data from api
            this.fetchManagers()
        }, 1000),

        async fetchManagers() {
            if (!this.inputSearchManagers) {
                return
            }
            this.isLoading = true
            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: SearchManagers,
                    // Parameters
                    variables: {
                        query: this.inputSearchManagers
                    }
                })
                this.searchedManagers = response.data.searchManagers || []
                this.isSearched = true
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isLoading = false
            }
        }
    }
}
</script>

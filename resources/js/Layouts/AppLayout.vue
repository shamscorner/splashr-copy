<template>
    <div class="relative h-screen">
        <nav class="sticky top-0 z-30 bg-white shadow-md">
            <!-- Primary Navigation Menu -->
            <div class="px-4 mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 sm:h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex items-center flex-shrink-0">
                            <inertia-link :href="route('dashboard')">
                                <jet-application-mark
                                    class="block w-auto h-16"
                                />
                            </inertia-link>
                        </div>

                        <!-- Navigation Links -->
                        <!-- <div
                            class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                        >
                            <jet-nav-link
                                :href="route('dashboard')"
                                :active="$page.currentRouteName == 'dashboard'"
                            >
                                Dashboard
                            </jet-nav-link>
                        </div> -->
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- bell notification -->
                        <!-- <bell-notification class="ml-8"></bell-notification> -->

                        <!-- profile and dropdown menu -->
                        <div class="relative ml-8">
                            <jet-dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        v-if="
                                            $page.jetstream.managesProfilePhotos
                                        "
                                        class="flex transition duration-150 ease-in-out border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300"
                                    >
                                        <!-- <img
                                            class="object-cover w-12 h-12 rounded-full"
                                            :src="$page.user.profile_photo_url"
                                            :alt="userName"
                                        /> -->
                                        <jet-avatar
                                            :src="$page.user.profile_photo_url"
                                            :alt="userName"
                                            :name="userName"
                                            class="w-12 h-12 text-lg font-semibold"
                                        ></jet-avatar>
                                    </button>

                                    <button
                                        v-else
                                        class="flex items-center font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                                    >
                                        <div>{{ userName }}</div>

                                        <div class="ml-1">
                                            <svg
                                                class="w-5 h-5 fill-current"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                </template>

                                <template #content>
                                    <!-- credits info -->
                                    <div v-if="creditsDataByUser" class="px-4">
                                        <div class="block py-2 text-gray-400">
                                            Credits
                                        </div>
                                        <div
                                            class="flex flex-col pb-4 space-y-2 text-xs text-gray-700"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <div>Video creation</div>
                                                <div>
                                                    {{
                                                        creditsDataByUser[
                                                            creditType
                                                                .videoCreation
                                                        ]
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    creditsDataByUser[
                                                        creditType
                                                            .videoIteration
                                                    ]
                                                "
                                                class="flex items-center justify-between"
                                            >
                                                <div>Format iteration</div>
                                                <div>
                                                    {{
                                                        creditsDataByUser[
                                                            creditType
                                                                .videoIteration
                                                        ]
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    creditsDataByUser[
                                                        creditType.richContent
                                                    ]
                                                "
                                                class="flex items-center justify-between"
                                            >
                                                <div>Rich content</div>
                                                <div>
                                                    {{
                                                        creditsDataByUser[
                                                            creditType
                                                                .richContent
                                                        ]
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="border-t border-gray-100"
                                        ></div>
                                    </div>

                                    <!-- Account Management -->
                                    <div
                                        class="block px-4 py-2 text-sm text-gray-400"
                                    >
                                        Manage Account
                                    </div>

                                    <jet-dropdown-link
                                        :href="route('profile.show')"
                                    >
                                        Profile
                                    </jet-dropdown-link>

                                    <!-- <jet-dropdown-link
                                        :href="route('api-tokens.index')"
                                        v-if="$page.jetstream.hasApiFeatures"
                                    >
                                        API Tokens
                                    </jet-dropdown-link> -->

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Management -->
                                    <!-- <template
                                        v-if="$page.jetstream.hasTeamFeatures"
                                    >
                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Manage Team
                                        </div>

                                        <jet-dropdown-link
                                            :href="
                                                route(
                                                    'teams.show',
                                                    $page.user.current_team
                                                )
                                            "
                                        >
                                            Team Settings
                                        </jet-dropdown-link>

                                        <jet-dropdown-link
                                            :href="route('teams.create')"
                                            v-if="
                                                $page.jetstream.canCreateTeams
                                            "
                                        >
                                            Create New Team
                                        </jet-dropdown-link>

                                        <div
                                            class="border-t border-gray-100"
                                        ></div>

                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Switch Teams
                                        </div>

                                        <template
                                            v-for="team in $page.user.all_teams"
                                        >
                                            <form
                                                @submit.prevent="
                                                    switchToTeam(team)
                                                "
                                            >
                                                <jet-dropdown-link as="button">
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <svg
                                                            v-if="
                                                                team.id ==
                                                                    $page.user
                                                                        .current_team_id
                                                            "
                                                            class="w-5 h-5 mr-2 text-green-400"
                                                            fill="none"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            ></path>
                                                        </svg>
                                                        <div>
                                                            {{ team.name }}
                                                        </div>
                                                    </div>
                                                </jet-dropdown-link>
                                            </form>
                                        </template>

                                        <div
                                            class="border-t border-gray-100"
                                        ></div>
                                    </template> -->
                                    <!-- End of Team Management section -->

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout">
                                        <jet-dropdown-link as="button">
                                            Logout
                                        </jet-dropdown-link>
                                    </form>
                                </template>
                            </jet-dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="flex items-center -mr-2 sm:hidden">
                        <!-- bell notification -->
                        <!-- <bell-notification class="mr-4"></bell-notification> -->

                        <button
                            @click="
                                showingNavigationDropdown = !showingNavigationDropdown
                            "
                            class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500"
                        >
                            <svg
                                class="w-6 h-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown
                                    }"
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

            <!-- Responsive Navigation Menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown
                }"
                class="sm:hidden"
            >
                <!-- <div class="pt-2 pb-3 space-y-1">
                    <jet-responsive-nav-link
                        :href="route('dashboard')"
                        :active="$page.currentRouteName == 'dashboard'"
                    >
                        Dashboard
                    </jet-responsive-nav-link>
                </div> -->

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <!-- <img
                                class="w-10 h-10 rounded-full"
                                :src="$page.user.profile_photo_url"
                                :alt="userName"
                            /> -->
                            <jet-avatar
                                :src="$page.user.profile_photo_url"
                                :alt="userName"
                                :name="userName"
                                class="w-10 h-10 text-lg font-semibold"
                            ></jet-avatar>
                        </div>

                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">
                                {{ userName }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.user.email }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <!-- credits info -->
                        <div v-if="creditsDataByUser" class="pl-4 pr-6">
                            <div class="block py-2 text-gray-400">
                                Credits
                            </div>
                            <div
                                class="flex flex-col pb-4 space-y-2 text-xs text-gray-700"
                            >
                                <div class="flex items-center justify-between">
                                    <div>Video creation</div>
                                    <div>
                                        {{
                                            creditsDataByUser[
                                                creditType.videoCreation
                                            ]
                                        }}
                                    </div>
                                </div>
                                <div
                                    v-if="
                                        creditsDataByUser[
                                            creditType.videoIteration
                                        ]
                                    "
                                    class="flex items-center justify-between"
                                >
                                    <div>Format iteration</div>
                                    <div>
                                        {{
                                            creditsDataByUser[
                                                creditType.videoIteration
                                            ]
                                        }}
                                    </div>
                                </div>
                                <div
                                    v-if="
                                        creditsDataByUser[
                                            creditType.richContent
                                        ]
                                    "
                                    class="flex items-center justify-between"
                                >
                                    <div>Rich content</div>
                                    <div>
                                        {{
                                            creditsDataByUser[
                                                creditType.richContent
                                            ]
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100"></div>
                        </div>

                        <jet-responsive-nav-link
                            :href="route('profile.show')"
                            :active="$page.currentRouteName == 'profile.show'"
                        >
                            Profile
                        </jet-responsive-nav-link>

                        <!-- <jet-responsive-nav-link
                            :href="route('api-tokens.index')"
                            :active="
                                $page.currentRouteName == 'api-tokens.index'
                            "
                            v-if="$page.jetstream.hasApiFeatures"
                        >
                            API Tokens
                        </jet-responsive-nav-link> -->

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <jet-responsive-nav-link as="button">
                                Logout
                            </jet-responsive-nav-link>
                        </form>

                        <!-- Team Management -->
                        <!-- <template v-if="$page.jetstream.hasTeamFeatures">
                            <div class="border-t border-gray-200"></div>

                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Manage Team
                            </div>

                            <jet-responsive-nav-link
                                :href="
                                    route('teams.show', $page.user.current_team)
                                "
                                :active="$page.currentRouteName == 'teams.show'"
                            >
                                Team Settings
                            </jet-responsive-nav-link>

                            <jet-responsive-nav-link
                                :href="route('teams.create')"
                                :active="
                                    $page.currentRouteName == 'teams.create'
                                "
                            >
                                Create New Team
                            </jet-responsive-nav-link>

                            <div class="border-t border-gray-200"></div>

                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Switch Teams
                            </div>

                            <template v-for="team in $page.user.all_teams">
                                <form
                                    @submit.prevent="switchToTeam(team)"
                                    :key="team.id"
                                >
                                    <jet-responsive-nav-link as="button">
                                        <div class="flex items-center">
                                            <svg
                                                v-if="
                                                    team.id ==
                                                        $page.user
                                                            .current_team_id
                                                "
                                                class="w-5 h-5 mr-2 text-green-400"
                                                fill="none"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                ></path>
                                            </svg>
                                            <div>{{ team.name }}</div>
                                        </div>
                                    </jet-responsive-nav-link>
                                </form>
                            </template>
                        </template> -->
                        <!-- End of Team Management section -->
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="absolute top-0 bottom-0 left-0 right-0">
            <slot></slot>
        </main>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple> </portal-target>
    </div>
</template>

<script>
// import JetApplicationLogo from '@/Jetstream/ApplicationLogo'
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
// import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import JetAvatar from '@/Jetstream/Avatar'
import GetCreditsDataByUser from '@/Graphql/User/Queries/GetCreditsDataByUser.gql'
import { creditType } from '@/Utilities/User'

export default {
    components: {
        // JetApplicationLogo,
        JetApplicationMark,
        JetDropdown,
        JetDropdownLink,
        JetAvatar,
        // JetNavLink,
        JetResponsiveNavLink
    },

    data() {
        return {
            showingNavigationDropdown: false,
            creditsDataByUser: null,
            creditType
        }
    },

    mounted() {
        // fetch and show the credits section in dropdown
        // show only if the user is a client, otherwise don't
        this.fetchCreditsData()
    },

    methods: {
        // switchToTeam(team) {
        //     this.$inertia.put(
        //         route('current-team.update'),
        //         {
        //             team_id: team.id
        //         },
        //         {
        //             preserveState: false
        //         }
        //     )
        // },

        logout() {
            // eslint-disable-next-line no-undef
            axios.post(route('logout').url()).then(() => {
                window.location = '/'
            })
        },

        async fetchCreditsData() {
            try {
                const response = await this.$apollo.query({
                    query: GetCreditsDataByUser,
                    variables: {
                        userId: this.$page.user.id,
                        companyId: this.$page.user.company_id
                    }
                })

                if (response.data.creditsDataByUser) {
                    this.creditsDataByUser = JSON.parse(
                        response.data.creditsDataByUser
                    )
                }
            } catch (error) {
                // Error
                console.error(error)
            }
        }
    },

    computed: {
        path() {
            return window.location.pathname
        },

        userName() {
            return this.$page.user.first_name + ' ' + this.$page.user.last_name
        }
    }
}
</script>

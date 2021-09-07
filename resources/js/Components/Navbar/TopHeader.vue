<template>
    <div class="flex items-center justify-between pt-5 pb-3 pr-10 border-b">
        <div class="flex items-center pl-4 space-x-2 xl:pl-0">
            <inertia-link
                v-if="backNav"
                class="px-2 ml-4 text-gray-400 transition hover:text-gray-800 focus:outline-none focus:text-gray-700"
                :href="backNavRoute"
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
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </inertia-link>

            <page-title>
                {{ $page.currentSideNavName }}
            </page-title>
        </div>

        <div class="flex space-x-10">
            <slot>
                <!-- bell notification -->
                <bell-notification
                    class="text-gray-500"
                    @click.native="openDetailsPanel('Activities')"
                ></bell-notification>
                <!-- task todo -->
                <tasks-button
                    v-if="isTodo"
                    @click.native="openDetailsPanel('Tasks')"
                ></tasks-button>
            </slot>

            <slot name="extras"></slot>
        </div>

        <portal to="modal">
            <side-panel
                v-if="isDetailsPanel"
                @on-close="isDetailsPanel = false"
            >
                <template #header>
                    <div class="mb-2">
                        {{ sidePanelTitle ? sidePanelTitle : '' }}
                    </div>
                    <session-badge
                        v-if="sessionType"
                        :session-type="sessionType"
                    ></session-badge>
                </template>

                <template #tabs>
                    <nav class="flex px-5">
                        <tab-button
                            v-for="tab in tabs"
                            :key="tab"
                            :is-selected="currentTab == tab"
                            @on-selected="selectTab(tab)"
                            :class="[tabs.length > 1 ? 'flex-1' : 'self-start']"
                        >
                            {{ tab }}
                        </tab-button>
                    </nav>
                </template>

                <template>
                    <keep-alive>
                        <activities
                            v-if="currentTab == 'Activities'"
                            :key="currentTab"
                            :tab-key="tabKey"
                            :order-id="orderId"
                            class="pb-16"
                        ></activities>
                    </keep-alive>
                    <keep-alive>
                        <todo
                            v-if="currentTab == 'Tasks' && isTodo"
                            :key="currentTab"
                            :tab-key="tabKey"
                            class="pb-10"
                        ></todo>
                    </keep-alive>
                </template>
            </side-panel>
        </portal>
    </div>
</template>

<script>
import PageTitle from '@/Components/PageTitle'
import BellNotification from '@/Components/Navbar/BellNotification'
import TasksButton from '@/Components/Navbar/TasksButton'

export default {
    components: {
        PageTitle,
        BellNotification,
        TasksButton,
        SessionBadge: () =>
            import(
                /* webpackChunkName: 'SessionBadge' */ '@/Components/VideoSummary/SessionBadge'
            ),
        TabButton: () =>
            import(
                /* webpackChunkName: 'TabButton' */ '@/Components/Tab/TabButton'
            ),
        SidePanel: () =>
            import(
                /* webpackChunkName: 'SidePanel' */ '@/Components/SidePanel'
            ),
        Todo: () =>
            import(/* webpackChunkName: 'Todo' */ '@/Components/Todo/Todo'),
        Activities: () =>
            import(
                /* webpackChunkName: 'Activities' */ '@/Components/Activities/Activities'
            )
    },

    props: {
        backNav: {
            type: Boolean,
            required: false,
            default: false
        },
        backNavRoute: {
            type: String,
            required: false,
            default: () => {
                // eslint-disable-next-line no-undef
                return route('dashboard')
            }
        },
        orderId: {
            type: String,
            required: false,
            default: ''
        },
        isTodo: {
            type: Boolean,
            required: false,
            default: true
        },
        sidePanelTitle: {
            type: String,
            required: false,
            default: ''
        },
        sessionType: {
            type: String,
            required: false,
            default: ''
        }
    },

    data() {
        return {
            isDetailsPanel: false,
            tabKey: 1,
            tabs: this.isTodo ? ['Activities', 'Tasks'] : ['Activities'],
            currentTab: 'Activities' // make it Activities by default
        }
    },

    methods: {
        selectTab(tab) {
            ++this.tabKey
            this.currentTab = tab
        },

        openDetailsPanel(tab) {
            this.currentTab = tab
            this.isDetailsPanel = true
        }
    }
}
</script>

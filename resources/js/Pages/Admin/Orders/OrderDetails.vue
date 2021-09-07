<template>
    <dashboard-admin-layout>
        <!-- toast alert -->
        <toast
            v-if="isToast.show"
            :role="isToast.role"
            @close="isToast.show = false"
        >
            {{ isToast.message }}
        </toast>
        <!-- top header section -->
        <top-header>
            <!-- comments -->
            <comments-button
                @click.native="openDetailsPanel('Comments')"
                :is-notification-dot="
                    hasCommentsUnanswered && isCommentsNotificationDot
                "
            ></comments-button>
            <!-- bell notification -->
            <bell-notification
                class="text-gray-500"
                @click.native="openDetailsPanel('Activities')"
            ></bell-notification>
            <!-- task todo -->
            <tasks-button
                @click.native="openDetailsPanel('Tasks')"
            ></tasks-button>
        </top-header>
        <!-- quick intro section -->
        <div class="px-6 pt-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-4 text-2xl font-semibold">
                    <!-- video title -->
                    <div>{{ $page.orderDetails.video.name }}</div>
                    <!-- google drive link -->
                    <a
                        v-if="$page.folders.projectFolderId"
                        :href="
                            `https://drive.google.com/drive/u/6/folders/${$page.folders.projectFolderId}`
                        "
                        target="_blank"
                        class="inline-block"
                    >
                        <google-drive-icon></google-drive-icon>
                    </a>
                    <!-- video session type -->
                    <session-badge
                        v-if="$page.orderDetails.video.session_type"
                        :session-type="$page.orderDetails.video.session_type"
                    ></session-badge>
                </div>
                <!-- input frame.io video id -->
                <div
                    class="flex items-center pl-3 pr-1.5 py-1.5 space-x-3 border-2 border-gray-200 rounded-full"
                >
                    <frame-io-icon class="w-4 h-4"></frame-io-icon>
                    <input
                        v-model="inputFrameIoProjectId"
                        type="text"
                        placeholder="Frame.io asset url"
                        class="p-0 text-sm placeholder-gray-400 border-none w-52 lg:w-80 focus:ring-0"
                    />
                    <button
                        class="text-white transition bg-gray-400 rounded-full hover:bg-gray-600 focus:outline-none focus:bg-gray-600"
                        :class="{
                            'opacity-30 pointer-events-none':
                                !inputFrameIoProjectId ||
                                isLoadingInputFrameIoProjectId
                        }"
                        @click="addFrameIoProjectId"
                    >
                        <plus-icon
                            :class="{
                                'transform rotate-45': !inputFrameIoAddMode
                            }"
                        ></plus-icon>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between my-1">
                <!-- client info -->
                <div class="flex items-center space-x-3">
                    <jet-avatar
                        :src="
                            $page.orderDetails.video.client.user
                                .profile_photo_url
                        "
                        :alt="
                            `${$page.orderDetails.video.client.user.first_name} ${$page.orderDetails.video.client.user.last_name}`
                        "
                        :name="
                            `${$page.orderDetails.video.client.user.first_name} ${$page.orderDetails.video.client.user.last_name}`
                        "
                        :title="
                            `${$page.orderDetails.video.client.user.first_name} ${$page.orderDetails.video.client.user.last_name}`
                        "
                        class="w-10 h-10 font-semibold"
                    ></jet-avatar>
                    <div class="text-gray-500">
                        <div class="font-semibold ">
                            <inertia-link
                                :href="
                                    route('admin.clients.profile', {
                                        client:
                                            $page.orderDetails.video.company_id
                                    })
                                "
                                class="hover:underline"
                            >
                                {{ $page.orderDetails.video.company.name }}
                            </inertia-link>
                        </div>
                        <div class="text-sm">
                            <a
                                :href="
                                    `mailto:${$page.orderDetails.video.client.user.email}`
                                "
                                class="underline"
                            >
                                {{ $page.orderDetails.video.client.user.email }}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- manager info -->
                <div class="self-end">
                    <profile-stack
                        :profiles="$page.orderDetails.managers"
                        :extra="{
                            orderId: $page.orderDetails.id,
                            videoId: $page.orderDetails.video_id,
                            managers: $page.orderDetails.managers
                        }"
                        @on-add="onAddPeopleInProfile"
                        @on-remove="onRemovePeopleFromProfile"
                    ></profile-stack>
                </div>
            </div>
        </div>

        <!-- details section -->
        <div class="min-h-screen mt-5">
            <!-- tabs -->
            <nav
                class="flex justify-start px-6"
                :class="{
                    'opacity-30 pointer-events-none': isPickerLoading
                }"
            >
                <tab-button
                    v-for="tab in tabs"
                    :key="tab"
                    :is-selected="currentTab == tab"
                    @on-selected="currentTab = tab"
                    class="lg:px-8"
                >
                    {{ tab }}
                </tab-button>
                <div class="flex items-center mb-3 ml-auto space-x-3">
                    <div class="font-semibold text-splashr-indigo-deep">
                        Rich content
                    </div>
                    <counter-button
                        :counter="richContent.count"
                        :class="{
                            'pointer-events-none opacity-30':
                                richContent.isLoading
                        }"
                        @increment="toggleRichContent($event, true)"
                        @decrement="toggleRichContent($event, false)"
                    ></counter-button>
                </div>
            </nav>
            <!-- tab details -->
            <div class="min-h-screen px-6 pt-5 pb-8 bg-gray-200">
                <keep-alive>
                    <div v-if="currentTab == 'Brief'">
                        <div
                            class="max-w-6xl p-5 mb-5 bg-white border rounded-md"
                        >
                            <div class="flex items-center mb-2 space-x-4">
                                <div
                                    class="font-semibold text-splashr-violet-deep"
                                >
                                    Manager notes
                                    <span class="text-gray-500">
                                        (internal use)
                                    </span>
                                </div>
                                <loading-icon
                                    v-if="managerNotes.isLoading"
                                    class="w-4 h-4"
                                ></loading-icon>
                            </div>
                            <textarea
                                v-model="managerNotes.value"
                                rows="5"
                                placeholder="Use this textarea to add any notes you may have on this project"
                                class="block w-full p-0 m-0 text-sm border-none focus:ring-0"
                                @input="updateManagerNotes"
                            ></textarea>
                        </div>
                        <video-summary
                            :edit-url="{
                                general: route('videos.update', {
                                    video: $page.orderDetails.video.id,
                                    step: 'General',
                                    sidebar: this.$page.isSideNavVisible
                                        ? this.$page.isSideNavVisible
                                        : 'true',
                                    edit: true
                                }),
                                assets: route('videos.update', {
                                    video: $page.orderDetails.video.id,
                                    step: 'Assets',
                                    sidebar: this.$page.isSideNavVisible
                                        ? this.$page.isSideNavVisible
                                        : 'true',
                                    edit: true
                                }),
                                details: route('videos.update', {
                                    video: $page.orderDetails.video.id,
                                    step: 'Details',
                                    sidebar: this.$page.isSideNavVisible
                                        ? this.$page.isSideNavVisible
                                        : 'true',
                                    edit: true
                                })
                            }"
                            :video="$page.orderDetails.video"
                            :folders="$page.folders"
                            :session-badge="false"
                            class="max-w-6xl"
                        ></video-summary>
                    </div>
                </keep-alive>

                <keep-alive>
                    <order-documents
                        v-if="currentTab == 'Documents'"
                        :company-id="$page.orderDetails.video.company_id"
                        :order-id="$page.orderDetails.id"
                        :documents="$page.orderDetails.creative_documents"
                        :video-session-type="
                            $page.orderDetails.video.session_type
                        "
                        @document-added="createActivity"
                        @no-price="noPriceForThisClient"
                    ></order-documents>
                </keep-alive>

                <keep-alive>
                    <div v-if="currentTab == 'Videos'">
                        <div v-if="frameIoVideos.isLoading" class="pl-6">
                            Loading . . .
                        </div>
                        <order-videos
                            :videos="frameIoVideos.value"
                            @toggle-type="toggleVideoType"
                        ></order-videos>
                    </div>
                </keep-alive>

                <keep-alive>
                    <div v-if="currentTab == 'Assets'">
                        <div v-if="!isAssetsAvailable" class="pl-6">
                            Loading . . .
                        </div>
                        <google-picker-body
                            v-show="isAssetsAvailable"
                            :parent-folder-id="$page.folders.assetsFolderId"
                            :dialog-mode="false"
                            :tab-names="[
                                'Project Assets',
                                'Upload',
                                'Assets Library'
                            ]"
                            :is-client-assets-tab="true"
                            :client-assets-parent-folder-id="
                                $page.orderDetails.video.company
                                    .g_media_folder_id
                            "
                            :is-picker-loading.sync="isPickerLoading"
                            @picker-loaded="isAssetsAvailable = true"
                            @close-picker="currentTab = 'Brief'"
                            @picker-files="onAddFilesFromMediaLibrary"
                        ></google-picker-body>
                    </div>
                </keep-alive>
            </div>
        </div>

        <portal to="modal">
            <!-- side panel -->
            <side-panel
                v-if="isDetailsPanel"
                @on-close="isDetailsPanel = false"
            >
                <template #header>
                    <div class="mb-2">
                        {{ $page.orderDetails.video.name }}
                    </div>
                    <session-badge
                        v-if="$page.orderDetails.video.session_type"
                        :session-type="$page.orderDetails.video.session_type"
                    ></session-badge>
                </template>

                <template #tabs>
                    <nav class="flex px-5">
                        <tab-button
                            v-for="tab in sidePanelTabs"
                            :key="tab"
                            :is-selected="currentSideTab == tab"
                            @on-selected="selectSidePanelTab(tab)"
                            class="flex-1"
                        >
                            {{ tab }}
                        </tab-button>
                    </nav>
                </template>

                <template>
                    <keep-alive>
                        <activities
                            v-if="currentSideTab == 'Activities'"
                            :key="currentSideTab"
                            :order-id="$page.orderDetails.id"
                            :tab-key="tabKey"
                            class="pb-24"
                        ></activities>
                    </keep-alive>
                    <keep-alive>
                        <todo
                            v-if="currentSideTab == 'Tasks'"
                            :key="currentSideTab"
                            :order-id="$page.orderDetails.id"
                            :tab-key="tabKey"
                            class="pb-24"
                        ></todo>
                    </keep-alive>
                    <keep-alive>
                        <order-comments
                            v-if="currentSideTab == 'Comments'"
                            :key="currentSideTab"
                            :order-id="$page.orderDetails.id"
                            :tab-key="tabKey"
                            class="pb-24"
                            @mark-comment-as-seen="markCommentAsSeen"
                        ></order-comments>
                    </keep-alive>
                </template>
            </side-panel>

            <!-- account manager popup -->
            <search-manager-popup
                v-if="popupManager.isShow"
                @on-selected="managerSelected"
                @on-close="popupManager.isShow = false"
            ></search-manager-popup>

            <dialog-confirm
                :show="dialogConfirm.isShow"
                @on-close="dialogConfirm.isShow = false"
                @on-remove="removeManagerFromProfile"
            ></dialog-confirm>
        </portal>
    </dashboard-admin-layout>
</template>

<script>
import DashboardAdminLayout from '@/Layouts/DashboardAdminLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import BellNotification from '@/Components/Navbar/BellNotification'
import CommentsButton from '@/Components/Navbar/CommentsButton'
import TasksButton from '@/Components/Navbar/TasksButton'
import SessionBadge from '@/Components/VideoSummary/SessionBadge'
import ProfileStack from '@/Components/ProfileStack'
import TabButton from '@/Components/Tab/TabButton'
import CounterButton from '@/Components/CounterButton'
import DialogConfirm from '@/Components/DialogConfirm'
import JetAvatar from '@/Jetstream/Avatar'
import GoogleDriveIcon from '@/Components/Icons/GoogleDriveIcon'
import FrameIoIcon from '@/Components/Icons/FrameIoIcon'
import PlusIcon from '@/Components/Icons/PlusIcon'
import LoadingIcon from '@/Components/Icons/LoadingIcon'
import UpdateOrderMutation from '@/Graphql/Order/Mutations/UpdateOrder.gql'
import CreateActivityMutation from '@/Graphql/Order/Mutations/CreateActivity.gql'
import ToggleRichContentMutation from '@/Graphql/Order/Mutations/ToggleRichContent.gql'
import ToggleVideoItemTypeMutation from '@/Graphql/Video/Mutations/ToggleVideoItemType.gql'
import IsPriceAvailable from '@/Mixins/IsPriceAvailable'
import FetchCommitments from '@/Mixins/FetchCommitments'
import FrameIoVideoMixin from '@/Mixins/FrameIoVideo'
import { videoStatus } from '@/Utilities/VideoStatus'
import { videoItemStatus, videoItemType } from '@/Utilities/VideoItems'
import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'
import MakeDriveFileShortcutMutation from '@/Graphql/Google/Mutations/MakeShortcut.gql'

export default {
    mixins: [FetchCommitments, IsPriceAvailable, FrameIoVideoMixin],

    components: {
        DashboardAdminLayout,
        TopHeader,
        BellNotification,
        CommentsButton,
        TasksButton,
        SessionBadge,
        ProfileStack,
        JetAvatar,
        TabButton,
        GoogleDriveIcon,
        FrameIoIcon,
        PlusIcon,
        LoadingIcon,
        CounterButton,
        VideoSummary: () =>
            import(
                /* webpackChunkName: 'VideoSummary' */ '@/Components/VideoSummary/VideoSummary'
            ),
        OrderDocuments: () =>
            import(
                /* webpackChunkName: 'OrderDocuments' */ '@/Components/Order/OrderDocuments'
            ),
        OrderVideos: () =>
            import(
                /* webpackChunkName: 'OrderVideos' */ '@/Components/Order/OrderVideos'
            ),
        SidePanel: () =>
            import(
                /* webpackChunkName: 'SidePanel' */ '@/Components/SidePanel'
            ),
        Todo: () =>
            import(/* webpackChunkName: 'Todo' */ '@/Components/Todo/Todo'),
        OrderComments: () =>
            import(
                /* webpackChunkName: 'OrderComment' */ '@/Components/OrderComments/OrderComments'
            ),
        Activities: () =>
            import(
                /* webpackChunkName: 'Activities' */ '@/Components/Activities/Activities'
            ),
        GooglePickerBody: () =>
            import(
                /* webpackChunkName: 'GooglePickerBody' */ '@/Components/MediaLibrary/GooglePickerBody'
            ),
        SearchManagerPopup: () =>
            import(
                /* webpackChunkName: 'SearchManagerPopup' */ '@/Components/SearchManagerPopup'
            ),
        DialogConfirm
    },

    data() {
        return {
            isAssetsAvailable: false,
            isPickerLoading: false,
            tabs: ['Brief', 'Documents', 'Videos', 'Assets'],
            currentTab: 'Brief', // make it 'Brief' by default
            isDetailsPanel: false,
            tabKey: 1,
            sidePanelTabs: ['Comments', 'Activities', 'Tasks'],
            currentSideTab: 'Comments', // make it Comments by default
            isCommentsNotificationDot: true,
            popupManager: {
                isShow: false,
                managers: [],
                currentProfile: null
            },
            dialogConfirm: {
                isShow: false
            },
            inputFrameIoProjectId: this.$page.orderDetails.frameio_project_id,
            isLoadingInputFrameIoProjectId: false,
            inputFrameIoAddMode: this.$page.orderDetails.frameio_project_id
                ? false
                : true,
            isToast: {
                show: false,
                role: 'success',
                message: ''
            },
            richContent: {
                count: this.$page.orderDetails.rich_contents_count,
                isLoading: false
            },
            managerNotes: {
                value: this.$page.orderDetails.video.manager_notes,
                isLoading: false
            }
        }
    },

    computed: {
        hasCommentsUnanswered() {
            if (this.$page.orderDetails.recent_commenters) {
                const commenters = JSON.parse(
                    this.$page.orderDetails.recent_commenters
                )
                if (commenters.includes(this.$page.user.id)) {
                    return false
                }
                return true
            }
            return false
        }
    },

    watch: {
        currentTab() {
            if (this.isAssetsAvailable) {
                this.isAssetsAvailable = false
            }
        },

        inputFrameIoProjectId(value) {
            if (value) {
                this.inputFrameIoAddMode = true
            }
        }
    },

    mounted() {
        if (this.inputFrameIoProjectId) {
            // @mixin method: FrameIoVideoMixin
            this.getFrameIoVideosByProject(
                this.inputFrameIoProjectId,
                this.$page.orderDetails.video.company_id,
                this.$page.orderDetails.video_id,
                this.$page.orderDetails.id
            )
        }
    },

    methods: {
        openDetailsPanel(tab) {
            this.currentSideTab = tab
            this.isDetailsPanel = true
        },

        selectSidePanelTab(tab) {
            ++this.tabKey
            this.currentSideTab = tab
        },

        async onAddFilesFromMediaLibrary({ files }) {
            console.log(files)
            // submit
            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: MakeDriveFileShortcutMutation,
                    // Parameters
                    variables: {
                        assets: {
                            video: {
                                id: this.$page.orderDetails.video.id
                            },
                            assets: {
                                media: files.map(file => {
                                    return {
                                        id: file.id,
                                        isParentFolder: true
                                    }
                                })
                            }
                        }
                    }
                })

                console.log(response)
            } catch (error) {
                console.log(error)
                alert('Something went wrong!')
            }
        },

        markCommentAsSeen() {
            this.isCommentsNotificationDot = false
        },

        onAddPeopleInProfile(extra) {
            // show the search popup
            this.popupManager.isShow = true
            // set values
            this.popupManager.managers = extra.managers
        },

        onRemovePeopleFromProfile(extra) {
            // open the confirmation dialog
            this.dialogConfirm.isShow = true
            // set the values
            this.popupManager.managers = extra.managers
            // set current profile id
            this.popupManager.currentProfile = extra.profile
        },

        async managerSelected(manager) {
            // sync the managers
            if (!manager) {
                return
            }

            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateOrderMutation,
                    // Parameters
                    variables: {
                        id: this.$page.orderDetails.id,
                        order: {
                            video_id: this.$page.orderDetails.video_id,
                            managers: {
                                sync: [
                                    ...this.popupManager.managers.map(
                                        preManagers => preManagers.id
                                    ),
                                    manager.id
                                ]
                            }
                        }
                    }
                })
                // push the manager value
                if (
                    !this.$page.orderDetails.managers.some(
                        preManager => preManager.id === manager.id
                    )
                ) {
                    this.$page.orderDetails.managers.push(manager)
                }

                // close the popup
                this.popupManager.isShow = false
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        async removeManagerFromProfile() {
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateOrderMutation,
                    // Parameters
                    variables: {
                        id: this.$page.orderDetails.id,
                        order: {
                            video_id: this.$page.orderDetails.video_id,
                            managers: {
                                disconnect: [
                                    this.popupManager.currentProfile.id
                                ]
                            }
                        }
                    }
                })
                // remove from the managers list
                if (
                    this.$page.orderDetails.managers.some(
                        preManager =>
                            preManager.id ===
                            this.popupManager.currentProfile.id
                    )
                ) {
                    this.$page.orderDetails.managers = this.$page.orderDetails.managers.filter(
                        manager =>
                            manager.id !== this.popupManager.currentProfile.id
                    )
                }
                // close the confirmation dialog
                this.dialogConfirm.isShow = false
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        async addFrameIoProjectId() {
            if (!this.inputFrameIoProjectId) {
                return
            }

            if (!this.inputFrameIoAddMode) {
                this.inputFrameIoProjectId = ''
                this.inputFrameIoAddMode = true
                return
            }

            this.isLoadingInputFrameIoProjectId = true
            try {
                // @mixin method: FetchCommitments
                let response = await this.fetchCommitments(
                    this.$page.orderDetails.video.company_id
                )

                if (response.data.commitment) {
                    // @mixin method: IsPriceAvailable
                    if (
                        !this.isPriceAvailable(
                            response.data.commitment,
                            this.$page.orderDetails.video.session_type
                        )
                    ) {
                        this.noPriceForThisClient()
                        return
                    }
                } else {
                    this.noPriceForThisClient()
                    return
                }

                // extract path from frame.io url
                this.inputFrameIoProjectId = this.getFrameIoAssetPath(
                    this.inputFrameIoProjectId
                )

                if (!this.inputFrameIoProjectId) {
                    return
                }

                // update order
                response = await this.$apollo.mutate({
                    mutation: UpdateOrderMutation,
                    variables: {
                        id: this.$page.orderDetails.id,
                        order: {
                            video_id: this.$page.orderDetails.video.id,
                            frameio_project_id: this.inputFrameIoProjectId
                        }
                    }
                })

                if (response.data.updateOrder) {
                    this.inputFrameIoAddMode = false
                    // show a toast
                    this.isToast.message = 'Frame.io project id has been added!'
                    this.isToast.role = 'success'
                    this.isToast.show = true

                    if (
                        this.$page.orderDetails.video.status !==
                        videoStatus.video
                    ) {
                        this.updateVideoStatus()
                    }

                    // @mixin method: FrameIoVideoMixin
                    this.getFrameIoVideosByProject(
                        this.inputFrameIoProjectId,
                        this.$page.orderDetails.video.company_id,
                        this.$page.orderDetails.video_id,
                        this.$page.orderDetails.id
                    )
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isLoadingInputFrameIoProjectId = false
            }
        },

        getFrameIoAssetPath(url) {
            const pathArr = url.split('projects/')
            return pathArr.pop()
        },

        async updateVideoStatus() {
            try {
                await this.$apollo.mutate({
                    mutation: UpdateVideoMutation,
                    variables: {
                        id: this.$page.orderDetails.video.id,
                        video: {
                            status: videoStatus.video
                        }
                    }
                })
            } catch (error) {
                console.error(error)
            }
        },

        async createActivity(description) {
            await this.$apollo.mutate({
                mutation: CreateActivityMutation,
                variables: {
                    activity: {
                        description: description,
                        order_id: this.$page.orderDetails.id,
                        user_id: this.$page.user.id
                    }
                }
            })
        },

        async toggleVideoType({ video, isChecked }) {
            console.log(video)
            try {
                const response = await this.$apollo.mutate({
                    mutation: ToggleVideoItemTypeMutation,
                    variables: {
                        videoItem: {
                            id: video.id,
                            company_id: this.$page.orderDetails.video
                                .company_id,
                            video_id: this.$page.orderDetails.video_id,
                            order_id: this.$page.orderDetails.id,
                            name: video.name,
                            type: isChecked
                                ? videoItemType.iteration
                                : videoItemType.master,
                            status: videoItemStatus[video.label]
                        }
                    }
                })

                if (response.data.toggleVideoItemType) {
                    this.frameIoVideos.value.find(frameIoVideo => {
                        if (frameIoVideo.id === video.id) {
                            frameIoVideo.video_item_type = isChecked
                                ? videoItemType.iteration
                                : videoItemType.master
                        }
                    })
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            }
        },

        async toggleRichContent(_, isIncrement) {
            let variables = {
                isIncrement: isIncrement,
                videoSessionType: this.$page.orderDetails.video.session_type,
                videoItem: {
                    company_id: this.$page.orderDetails.video.company_id,
                    video_id: this.$page.orderDetails.video_id,
                    order_id: this.$page.orderDetails.id,
                    name:
                        'Rich content of ' + this.$page.orderDetails.video.name,
                    type: videoItemType.richContent,
                    status: videoItemStatus.approved
                }
            }

            this.richContent.isLoading = true

            try {
                const response = await this.$apollo.mutate({
                    mutation: ToggleRichContentMutation,
                    variables: variables
                })

                if (response.data.toggleRichContent) {
                    if (isIncrement) {
                        ++this.richContent.count
                    } else {
                        if (this.richContent.count == 0) {
                            return
                        }
                        --this.richContent.count
                    }
                }
            } catch (error) {
                console.error(error)
            } finally {
                this.richContent.isLoading = false
            }
        },

        updateManagerNotes() {
            this.debouncedUpdateManagerNotes()
        },

        debouncedUpdateManagerNotes: window._.debounce(function() {
            this.syncManagerNotes()
        }, 1500),

        async syncManagerNotes() {
            if (!this.managerNotes.value) {
                return
            }

            try {
                this.managerNotes.isLoading = true
                await this.$apollo.mutate({
                    mutation: UpdateVideoMutation,
                    variables: {
                        id: this.$page.orderDetails.video.id,
                        video: {
                            manager_notes: this.managerNotes.value
                        }
                    }
                })
            } catch (error) {
                console.error(error)
            } finally {
                this.managerNotes.isLoading = false
            }
        },

        noPriceForThisClient() {
            this.isToast.message = 'No price has been defined for this client'
            this.isToast.role = 'danger'
            this.isToast.show = true
        }
    }
}
</script>

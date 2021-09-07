<template>
    <dashboard-layout>
        <top-header
            back-nav
            :back-nav-route="
                $page.isSideNavVisible
                    ? route('admin.clients.profile', {
                          client: this.$page.user.company_id
                      })
                    : route('dashboard')
            "
        >
            <div></div>
        </top-header>
        <div class="px-6 pt-0 pb-6 mb-10">
            <form @submit.prevent="" class="flex flex-col py-4 space-y-8">
                <!-- video title -->
                <hover-input v-model="form.videoTitle"></hover-input>

                <!-- stepper section -->
                <stepper
                    :active="active"
                    :steps="$page.videoSteps"
                    :class="{
                        'opacity-50 pointer-events-none': !isGDriveFolderCreated
                    }"
                ></stepper>

                <slot></slot>

                <div class="flex space-x-4">
                    <jet-secondary-button
                        @click.native="goPreviousOrCancel"
                        :class="{
                            'opacity-30 pointer-events-none': form.isLoading
                        }"
                    >
                        {{ active == 1 ? 'Cancel' : 'Previous' }}
                    </jet-secondary-button>
                    <jet-secondary-button
                        @click.native="saveAndClose"
                        :class="{
                            'opacity-30 pointer-events-none': form.isLoading
                        }"
                    >
                        Save &amp; Close
                    </jet-secondary-button>
                    <jet-button
                        v-if="active != $page.videoSteps.length"
                        @click.native="goNext"
                        :class="{
                            'opacity-30 pointer-events-none': form.isLoading
                        }"
                    >
                        Next
                    </jet-button>
                    <jet-button
                        v-if="
                            !$page.isEdit && active == $page.videoSteps.length
                        "
                        @click.native="createOrder"
                        :class="{
                            'opacity-30 pointer-events-none': form.isLoading
                        }"
                    >
                        Place Order
                    </jet-button>
                </div>
            </form>
        </div>
        <!-- toast alert -->
        <toast v-if="isToast.show" role="danger" @close="isToast.show = false">
            {{ isToast.message }}
        </toast>
    </dashboard-layout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout'
import TopHeader from '@/Components/Navbar/TopHeader'
import HoverInput from '@/Components/HoverInput'
import Stepper from '@/Components/Stepper/Stepper'
import JetButton from '@/Jetstream/Button'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import { activityDescription } from '@/Utilities/Activity'
import GoogleDriveService from '@/Services/GoogleDriveService'
import CreateOrderMutation from '@/Graphql/Order/Mutations/CreateOrder.gql'
import CreateActivityMutation from '@/Graphql/Order/Mutations/CreateActivity.gql'

export default {
    components: {
        DashboardLayout,
        TopHeader,
        HoverInput,
        Stepper,
        JetButton,
        JetSecondaryButton
    },

    props: {
        active: {
            type: Number,
            required: false,
            default: 1
        }
    },

    data() {
        return {
            form: {
                videoTitle: this.$page.video.name,
                isLoading: false
            },
            isGDriveFolderCreated: true,
            isToast: {
                show: false,
                message: ''
            },
            isProjectCreatedCheckInterval: null
        }
    },

    mounted() {
        if (this.active == 1) {
            this.isGDriveFolderCreated = false

            this.isProjectCreatedCheckInterval = setInterval(() => {
                this.isProjectFoldersCreated()
            }, 3000)
        }
    },

    methods: {
        async isProjectFoldersCreated() {
            try {
                const response = await GoogleDriveService.isProjectFoldersCreated(
                    this.$page.video.id
                )

                if (response.data.isCreated) {
                    this.isGDriveFolderCreated = true
                    clearInterval(this.isProjectCreatedCheckInterval)
                }
            } catch (error) {
                console.log(error)
            }
        },

        goPreviousOrCancel() {
            if (this.active == 1) {
                // cancel
                // eslint-disable-next-line no-undef
                this.$inertia.replace(route('dashboard.client'))
                return
            }

            // update the current page's data
            this.submitData(this.active - 2)
        },

        goNext() {
            if (!this.isGDriveFolderCreated) {
                this.isToast.message =
                    'We are setting up your video, please wait!'
                this.isToast.show = true
                return
            }

            // update the current page's data
            this.submitData(this.active)
        },

        saveAndClose() {
            this.submitData(-1)
        },

        submitData(pageNumberToGo) {
            // start the loading state
            this.form.isLoading = true
            setTimeout(() => {
                this.form.isLoading = false
            }, 5000)

            // submit
            this.$emit('submit', {
                videoName: this.form.videoTitle,
                pageNumberToGo: pageNumberToGo
            })
        },

        async createOrder() {
            try {
                this.form.isLoading = true
                // create order
                const response = await this.$apollo.mutate({
                    mutation: CreateOrderMutation,
                    variables: {
                        order: {
                            video_id: this.$page.video.id
                        }
                    }
                })

                this.createActivity(response.data.createOrder)

                // redirect
                // eslint-disable-next-line no-undef
                this.$inertia.replace(route('dashboard.client'))
                this.form.isLoading = false
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        async createActivity(order) {
            await this.$apollo.mutate({
                mutation: CreateActivityMutation,
                variables: {
                    activity: {
                        description: activityDescription.validatedBrief,
                        order_id: order.id,
                        user_id: this.$page.user.id
                    }
                }
            })
        }
    }
}
</script>

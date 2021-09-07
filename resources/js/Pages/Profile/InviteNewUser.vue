<template>
    <jet-action-section>
        <template #title>
            Invite a user to your account
        </template>

        <template #description>
            This user will be sharing your company's account and will be able to
            create and edit orders
        </template>

        <template #content>
            <div class="flex items-end max-w-xl space-x-5 text-gray-600">
                <!-- Email -->
                <div class="flex-1">
                    <jet-label for="email" value="Email" />
                    <jet-input
                        id="email-invite"
                        type="email"
                        class="block w-full mt-1"
                        v-model="form.email"
                    />
                </div>
                <jet-secondary-button
                    class="py-3"
                    type="button"
                    @click.native.prevent="sendInvite"
                    :class="{
                        'opacity-30 pointer-events-none': isInvitationLoading
                    }"
                >
                    send invite
                </jet-secondary-button>
            </div>

            <div v-if="invitations.length" class="max-w-md mt-5">
                <h1 class="pb-1 mb-1 font-bold border-b">Invited Users</h1>
                <div class="flex flex-col text-gray-600 divide-y">
                    <div
                        v-for="invitation in invitations"
                        :key="invitation.key"
                        class="flex items-center justify-between py-1 group"
                    >
                        <div>{{ invitation.invitee_email }}</div>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="w-6 h-6 transition opacity-0 cursor-pointer group-hover:opacity-100 group-hover:text-red-400"
                            @click="removeFromInvitations(invitation.id)"
                            :class="{
                                'opacity-30 pointer-events-none':
                                    invitation.isLoading
                            }"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </template>
    </jet-action-section>
</template>

<script>
import JetActionSection from '@/Jetstream/ActionSection'
import JetLabel from '@/Jetstream/Label'
import JetInput from '@/Jetstream/Input'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import GetInvitedUsersQuery from '@/Graphql/User/Queries/GetInvitedUsers.gql'
import InviteNewUserMutation from '@/Graphql/User/Mutations/InviteNewUser.gql'
import DeleteInvitationMutation from '@/Graphql/User/Mutations/DeleteInvitation.gql'
import { userType } from '@/Utilities/User'

export default {
    components: {
        JetActionSection,
        JetLabel,
        JetInput,
        JetSecondaryButton
    },

    data() {
        return {
            invitations: [],
            form: {
                email: ''
            },
            isInvitationLoading: false
        }
    },

    mounted() {
        // fetch invited users data
        this.fetchInvitedUsers()
    },

    methods: {
        async fetchInvitedUsers() {
            try {
                const response = await this.$apollo.query({
                    query: GetInvitedUsersQuery,
                    variables: {
                        inviter_id: this.$page.user.id
                    }
                })

                if (response.data) {
                    this.invitations = response.data.invitationsByInviter.map(
                        invitation => {
                            return {
                                ...invitation,
                                isLoading: false
                            }
                        }
                    )
                }
            } catch (error) {
                console.log(error)
            }
        },

        async sendInvite() {
            if (!this.form.email) {
                return
            }

            // submit
            try {
                this.isInvitationLoading = true
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: InviteNewUserMutation,
                    // Parameters
                    variables: {
                        data: {
                            invitee_email: this.form.email.trim(),
                            inviter_id: this.$page.user.id,
                            metadata: {
                                inviter: {
                                    id: this.$page.user.id,
                                    email: this.$page.user.email,
                                    company_id: this.$page.user.company_id,
                                    type: userType.client
                                }
                            }
                        }
                    }
                })

                // clear the input
                this.form.email = ''

                // push
                this.invitations.unshift({
                    ...response.data.sendUserRegistrationInvite,
                    isLoading: false
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isInvitationLoading = false
            }
        },

        async removeFromInvitations(invitationId) {
            try {
                this.loadProgressOfAInvitation(invitationId, true)

                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: DeleteInvitationMutation,
                    // Parameters
                    variables: {
                        id: invitationId
                    }
                })

                if (response.data) {
                    // remove from invitations
                    this.invitations = this.invitations
                        .filter(invitation => invitation.id !== invitationId)
                        .map(invitation => {
                            return {
                                ...invitation,
                                isLoading: false
                            }
                        })
                }
            } catch (error) {
                console.log(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.loadProgressOfAInvitation(invitationId, false)
            }
        },

        loadProgressOfAInvitation(invitationId, isLoading) {
            this.invitations.find(invitation => {
                if (invitation.id === invitationId) {
                    invitation.isLoading = isLoading
                }
            })
        }
    }
}
</script>

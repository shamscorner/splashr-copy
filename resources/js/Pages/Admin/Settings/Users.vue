<template>
    <dashboard-admin-layout>
        <div class="px-8 py-4 mb-10 max-w-screen-2xl">
            <!-- create user section -->
            <div class="flex justify-end mt-3 mb-5">
                <div class="relative">
                    <!-- action -->
                    <button
                        class="z-20 flex items-center px-4 py-2 space-x-3 font-bold uppercase transition border-2 rounded text-md border-splashr-violet-deep text-splashr-violet-deep hover:bg-purple-100 focus:ring focus:ring-offset-splashr-violet-light"
                        @click.stop="
                            isInputPanelForCreateUser = !isInputPanelForCreateUser
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="w-8 h-8"
                        >
                            <path
                                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                            />
                        </svg>
                        <div>create user</div>
                    </button>

                    <!-- input panel -->
                    <div
                        v-show="isInputPanelForCreateUser"
                        class="absolute right-0 z-20 p-1.5 mt-2 border-2 border-gray-300 rounded shadow-md bg-gray-50"
                        id="inputUserPanel"
                    >
                        <div class="flex space-x-2">
                            <input
                                v-model="form.newUserEmail"
                                type="text"
                                placeholder="Email"
                                class="placeholder-gray-400 border-gray-300 rounded"
                                :class="{
                                    'opacity-30 pointer-events-none':
                                        form.isLoading
                                }"
                            />
                            <select
                                v-model="form.newUserRole"
                                class="border-gray-300 rounded"
                                :class="{
                                    'opacity-30 pointer-events-none':
                                        form.isLoading
                                }"
                            >
                                <option :value="managerRole.manager">
                                    Manager
                                </option>
                                <option :value="managerRole.admin">
                                    Admin
                                </option>
                                <option :value="managerRole.teamLead">
                                    Team Lead
                                </option>
                            </select>
                        </div>
                        <button
                            class="w-full px-4 py-2 mt-2 text-white uppercase bg-purple-900 rounded text-md hover:bg-splashr-violet-deep focus:outline-none focus:ring"
                            @click="sendInvite"
                            :class="{
                                'opacity-30 pointer-events-none': form.isLoading
                            }"
                        >
                            send invite
                        </button>
                    </div>
                </div>
            </div>

            <!-- data table -->
            <table-custom :headers="['Name', 'Email', 'Role', '']">
                <table-row-custom
                    v-for="manager in managersData"
                    :key="manager.id"
                >
                    <table-data-custom class="px-4 py-3 ring-1 ring-white">
                        <div
                            v-if="manager.user"
                            class="flex items-center space-x-5"
                        >
                            <jet-avatar
                                :src="manager.user.profile_photo_url"
                                :alt="
                                    `${manager.user.first_name} ${manager.user.last_name}`
                                "
                                :name="
                                    `${manager.user.first_name} ${manager.user.last_name}`
                                "
                                class="w-10 h-10 font-semibold"
                            ></jet-avatar>
                            <div>
                                {{
                                    manager.user.first_name +
                                        ' ' +
                                        manager.user.last_name
                                }}
                            </div>
                        </div>
                        <div
                            v-else
                            class="inline-block px-3 py-1 text-xs text-white uppercase rounded-md bg-splashr-blue-light"
                        >
                            Pending
                        </div>
                    </table-data-custom>
                    <table-data-custom class="px-4 py-3 ring-1 ring-white">
                        {{
                            manager.user
                                ? manager.user.email
                                : manager.invitee_email
                        }}
                    </table-data-custom>
                    <table-data-custom class="px-4 py-3 ring-1 ring-white">
                        <select
                            v-model="manager.role"
                            class="bg-transparent border-none rounded"
                            @change="
                                changeRoleOfUser(manager, $event.target.value)
                            "
                        >
                            <option :value="managerRole.manager">
                                Manager
                            </option>
                            <option :value="managerRole.admin">Admin</option>
                            <option :value="managerRole.teamLead">
                                Team Lead
                            </option>
                        </select>
                    </table-data-custom>
                    <table-data-custom class="px-4 py-3 ring-1 ring-white">
                        {{ manager.created_at | diffForHumansTime }}
                    </table-data-custom>
                </table-row-custom>
            </table-custom>
        </div>
    </dashboard-admin-layout>
</template>

<script>
import DashboardAdminLayout from '@/Layouts/DashboardAdminLayout'
import TableCustom from '@/Components/Table/TableCustom'
import TableRowCustom from '@/Components/Table/TableRowCustom'
import TableDataCustom from '@/Components/Table/TableDataCustom'
import JetAvatar from '@/Jetstream/Avatar'
import { diffForHumansTime } from '@/Utilities/RelativeTime'
import { userType, managerRole } from '@/Utilities/User'
import InviteNewUserMutation from '@/Graphql/User/Mutations/InviteNewUser.gql'
import UpdateManagerMutation from '@/Graphql/Manager/Mutations/UpdateManager.gql'
import UpdateInvitationMutation from '@/Graphql/User/Mutations/UpdateInvitation.gql'

export default {
    components: {
        DashboardAdminLayout,
        TableCustom,
        TableRowCustom,
        TableDataCustom,
        JetAvatar
    },

    data() {
        return {
            isInputPanelForCreateUser: false,
            managerRole,
            managersData: this.$page.managers.map(manager => {
                if (manager.role) {
                    return {
                        ...manager,
                        role: manager.role
                    }
                }

                const metadata = JSON.parse(manager.metadata)
                return {
                    ...manager,
                    metadata: metadata,
                    role: metadata.manager_type
                }
            }),
            form: {
                newUserEmail: '',
                newUserRole: managerRole.manager,
                isLoading: false
            }
        }
    },

    filters: {
        diffForHumansTime(timeStamp) {
            if (!timeStamp) {
                return '7 days'
            }
            return diffForHumansTime(timeStamp)
        }
    },

    created() {
        let self = this
        window.addEventListener('click', function(e) {
            // close dropdown when clicked outside
            const elem = document.getElementById('inputUserPanel')
            if (elem && !elem.contains(e.target)) {
                self.isInputPanelForCreateUser = false
            }
        })
    },

    methods: {
        async sendInvite() {
            if (!this.form.newUserEmail) {
                return
            }

            // submit
            try {
                this.form.isLoading = true
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: InviteNewUserMutation,
                    // Parameters
                    variables: {
                        data: {
                            invitee_email: this.form.newUserEmail.trim(),
                            inviter_id: this.$page.user.id,
                            metadata: {
                                inviter: {
                                    id: this.$page.user.id,
                                    email: this.$page.user.email,
                                    company_id: this.$page.user.company_id,
                                    type: userType.manager
                                },
                                manager_type: this.form.newUserRole
                            }
                        }
                    }
                })

                // invitation response data
                const invitation = response.data.sendUserRegistrationInvite

                // push
                if (invitation) {
                    this.managersData.unshift({
                        ...invitation,
                        metadata: JSON.parse(invitation.metadata),
                        role: this.form.newUserRole
                    })

                    // clear the input
                    this.form.newUserEmail = ''
                    this.form.newUserRole = managerRole.manager
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.form.isLoading = false
                this.isInputPanelForCreateUser = false
            }
        },

        async changeRoleOfUser(managerData, role) {
            // update manager's role by default
            let mutation = UpdateManagerMutation
            let variables = {
                id: managerData.id,
                manager: {
                    role: role
                }
            }

            if (!managerData.user) {
                // update into the invitations
                mutation = UpdateInvitationMutation
                variables = {
                    id: managerData.id,
                    invitation: {
                        metadata: JSON.stringify({
                            inviter: {
                                id: this.$page.user.id,
                                email: this.$page.user.email,
                                company_id: this.$page.user.company_id,
                                type: userType.manager
                            },
                            manager_type: role
                        })
                    }
                }
            }

            // submit
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: mutation,
                    // Parameters
                    variables: variables
                })
            } catch (error) {
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        }
    }
}
</script>

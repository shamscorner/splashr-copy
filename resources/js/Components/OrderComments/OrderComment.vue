<template>
    <div>
        <div class="p-4 mt-3 border rounded-md">
            <div class="flex space-x-4">
                <jet-avatar
                    :src="comment.user.profile_photo_url"
                    :alt="
                        `${comment.user.first_name} ${comment.user.last_name}`
                    "
                    :name="
                        `${comment.user.first_name} ${comment.user.last_name}`
                    "
                    class="w-12 h-12 font-semibold"
                ></jet-avatar>
                <div class="flex-1 ml-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-bold break-words">
                                {{
                                    comment.user.first_name +
                                        ' ' +
                                        comment.user.last_name
                                }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{
                                    getLastUpdatedTime(
                                        comment.created_at,
                                        comment.updated_at
                                    )
                                }}
                            </p>
                            <div class="mt-1 text-sm text-gray-600">
                                {{ comment.text }}
                            </div>
                        </div>
                        <jet-dropdown
                            v-if="comment.user.id === this.$page.user.id"
                            align="right"
                            width="20"
                        >
                            <template #trigger>
                                <button
                                    class="text-gray-400 transition focus:outline-none hover:text-gray-900 focus:ring-1"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                        />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div
                                    class="flex flex-col -mt-4 text-gray-500 border-t border-l border-r divide-y rounded"
                                >
                                    <button
                                        class="px-2 py-1 text-left hover:text-gray-800 focus:outline-none"
                                        @click="openEditSection"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="px-2 py-1 text-left hover:text-gray-800 focus:outline-none"
                                        @click="openDeleteSection"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </template>
                        </jet-dropdown>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <button-text
                            @click.native="openReplySection"
                            class="text-sm text-gray-400"
                        >
                            Reply
                        </button-text>
                    </div>
                </div>
            </div>

            <div v-if="isDelete" class="mt-4 text-sm text-gray-600">
                <p class="text-red-600">Do you really want to delete?</p>
                <button-text @click.native="deleteComment">Yes</button-text>
                <button-text @click.native="closeSections">No</button-text>
            </div>

            <div v-if="isInputBoxSection">
                <text-area-custom
                    class="block w-full mt-4"
                    v-model="inputBox.text"
                ></text-area-custom>

                <jet-button
                    class="inline mt-4"
                    :class="{ 'opacity-25': inputBox.processing }"
                    type="button"
                    :disabled="inputBox.processing"
                    @click.native.prevent="performEditOrReplyToComment"
                >
                    {{ isEdit ? 'Edit' : 'Reply' }}
                </jet-button>

                <button-text class="ml-4" @click.native="closeSections">
                    Cancel
                </button-text>
            </div>

            <div>
                <error-message :input-box="inputBox"></error-message>
            </div>
        </div>

        <div class="ml-5">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import TimeStampManagementMixin from '@/Mixins/TimeStampManagement'

import ButtonText from '@/Components/ButtonText'
import TextAreaCustom from '@/Components/TextAreaCustom'
import JetButton from '@/Jetstream/Button'
import JetAvatar from '@/Jetstream/Avatar'
import ErrorMessage from '@/Components/ErrorMessage'
import JetDropdown from '@/Jetstream/Dropdown'
import { getTimeFormat } from '@/Utilities/ConvertTimeFormatFromSecond'

import EditOrderCommentMutation from '@/Graphql/Order/Mutations/EditOrderComment.gql'
import DeleteOrderCommentMutation from '@/Graphql/Order/Mutations/DeleteOrderComment.gql'
import CreateOrderCommentMutation from '@/Graphql/Order/Mutations/CreateOrderComment.gql'

export default {
    mixins: [TimeStampManagementMixin],

    components: {
        ButtonText,
        JetButton,
        JetAvatar,
        ErrorMessage,
        TextAreaCustom,
        JetDropdown
    },

    props: {
        comment: {
            type: Object,
            required: true
        },
        orderId: {
            type: String,
            required: true
        },
        parentCommentId: {
            type: String,
            required: false,
            default: null
        }
    },

    data() {
        return {
            isDelete: false,
            isEdit: false,
            isInputBoxSection: false,
            inputBox: {
                text: '',
                processing: false,
                message: {
                    error: false,
                    text: ''
                }
            }
        }
    },

    filters: {
        timeFormat(val) {
            return getTimeFormat(val)
        }
    },

    methods: {
        openReplySection() {
            // close the delete section if open
            this.isDelete = false
            this.isEdit = false
            this.isInputBoxSection = !this.isInputBoxSection
        },

        openEditSection() {
            // close the delete section if open
            this.isDelete = false
            this.isInputBoxSection = !this.isInputBoxSection
            this.isEdit = true
        },

        openDeleteSection() {
            // close the other sections first
            this.closeSections()

            // open the delete section
            this.isDelete = !this.isDelete
        },

        async deleteComment() {
            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: DeleteOrderCommentMutation,
                    // Parameters
                    variables: {
                        id: this.comment.id
                    }
                })

                if (response.data) {
                    // close the delete section
                    this.isDelete = false
                    // remove this comment from comment section
                    this.$emit('delete', response.data.deleteComment)
                } else {
                    this.showErrorMessage()
                }
            } catch (error) {
                console.log(error)
            }
        },

        performEditOrReplyToComment() {
            if (this.inputBox.text == '') {
                this.inputBox.message.text = 'Input text is required'
                this.inputBox.message.error = true
                return
            }

            this.inputBox.processing = true

            if (this.isEdit) {
                // perform edit action
                this.editComment()
                return
            }

            // perform addition action
            this.replyComment()
        },

        async editComment() {
            if (!this.inputBox.text) {
                return
            }

            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: EditOrderCommentMutation,
                    // Parameters
                    variables: {
                        id: this.comment.id,
                        comment: {
                            user_id: this.$page.user.id,
                            order_id: this.orderId,
                            text: this.inputBox.text
                        }
                    }
                })

                if (response.data.editComment) {
                    // close sections
                    this.closeSections()
                    // update this comment in the comment section
                    this.$emit('edit', response.data.editComment)
                } else {
                    this.showErrorMessage()
                }
            } catch (error) {
                console.log(error)
            }
        },

        async replyComment() {
            const commentId = this.comment.comment_id
                ? this.parentCommentId
                : this.comment.id

            try {
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: CreateOrderCommentMutation,
                    // Parameters
                    variables: {
                        comment: {
                            user_id: this.$page.user.id,
                            order_id: this.orderId,
                            comment_id: commentId,
                            seen_by: JSON.stringify([this.$page.user.id]),
                            text: this.inputBox.text
                        }
                    }
                })

                if (response.data) {
                    // close sections
                    this.closeSections()
                    // push this reply in the comment section
                    this.$emit('reply', response.data.addComment)
                } else {
                    this.showErrorMessage()
                }
            } catch (error) {
                console.log(error)
            }
        },

        closeSections() {
            this.isInputBoxSection = false
            this.isEdit = false
            this.isDelete = false
            this.inputBox = {
                text: '',
                processing: false,
                message: {
                    error: false,
                    text: ''
                }
            }
        },

        showErrorMessage() {
            this.inputBox.message.error = true
            this.inputBox.message.text =
                'Oops! Something went wrong! Try again.'
            this.inputBox.processing = false
        }
    }
}
</script>

<template>
    <div>
        <div class="p-2 mt-3 border rounded-md">
            <div class="flex items-start">
                <img
                    :src="getUserInfo.photoLink"
                    width="50"
                    height="50"
                    class="rounded-full"
                />
                <div class="w-full ml-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-bold break-words">
                                {{ getUserInfo.displayName }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{
                                    getLastUpdatedTime(
                                        comment.createdTime,
                                        comment.modifiedTime
                                    )
                                }}
                            </p>
                            <div
                                v-html="comment.htmlContent"
                                class="mt-1 text-sm text-gray-600"
                            ></div>
                        </div>
                        <jet-dropdown
                            v-if="comment.author.me"
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
                        <div
                            v-if="!isReply()"
                            class="ml-auto transition duration-200 justify-self-end"
                            :class="{ 'text-gray-400': !comment.resolved }"
                            @click="resolveComment"
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
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
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
                    id="comment_text"
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

                <button-text class="ml-4" @click.native="closeSections"
                    >Cancel</button-text
                >
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
import ErrorMessage from '@/Components/ErrorMessage'

import GDocApiService from '@/Services/GDocApiService.js'
import JetDropdown from '@/Jetstream/Dropdown'

export default {
    mixins: [TimeStampManagementMixin],

    components: {
        ButtonText,
        JetButton,
        ErrorMessage,
        TextAreaCustom,
        JetDropdown
    },

    props: {
        comment: {
            type: Object,
            required: true
        },
        presentationId: {
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
            },
            isResolveLoading: false
        }
    },

    computed: {
        getUserInfo() {
            if (this.comment.author.me) {
                // return native user
                return {
                    photoLink: this.$page.user.profile_photo_url,
                    displayName:
                        this.$page.user.first_name +
                        ' ' +
                        this.$page.user.last_name
                }
            }
            return {
                photoLink: 'https://' + this.comment.author.photoLink,
                displayName: this.comment.author.displayName
            }
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

        isReply() {
            return this.comment.kind == 'drive#reply'
        },

        async deleteComment() {
            // if this is reply type
            if (this.isReply()) {
                this.deleteReply()
                return
            }

            try {
                const response = await GDocApiService.deleteComment(
                    this.presentationId,
                    this.comment.id
                )

                if (response.data) {
                    // close the delete section
                    this.isDelete = false
                    // remove this comment from comment section
                    this.$emit('delete', this.comment.id)
                } else {
                    this.showErrorMessage()
                }
            } catch (error) {
                console.log(error)
            }
        },

        async deleteReply() {
            try {
                const response = await GDocApiService.deleteReplyFromAComment(
                    this.presentationId,
                    this.parentCommentId,
                    this.comment.id
                )

                if (response.data) {
                    // close the delete section
                    this.isDelete = false
                    // remove this comment from reply section
                    this.$emit('delete-reply', {
                        commentId: this.parentCommentId,
                        replyId: this.comment.id
                    })
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
            // if this is reply type
            if (this.isReply()) {
                this.editReply()
                return
            }

            try {
                const response = await GDocApiService.editComment(
                    this.presentationId,
                    this.comment.id,
                    this.inputBox.text
                )

                if (response.data) {
                    // close sections
                    this.closeSections()
                    // update this comment in the comment section
                    this.$emit('update', response.data)
                } else {
                    this.showErrorMessage()
                }
            } catch (error) {
                console.log(error)
            }
        },

        async editReply() {
            try {
                const response = await GDocApiService.editReplyInAComment(
                    this.presentationId,
                    this.parentCommentId,
                    this.comment.id,
                    this.inputBox.text
                )

                if (response.data) {
                    // close sections
                    this.closeSections()
                    // update this reply in the reply section
                    this.$emit('update-reply', {
                        commentId: this.parentCommentId,
                        reply: response.data
                    })
                } else {
                    this.showErrorMessage()
                }
            } catch (error) {
                console.log(error)
            }
        },

        async resolveComment() {
            this.isResolveLoading = true
            try {
                const response = await GDocApiService.resolveComment(
                    this.presentationId,
                    this.comment.id,
                    {
                        content: this.comment.htmlContent,
                        resolved: !this.comment.resolved
                    }
                )

                if (response.data) {
                    // update this comment in the comment section
                    this.$emit('update', response.data)
                } else {
                    this.showErrorMessage()
                }
                this.isResolveLoading = false
            } catch (error) {
                console.log(error)
            }
        },

        async replyComment() {
            let commentId = this.comment.id

            // if the comment type is reply
            if (this.isReply()) {
                commentId = this.parentCommentId
            }

            try {
                const response = await GDocApiService.createReplyToComment(
                    this.presentationId,
                    commentId,
                    this.inputBox.text
                )

                if (response.data) {
                    // close sections
                    this.closeSections()
                    // push this reply in the comment section
                    this.$emit('reply', {
                        reply: response.data,
                        commentId: commentId
                    })
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

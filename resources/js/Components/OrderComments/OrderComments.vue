<template>
    <div>
        <div class="p-5 text-right">
            <text-area-custom
                v-model="inputBox.text"
                :rows="4"
                class="block w-full"
            ></text-area-custom>
            <jet-button
                type="button"
                class="mt-4"
                :class="{ 'opacity-25': inputBox.processing }"
                :disabled="inputBox.processing"
                @click.native.prevent="addComment"
            >
                Send
            </jet-button>
        </div>
        <div v-if="isCommentsLoading" class="mt-4 text-center text-gray-600">
            Loading . . .
        </div>
        <div v-else-if="commentsByOrder.data.length" class="px-5">
            <order-comment
                v-for="comment in commentsByOrder.data"
                :key="comment.id"
                :comment="comment"
                :order-id="orderId"
                @edit="onCommentEdit"
                @delete="onCommentDelete"
                @reply="onReply"
            >
                <template v-if="comment.comments.length">
                    <order-comment
                        v-for="reply in comment.comments"
                        :key="reply.id"
                        :comment="reply"
                        :order-id="orderId"
                        :parent-comment-id="comment.id"
                        @edit="onReplyEdit"
                        @delete="onReplyDelete"
                        @reply="onReply"
                    >
                    </order-comment>
                </template>
            </order-comment>
        </div>
        <div v-else class="mt-4 text-center text-gray-600">
            No comments found.
        </div>
        <button-text
            v-if="
                !isCommentsLoading &&
                    !!commentsByOrder.paginatorInfo.hasMorePages
            "
            class="block w-full my-5 text-center text-gray-500"
            :class="{
                'text-gray-300 pointer-events-none': isLoadingMoreComments
            }"
            @click.native="fetchComments(true)"
        >
            {{ isLoadingMoreComments ? 'Loading' : 'More comments' }}
        </button-text>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button'
import GetOrderComments from '@/Graphql/Order/Queries/GetOrderComments.gql'
import CreateOrderCommentMutation from '@/Graphql/Order/Mutations/CreateOrderComment.gql'
import MarkCommentAsSeenMutation from '@/Graphql/Order/Mutations/MarkCommentAsSeen.gql'

export default {
    components: {
        OrderComment: () =>
            import(
                /* webpackChunkName: 'OrderComment' */ '@/Components/OrderComments/OrderComment'
            ),
        ButtonText: () =>
            import(
                /* webpackChunkName: 'ButtonText' */ '@/Components/ButtonText'
            ),
        TextAreaCustom: () =>
            import(
                /* webpackChunkName: 'TextAreaCustom' */ '@/Components/TextAreaCustom'
            ),
        JetButton
    },

    props: {
        orderId: {
            type: String,
            required: true
        },
        tabKey: {
            type: Number,
            required: false,
            default: 1
        }
    },

    data() {
        return {
            commentsByOrder: {
                data: [],
                paginatorInfo: {
                    currentPage: 1
                }
            },
            isCommentsLoading: false,
            isLoadingMoreComments: false,
            inputBox: {
                text: '',
                processing: false
            }
        }
    },

    watch: {
        tabKey: {
            immediate: true,
            handler() {
                this.fetchComments(false)
            }
        }
    },

    methods: {
        async fetchComments(isLoadMoreComments) {
            if (!isLoadMoreComments) {
                // show the main loading
                this.isCommentsLoading = true
            } else {
                this.isLoadingMoreComments = true
                ++this.commentsByOrder.paginatorInfo.currentPage
            }

            try {
                const response = await this.$apollo.query({
                    // Query
                    query: GetOrderComments,
                    // Parameters
                    variables: {
                        orderId: this.orderId,
                        page: this.commentsByOrder.paginatorInfo.currentPage
                    },
                    fetchPolicy: 'network-only'
                })

                if (response.data.commentsByOrder) {
                    if (isLoadMoreComments) {
                        this.commentsByOrder.data = this.commentsByOrder.data.concat(
                            response.data.commentsByOrder.data
                        )
                    } else {
                        this.commentsByOrder.data =
                            response.data.commentsByOrder.data
                    }

                    this.commentsByOrder.paginatorInfo =
                        response.data.commentsByOrder.paginatorInfo
                }

                // mark the comments as seen if there are comments
                if (this.commentsByOrder.data.length) {
                    this.markCommentAsSeen()
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isCommentsLoading = false
                this.isLoadingMoreComments = false
            }
        },

        async addComment() {
            if (!this.inputBox.text) {
                return
            }

            // submit
            try {
                this.inputBox.processing = true
                const response = await this.$apollo.mutate({
                    // Mutation
                    mutation: CreateOrderCommentMutation,
                    // Parameters
                    variables: {
                        comment: {
                            user_id: this.$page.user.id,
                            order_id: this.orderId,
                            comment_id: null,
                            seen_by: JSON.stringify([this.$page.user.id]),
                            text: this.inputBox.text
                        }
                    }
                })

                if (response.data.addComment) {
                    this.commentsByOrder.data.unshift(response.data.addComment)
                    // clear the inputs
                    this.inputBox.text = ''
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.inputBox.processing = false
            }
        },

        onCommentEdit(currentComment) {
            this.commentsByOrder.data.find(comment => {
                if (comment.id === currentComment.id) {
                    comment.text = currentComment.text
                    comment.updated_at = currentComment.updated_at
                }
            })
        },

        onCommentDelete(deletedComment) {
            this.commentsByOrder.data = this.commentsByOrder.data.filter(
                comment => comment.id != deletedComment.id
            )
        },

        onReply(reply) {
            this.commentsByOrder.data.find(comment => {
                if (comment.id === reply.comment_id) {
                    comment.comments.push(reply)
                }
            })
        },

        onReplyEdit(currentReply) {
            this.commentsByOrder.data.find(comment => {
                if (comment.id === currentReply.comment_id) {
                    comment.comments.find(innerComment => {
                        if (innerComment.id === currentReply.id) {
                            innerComment.text = currentReply.text
                            innerComment.updated_at = currentReply.updated_at
                        }
                    })
                }
            })
        },

        onReplyDelete(currentReply) {
            console.log(currentReply)
            this.commentsByOrder.data.find(comment => {
                if (comment.id === currentReply.comment_id) {
                    comment.comments = comment.comments.filter(
                        innerComment => innerComment.id !== currentReply.id
                    )
                }
            })
        },

        async markCommentAsSeen() {
            // submit
            try {
                await this.$apollo.mutate({
                    // Mutation
                    mutation: MarkCommentAsSeenMutation,
                    // Parameters
                    variables: {
                        orderId: this.orderId,
                        userId: this.$page.user.id
                    }
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }

            this.$emit('mark-comment-as-seen', this.orderId)
        }
    }
}
</script>

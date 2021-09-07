import UpdateVideoMutation from '@/Graphql/Video/Mutations/UpdateVideo.gql'
import CreateActivityMutation from '@/Graphql/Order/Mutations/CreateActivity.gql'
import { userType } from '@/Utilities/User'

export default {
    methods: {
        onCommentDelete(commentId) {
            this.comments = this.comments.filter(
                comment => comment.id !== commentId
            )
        },

        onCommentUpdate(currentComment) {
            // update the comment with new data
            this.comments.forEach(comment => {
                if (comment.id === currentComment.id) {
                    comment.htmlContent = currentComment.htmlContent
                    comment.modifiedTime = currentComment.modifiedTime
                    comment.resolved = currentComment.resolved
                }
            })

            this.updateVideoStatusAndPendingSide(
                this.$page.videoId,
                this.videoStatus
            )
        },

        onReplyOnComment(data) {
            // push the newly created reply
            this.comments
                .find(comment => comment.id === data.commentId)
                .replies.push(data.reply)

            this.updateVideoStatusAndPendingSide(
                this.$page.videoId,
                this.videoStatus
            )

            this.createActivity(
                // eslint-disable-next-line no-undef
                route().params.order,
                this.activityDescription
            )
        },

        onReplyUpdate(data) {
            // update the reply with new data
            this.comments.forEach(comment => {
                if (comment.id === data.commentId) {
                    comment.replies.forEach(reply => {
                        if (reply.id === data.reply.id) {
                            reply.htmlContent = data.reply.htmlContent
                            reply.modifiedTime = data.reply.modifiedTime
                        }
                    })
                }
            })

            this.updateVideoStatusAndPendingSide(
                this.$page.videoId,
                this.videoStatus
            )
        },

        onReplyDelete(data) {
            this.comments.forEach(comment => {
                if (comment.id === data.commentId) {
                    comment.replies = comment.replies.filter(
                        reply => reply.id !== data.replyId
                    )
                }
            })
        },

        async updateVideoStatusAndPendingSide(videoId, status) {
            try {
                // submit video data
                await this.$apollo.mutate({
                    // Mutation
                    mutation: UpdateVideoMutation,
                    // Parameters
                    variables: {
                        id: videoId,
                        video: {
                            status: status,
                            pending_side: userType.manager
                        }
                    }
                })
            } catch (error) {
                console.log(error)
            }
        },

        async createActivity(orderId, description) {
            await this.$apollo.mutate({
                mutation: CreateActivityMutation,
                variables: {
                    activity: {
                        description: description,
                        order_id: orderId,
                        user_id: this.$page.user.id
                    }
                }
            })
        }
    }
}

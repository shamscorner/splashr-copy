import axios from 'axios'

const apiClient = axios.create({
    baseURL: process.env.MIX_FRONTEND_APP_URL + '/api/v1',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
    }
})

export default {
    getSlideObjectIdOfAPresentation(presentationId) {
        return apiClient.get(
            `/google/slide/presentations/${presentationId}/slides`
        )
    },

    getThumbnailOfASlide(presentationId, slideId) {
        return apiClient.get(
            `/google/slide/presentations/${presentationId}/slides/${slideId}/thumbnail`
        )
    },

    getCommentsOfAPageInAPresentation(
        presentationId,
        pageId,
        nextPageToken = null,
        perPage = 20
    ) {
        return apiClient.get(
            `/google/drive/files/${presentationId}/pages/${pageId}/comments?perPage=${perPage}&nextPageToken=${nextPageToken}`
        )
    },

    getCommentsOfAGoogleDoc(docId, nextPageToken = null, perPage = 20) {
        return apiClient.get(
            `/google/drive/files/${docId}/comments?perPage=${perPage}&nextPageToken=${nextPageToken}`
        )
    },

    createComment(presentationId, pageId, text) {
        return apiClient.post(
            `/google/drive/files/${presentationId}/comments`,
            {
                content: text,
                page: pageId
            }
        )
    },

    editComment(presentationId, commentId, text) {
        return apiClient.patch(
            `/google/drive/files/${presentationId}/comments/${commentId}`,
            {
                content: text
            }
        )
    },

    deleteComment(presentationId, commentId) {
        return apiClient.delete(
            `/google/drive/files/${presentationId}/comments/${commentId}`
        )
    },

    resolveComment(presentationId, commentId, body) {
        return apiClient.patch(
            `/google/drive/files/${presentationId}/comments/${commentId}/resolve`,
            {
                content: body.content,
                resolved: body.resolved
            }
        )
    },

    createReplyToComment(presentationId, commentId, text) {
        return apiClient.post(
            `/google/drive/files/${presentationId}/comments/${commentId}/replies`,
            {
                content: text
            }
        )
    },

    editReplyInAComment(presentationId, commentId, replyId, text) {
        return apiClient.patch(
            `/google/drive/files/${presentationId}/comments/${commentId}/replies/${replyId}`,
            {
                content: text
            }
        )
    },

    deleteReplyFromAComment(presentationId, commentId, replyId) {
        return apiClient.delete(
            `/google/drive/files/${presentationId}/comments/${commentId}/replies/${replyId}`
        )
    }
}

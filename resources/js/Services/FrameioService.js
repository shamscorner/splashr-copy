import axios from 'axios'

const apiClient = axios.create({
    baseURL: 'https://api.frame.io/v2',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${process.env.MIX_FRAME_IO_API_TOKEN}`
    }
})

export default {
    getCurrentAuthenticatedUser() {
        return apiClient.get('/me')
    },

    getAssetDetailsById(assetId) {
        return apiClient.get(`/assets/${assetId}`)
    },

    getProject(projectId) {
        return apiClient.get(`/projects/${projectId}`)
    },

    getAssetChildren(assetId) {
        return apiClient.get(`/assets/${assetId}/children`)
    },

    getAllCommentsWithReplies(assetId) {
        return apiClient.get(`/assets/${assetId}/comments`)
    },

    addComment(assetId, replyText, timestamp) {
        return apiClient.post(`/assets/${assetId}/comments`, {
            text: replyText,
            annotation: null,
            timestamp: timestamp,
            pitch: null,
            yaw: null
        })
    },

    addReplyToComment(commentId, replyText, timestamp) {
        return apiClient.post(`/comments/${commentId}/replies`, {
            text: replyText,
            annotation: null,
            timestamp: timestamp,
            pitch: null,
            yaw: null
        })
    },

    updateComment(commentId, data) {
        return apiClient.put(`/comments/${commentId}`, data)
    },

    deleteComment(commentId) {
        return apiClient.delete(`/comments/${commentId}`)
    }
}

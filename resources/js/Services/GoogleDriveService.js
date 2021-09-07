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
    fetchToken() {
        return apiClient.get('/google/drive/token')
    },
    isProjectFoldersCreated(videoId) {
        return apiClient.get(
            '/google/drive/is-project-folders-created/' + videoId
        )
    },
    createPermission(fileId, email) {
        return apiClient.post('/google/drive/permissions/create', {
            fileId: fileId,
            email: email
        })
    },
    fetchFilesFromAFolder(folderId, acceptTarget, isShortcutDetails = false) {
        let query = '?shortcut-details=' + isShortcutDetails
        query += acceptTarget ? '&accept-target=' + acceptTarget : ''
        return apiClient.get(`/google/drive/folders/${folderId}/files${query}`)
    },
    uploadFileToGoogleDrive(data) {
        return apiClient.post(`/google/drive/upload/file`, data)
    },
    updateFileTrashedOrNot(fileId, isTrashed) {
        return apiClient.patch(
            `/google/drive/files/${fileId}/move-or-restore-in-trash`,
            {
                is_trashed: isTrashed
            }
        )
    },
    getAFileById(fileId) {
        return apiClient.get(`/google/drive/files/${fileId}`)
    },
    updateVideoProjectName(videoId, name) {
        return apiClient.patch(
            `/google/drive/files/video-projects/${videoId}/update-name`,
            {
                name: name
            }
        )
    },
    deleteFile(fileId) {
        return apiClient.delete(`/google/drive/files/${fileId}`)
    }
}

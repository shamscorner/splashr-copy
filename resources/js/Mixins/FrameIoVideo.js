import FrameioService from '@/Services/FrameioService'
import InsertOrUpdateVideoItemMutation from '@/Graphql/Video/Mutations/InsertOrUpdateVideoItem.gql'
import { videoItemStatus, videoItemType } from '@/Utilities/VideoItems'

export default {
    data() {
        return {
            frameIoVideos: {
                value: [],
                isLoading: false
            },
            isToast: {
                show: false,
                role: 'success',
                message: ''
            }
        }
    },

    methods: {
        async getFrameIoVideosByProject(
            assetPath,
            companyId,
            videoId,
            orderId,
            isClient = false
        ) {
            this.frameIoVideos.isLoading = true

            const assetPathArr = assetPath.split('/')

            if (!assetPathArr.length) {
                return
            }

            try {
                if (assetPathArr.length === 1) {
                    const response = await FrameioService.getProject(
                        assetPathArr[0]
                    )

                    if (response.data.root_asset_id) {
                        this.getAndSyncFrameIoVideos(
                            response.data.root_asset_id,
                            companyId,
                            videoId,
                            orderId,
                            isClient
                        )
                    }
                } else {
                    this.getAndSyncFrameIoVideos(
                        assetPathArr[assetPathArr.length - 1],
                        companyId,
                        videoId,
                        orderId,
                        isClient
                    )
                }
            } catch (error) {
                console.log(error)

                this.isToast.message =
                    'Frame.io project url has not been found.'
                this.isToast.role = 'danger'
                this.isToast.show = true
            } finally {
                this.frameIoVideos.isLoading = false
            }
        },

        async getAndSyncFrameIoVideos(
            rootAssetId,
            companyId,
            videoId,
            orderId,
            isClient
        ) {
            try {
                const response = await FrameioService.getAssetChildren(
                    rootAssetId
                )

                let videos = []

                if (isClient) {
                    videos = response.data.filter(
                        video =>
                            video.label !== 'in_progress' &&
                            video.label !== 'none'
                    )
                } else {
                    videos = response.data
                }

                this.frameIoVideos.value = videos.map(video => {
                    return {
                        id: video.id,
                        name: video.name,
                        updated_at: video.updated_at,
                        label: video.label,
                        thumb: video.cover_asset
                            ? video.cover_asset.thumb
                            : video.thumb,
                        video_item_type: videoItemType.master
                    }
                })

                this.syncFrameIoVideoItemsToBackend(
                    videos,
                    companyId,
                    videoId,
                    orderId,
                    isClient
                )
            } catch (error) {
                console.error(error)
            }
        },

        async syncFrameIoVideoItemsToBackend(
            videos,
            companyId,
            videoId,
            orderId,
            isClient
        ) {
            if (!videos.length) {
                return
            }

            try {
                const response = await this.$apollo.mutate({
                    mutation: InsertOrUpdateVideoItemMutation,
                    variables: {
                        videoItems: videos.map(video => {
                            return {
                                id: video.id,
                                company_id: companyId,
                                video_id: videoId,
                                order_id: orderId,
                                name: video.name,
                                status: videoItemStatus[video.label],
                                url: JSON.stringify(video.downloads)
                            }
                        })
                    }
                })

                if (response.data.insertOrUpdateVideoItem) {
                    this.updateVideoItemsData(
                        response.data.insertOrUpdateVideoItem,
                        isClient
                    )
                }
            } catch (error) {
                console.error(error)
            }
        },

        updateVideoItemsData(videoItemsFromBackend, isClient) {
            videoItemsFromBackend.forEach(videoItem => {
                this.frameIoVideos.value.find(frameIoVideo => {
                    if (frameIoVideo.id === videoItem.id) {
                        frameIoVideo.video_item_type = videoItem.type
                        if (
                            !isClient &&
                            videoItem.status === videoItemStatus.paid
                        ) {
                            frameIoVideo.label = 'paid'
                        }
                    }
                })
            })
        }
    }
}

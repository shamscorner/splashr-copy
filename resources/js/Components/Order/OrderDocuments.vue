<template>
    <div>
        <!-- Add document section -->
        <div
            v-if="!isClient"
            class="absolute flex items-center px-4 py-3 space-x-3 font-semibold text-gray-600 transition bg-white rounded-full shadow-md cursor-pointer bottom-8 right-10"
            :class="{
                'opacity-30 pointer-events-none': isLoadingAddDocumentButton
            }"
            v-click-outside="defocusApp"
            @click.stop="openCreateDocumentInput"
        >
            <google-drive-icon></google-drive-icon>

            <div v-if="!isAddInput">Add document</div>
            <div v-else class="flex items-center space-x-4">
                <input
                    v-model="documentInput.url"
                    type="text"
                    placeholder="Add a Google Drive document url"
                    class="py-0.5 w-60 text-sm border-gray-400 rounded-md"
                    ref="documentInput"
                    @keyup.enter="createCreativeDocument"
                />
                <button-plus
                    class="w-6 h-6 text-gray-500 transition border-2 border-gray-500 rounded-full hover:text-gray-800 hover:border-gray-800"
                    :class="{
                        'pointer-events-none opacity-30':
                            documentInput.isLoading
                    }"
                    @click.native="createCreativeDocument"
                ></button-plus>
            </div>
        </div>
        <!-- documents view -->
        <empty-message v-if="!creativeDocuments.length" class="mt-10">
            No documents found.<br />
            Don't be shy, add one using the button below.
        </empty-message>
        <div v-else class="flex flex-wrap">
            <document-box
                v-for="document in creativeDocuments"
                :key="document.id"
                :document="{
                    ...document,
                    redirectTo: isClient
                        ? getDocumentUrl(document)
                        : document.url
                }"
                :is-client="isClient"
                @on-sync="syncCreativeDocument"
                @clicked="document.is_changed = false"
                class="mr-5 my-2.5"
            ></document-box>
        </div>
    </div>
</template>

<script>
import DocumentBox from '@/Components/DocumentBox'
import GoogleDriveIcon from '@/Components/Icons/GoogleDriveIcon'
import ButtonPlus from '@/Components/ButtonPlus'
import DefocusMixin from '@/Mixins/Defocus'
import IsPriceAvailable from '@/Mixins/IsPriceAvailable'
import FetchCommitments from '@/Mixins/FetchCommitments'
import GoogleDriveService from '@/Services/GoogleDriveService'
import { videoType } from '@/Utilities/VideoStatus'
import { activityDescription } from '@/Utilities/Activity'
import CreateCreativeDocumentMutation from '@/Graphql/Order/Mutations/CreateCreativeDocument.gql'
import UpdateCreativeDocumentMutation from '@/Graphql/Order/Mutations/UpdateCreativeDocument.gql'

export default {
    mixins: [FetchCommitments, IsPriceAvailable, DefocusMixin],

    components: {
        DocumentBox,
        GoogleDriveIcon,
        ButtonPlus,
        EmptyMessage: () =>
            import(
                /* webpackChunkName: 'EmptyMessage' */ '@/Components/EmptyMessage'
            )
    },

    props: {
        documents: {
            type: Array,
            required: true
        },
        companyId: {
            type: String,
            required: false,
            default: ''
        },
        orderId: {
            type: String,
            required: true
        },
        isClient: {
            type: Boolean,
            required: false,
            default: false
        },
        videoSessionType: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            creativeDocuments: this.documents.map(document => {
                return {
                    ...document,
                    isLoading: false
                }
            }),
            isAddInput: false,
            documentInput: {
                url: '',
                isLoading: false
            },
            isLoadingAddDocumentButton: false
        }
    },

    created() {
        const closeOnEscape = e => {
            if (this.isAddInput && e.keyCode === 27) {
                this.isAddInput = false
            }
        }

        this.$once('hook:destroyed', () => {
            document.removeEventListener('keydown', closeOnEscape)
        })

        document.addEventListener('keydown', closeOnEscape)
    },

    methods: {
        getDocumentUrl(document) {
            if (document.type == videoType.slide) {
                // eslint-disable-next-line no-undef
                return route('orders.storyboard.google-slide', {
                    order: this.orderId,
                    id: document.document_id
                })
            } else if (document.type == videoType.doc) {
                // eslint-disable-next-line no-undef
                return route('orders.creative-proposal.google-doc', {
                    order: this.orderId,
                    id: document.document_id
                })
            }
            return ''
        },

        async openCreateDocumentInput() {
            if (this.isAddInput) {
                return
            }

            this.isLoadingAddDocumentButton = true

            try {
                // @mixin method: FetchCommitments
                const response = await this.fetchCommitments(this.companyId)

                if (response.data.commitment) {
                    // @mixin method: IsPriceAvailable
                    if (
                        this.isPriceAvailable(
                            response.data.commitment,
                            this.videoSessionType
                        )
                    ) {
                        this.isAddInput = true
                    } else {
                        this.$emit('no-price')
                    }
                } else {
                    this.$emit('no-price')
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            } finally {
                this.isLoadingAddDocumentButton = false
            }
        },

        async createCreativeDocument() {
            if (!this.documentInput.url) {
                return
            }

            this.documentInput.isLoading = true

            const documentId = this.parseDocumentIdFromUrl(
                this.documentInput.url
            )

            try {
                const documentFileResponse = await GoogleDriveService.getAFileById(
                    documentId
                )

                if (documentFileResponse.data.file.type) {
                    let documentType = this.getDocumentMimeType(
                        documentFileResponse.data.file.type
                    )

                    const response = await this.$apollo.mutate({
                        mutation: CreateCreativeDocumentMutation,
                        variables: {
                            creativeDocument: {
                                type: documentType,
                                url: this.documentInput.url,
                                order_id: this.orderId,
                                document_id: documentId
                            }
                        }
                    })

                    // show in the documents view
                    if (response.data.createCreativeDocument) {
                        this.creativeDocuments.unshift({
                            ...response.data.createCreativeDocument,
                            isLoading: false
                        })
                        // clear the input and hide input view
                        this.documentInput.url = ''
                        this.isAddInput = false

                        // notify to register the document added activity
                        this.registerDocumentSubmittedActivity(documentType)
                    }
                }
            } catch (error) {
                // Error
                console.error(error)
                alert(
                    'Oops!!! Something went wrong. Maybe your document already exists.'
                )
            } finally {
                this.documentInput.isLoading = false
            }
        },

        async syncCreativeDocument(document) {
            this.loadDocumentProgressbar(document.id, true)

            try {
                const response = await this.$apollo.mutate({
                    mutation: UpdateCreativeDocumentMutation,
                    variables: {
                        id: document.id,
                        creativeDocument: {
                            type: document.type,
                            url: document.url,
                            order_id: document.order_id,
                            document_id: document.document_id,
                            is_changed: true
                        }
                    }
                })

                if (response.data.updateCreativeDocument) {
                    // notify to register the document added activity
                    this.registerDocumentSubmittedActivity(document.type)
                }
            } catch (error) {
                // Error
                console.error(error)
                alert(
                    'Oops!!! Something went wrong. Maybe your document already exists.'
                )
            } finally {
                this.loadDocumentProgressbar(document.id, false)
            }
        },

        parseDocumentIdFromUrl(url) {
            const documentArr = url.split('/')
            return documentArr[documentArr.length - 2]
        },

        getDocumentMimeType(type) {
            if (type === 'application/vnd.google-apps.document') {
                return videoType.doc
            }
            if (type === 'application/vnd.google-apps.presentation') {
                return videoType.slide
            }
            return 0
        },

        loadDocumentProgressbar(documentId, isLoading) {
            this.creativeDocuments.find(doc => {
                if (doc.id === documentId) {
                    doc.isLoading = isLoading
                }
            })
        },

        registerDocumentSubmittedActivity(documentType) {
            if (documentType == videoType.doc) {
                this.$emit(
                    'document-added',
                    activityDescription.submittedCreativeIdeas
                )
            } else if (documentType == videoType.slide) {
                this.$emit(
                    'document-added',
                    activityDescription.submittedStoryboard
                )
            }
        },

        defocusApp() {
            this.isAddInput = false
        }
    }
}
</script>

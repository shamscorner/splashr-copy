<template>
    <div>
        <h1 class="mt-5 text-lg font-semibold text-gray-900">
            {{ leadingTitle }}
        </h1>
        <table-custom :headers="['Video type', 'Add', 'Used', 'Left', 'Price']">
            <table-row-custom>
                <table-data-custom>
                    Master
                </table-data-custom>
                <table-data-custom>
                    <div class="flex items-center justify-center">
                        <counter-button
                            tiny
                            signed
                            input-mode
                            :is-flushed="formCreditTypes.master.isFlushed"
                            class="w-24 text-gray-600 bg-white"
                            @increment="increment($event, 'master')"
                            @decrement="decrement($event, 'master')"
                        ></counter-button>
                    </div>
                </table-data-custom>
                <table-data-custom class="text-center">
                    {{ formCreditTypes.master.usedQuantity }}
                </table-data-custom>
                <table-data-custom class="text-center">
                    {{
                        formCreditTypes.master.quantity -
                            formCreditTypes.master.usedQuantity
                    }}
                </table-data-custom>
                <table-data-custom>
                    <input
                        v-model="formCreditTypes.master.price"
                        type="text"
                        class="w-16 px-2 py-1 text-sm"
                    />
                    <span class="ml-1">€</span>
                </table-data-custom>
            </table-row-custom>
            <table-row-custom>
                <table-data-custom>
                    Iteration
                </table-data-custom>
                <table-data-custom>
                    <div class="flex items-center justify-center">
                        <counter-button
                            tiny
                            signed
                            input-mode
                            :is-flushed="formCreditTypes.iteration.isFlushed"
                            class="w-24 text-gray-600 bg-white"
                            @increment="increment($event, 'iteration')"
                            @decrement="decrement($event, 'iteration')"
                        ></counter-button>
                    </div>
                </table-data-custom>
                <table-data-custom class="text-center">
                    {{ formCreditTypes.iteration.usedQuantity }}
                </table-data-custom>
                <table-data-custom class="text-center">
                    {{
                        formCreditTypes.iteration.quantity -
                            formCreditTypes.iteration.usedQuantity
                    }}
                </table-data-custom>
                <table-data-custom>
                    <input
                        v-model="formCreditTypes.iteration.price"
                        type="text"
                        class="w-16 px-2 py-1 text-sm"
                    />
                    <span class="ml-1">€</span>
                </table-data-custom>
            </table-row-custom>
            <table-row-custom>
                <table-data-custom>
                    Rich Content
                </table-data-custom>
                <table-data-custom>
                    <div class="flex items-center justify-center">
                        <counter-button
                            tiny
                            signed
                            input-mode
                            :is-flushed="formCreditTypes.richContent.isFlushed"
                            class="w-24 text-gray-600 bg-white"
                            @increment="increment($event, 'richContent')"
                            @decrement="decrement($event, 'richContent')"
                        ></counter-button>
                    </div>
                </table-data-custom>
                <table-data-custom class="text-center">
                    {{ formCreditTypes.richContent.usedQuantity }}
                </table-data-custom>
                <table-data-custom class="text-center">
                    {{
                        formCreditTypes.richContent.quantity -
                            formCreditTypes.richContent.usedQuantity
                    }}
                </table-data-custom>
                <table-data-custom>
                    <input
                        v-model="formCreditTypes.richContent.price"
                        type="text"
                        class="w-16 px-2 py-1 text-sm"
                    />
                    <span class="ml-1">€</span>
                </table-data-custom>
            </table-row-custom>
        </table-custom>
        <div class="flex justify-end mt-6">
            <jet-secondary-button
                class="font-bold"
                :class="{
                    'opacity-0 pointer-events-none':
                        isUpdatingAgreement || !isFormChanged
                }"
                @click.native="updateCommitment"
            >
                <div class="flex items-center">
                    <loading-icon
                        v-if="isUpdatingAgreement"
                        class="w-5 h-5"
                    ></loading-icon>
                    <div>Update</div>
                </div>
            </jet-secondary-button>
        </div>
    </div>
</template>

<script>
import TableCustom from '@/Components/Table/TableCustom'
import TableRowCustom from '@/Components/Table/TableRowCustom'
import TableDataCustom from '@/Components/Table/TableDataCustom'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import LoadingIcon from '@/Components/Icons/LoadingIcon'
import CounterButton from '@/Components/CounterButton'
import UpdateOrCreateCommitmentMutation from '@/Graphql/Company/Mutations/UpdateOrCreateCommitment.gql'

export default {
    components: {
        TableCustom,
        TableRowCustom,
        TableDataCustom,
        JetSecondaryButton,
        LoadingIcon,
        CounterButton
    },

    props: {
        leadingTitle: {
            type: String,
            required: true
        },
        creditTypes: {
            type: Object,
            required: true
        },
        companyId: {
            type: String,
            required: true
        },
        type: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            formCreditTypes: {
                master: {
                    quantity: this.creditTypes.master.quantity
                        ? this.creditTypes.master.quantity
                        : 0,
                    usedQuantity: this.creditTypes.master.usedQuantity
                        ? this.creditTypes.master.usedQuantity
                        : 0,
                    price: this.creditTypes.master.price
                        ? this.creditTypes.master.price
                        : 500,
                    isFlushed: false
                },
                iteration: {
                    quantity: this.creditTypes.iteration.quantity
                        ? this.creditTypes.iteration.quantity
                        : 0,
                    usedQuantity: this.creditTypes.iteration.usedQuantity
                        ? this.creditTypes.iteration.usedQuantity
                        : 0,
                    price: this.creditTypes.iteration.price
                        ? this.creditTypes.iteration.price
                        : 500,
                    isFlushed: false
                },
                richContent: {
                    quantity: this.creditTypes.richContent.quantity
                        ? this.creditTypes.richContent.quantity
                        : 0,
                    usedQuantity: this.creditTypes.richContent.usedQuantity
                        ? this.creditTypes.richContent.usedQuantity
                        : 0,
                    price: this.creditTypes.richContent.price
                        ? this.creditTypes.richContent.price
                        : 250,
                    isFlushed: false
                }
            },
            isUpdatingAgreement: false,
            isFormChanged: false
        }
    },

    watch: {
        formCreditTypes: {
            deep: true,
            handler: function() {
                this.isFormChanged = true
            }
        }
    },

    methods: {
        increment(value, type) {
            value = parseInt(value)
            this.formCreditTypes[type].quantity += value
        },

        decrement(value, type) {
            value = parseInt(value)
            this.formCreditTypes[type].quantity -= value
        },

        async updateCommitment() {
            if (!this.formCreditTypes.master.price) {
                this.showToastMessage('Master price is required', 'danger')
                return
            }

            if (!this.formCreditTypes.iteration.price) {
                this.showToastMessage('Iteration price is required', 'danger')
                return
            }

            if (!this.formCreditTypes.richContent.price) {
                this.showToastMessage(
                    'Rich content price is required',
                    'danger'
                )
                return
            }

            this.isUpdatingAgreement = true

            let variables = null

            if (this.type === 'motion') {
                variables = {
                    id: this.companyId,
                    companyId: this.companyId,
                    commitment: {
                        motion_quantity_master: this.formCreditTypes.master
                            .quantity,
                        motion_quantity_iteration: this.formCreditTypes
                            .iteration.quantity,
                        motion_quantity_rich_content: this.formCreditTypes
                            .richContent.quantity,
                        motion_price_master: this.formCreditTypes.master.price,
                        motion_price_iteration: this.formCreditTypes.iteration
                            .price,
                        motion_price_rich_content: this.formCreditTypes
                            .richContent.price
                    }
                }
            } else if (this.type === 'acting') {
                variables = {
                    id: this.companyId,
                    companyId: this.companyId,
                    commitment: {
                        acting_quantity_master: this.formCreditTypes.master
                            .quantity,
                        acting_quantity_iteration: this.formCreditTypes
                            .iteration.quantity,
                        acting_quantity_rich_content: this.formCreditTypes
                            .richContent.quantity,
                        acting_price_master: this.formCreditTypes.master.price,
                        acting_price_iteration: this.formCreditTypes.iteration
                            .price,
                        acting_price_rich_content: this.formCreditTypes
                            .richContent.price
                    }
                }
            }

            try {
                const response = await this.$apollo.mutate({
                    mutation: UpdateOrCreateCommitmentMutation,
                    variables: variables
                })

                if (response.data.updateOrCreateCommitment) {
                    this.showToastMessage('Agreement updated!')
                    this.resetForm()

                    setTimeout(() => {
                        this.isFormChanged = false
                    }, 1000)
                }
            } catch (error) {
                console.error(error)
                alert('Something went wrong!')
            } finally {
                this.isUpdatingAgreement = false
            }
        },

        showToastMessage(message, role = 'success') {
            this.$emit('show-toast', {
                message: message,
                role: role
            })
        },

        resetForm() {
            this.formCreditTypes.master.isFlushed = !this.formCreditTypes.master
                .isFlushed
            this.formCreditTypes.iteration.isFlushed = !this.formCreditTypes
                .iteration.isFlushed
            this.formCreditTypes.richContent.isFlushed = !this.formCreditTypes
                .richContent.isFlushed
        }
    }
}
</script>

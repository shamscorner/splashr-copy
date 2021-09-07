<template>
    <jet-form-section @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                class="col-span-6 sm:col-span-4"
                v-if="$page.jetstream.managesProfilePhotos"
            >
                <!-- Profile Photo File Input -->
                <input
                    type="file"
                    class="hidden"
                    ref="photo"
                    @change="updatePhotoPreview"
                />

                <jet-label for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div class="mt-2" v-show="!photoPreview">
                    <jet-avatar
                        :src="user.profile_photo_url"
                        alt="Current Profile Photo"
                        :name="`${user.first_name} ${user.last_name}`"
                        class="w-20 h-20 font-semibold"
                    ></jet-avatar>
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" v-show="photoPreview">
                    <span
                        class="block w-20 h-20 rounded-full"
                        :style="
                            'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
                                photoPreview +
                                '\');'
                        "
                    >
                    </span>
                </div>

                <jet-secondary-button
                    class="mt-2 mr-2"
                    type="button"
                    @click.native.prevent="selectNewPhoto"
                >
                    Select A New Photo
                </jet-secondary-button>

                <jet-secondary-button
                    type="button"
                    class="mt-2"
                    @click.native.prevent="deletePhoto"
                    v-if="user.profile_photo_path"
                >
                    Remove Photo
                </jet-secondary-button>

                <jet-input-error :message="form.error('photo')" class="mt-2" />
            </div>

            <!-- First name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="first-name" value="First Name" />
                <jet-input
                    id="first-name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.first_name"
                    autocomplete="name"
                />
                <jet-input-error
                    :message="form.error('first_name')"
                    class="mt-2"
                />
            </div>

            <!-- Last name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="last-name" value="Last Name" />
                <jet-input
                    id="last-name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.last_name"
                    autocomplete="name"
                />
                <jet-input-error
                    :message="form.error('last_name')"
                    class="mt-2"
                />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email" />
                <jet-input
                    id="email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.email"
                />
                <jet-input-error :message="form.error('email')" class="mt-2" />
            </div>

            <!-- Company -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="company" value="Company" />
                <jet-input
                    id="company"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.company"
                />
                <jet-input-error
                    :message="form.error('company')"
                    class="mt-2"
                />
            </div>

            <!-- Phone -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="phone" value="Phone" />
                <jet-input
                    id="phone"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.phone"
                />
                <jet-input-error :message="form.error('phone')" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetAvatar from '@/Jetstream/Avatar'
import GetCompanyQuery from '@/Graphql/Company/Queries/GetCompany.gql'

export default {
    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetAvatar
    },

    props: ['user'],

    data() {
        return {
            form: this.$inertia.form(
                {
                    _method: 'PUT',
                    first_name: this.user.first_name,
                    last_name: this.user.last_name,
                    email: this.user.email,
                    phone: this.user.phone,
                    company: '',
                    photo: null
                },
                {
                    bag: 'updateProfileInformation',
                    resetOnSuccess: false
                }
            ),

            photoPreview: null
        }
    },

    async mounted() {
        try {
            const response = await this.$apollo.query({
                query: GetCompanyQuery,
                variables: {
                    id: this.user.company_id
                }
            })

            if (response.data.company) {
                this.form.company = response.data.company.name
            }
        } catch (error) {
            console.error(error)
        }
    },

    methods: {
        updateProfileInformation() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0]
            }

            // eslint-disable-next-line no-undef
            this.form.post(route('user-profile-information.update'), {
                preserveScroll: true
            })
        },

        selectNewPhoto() {
            this.$refs.photo.click()
        },

        updatePhotoPreview() {
            const reader = new FileReader()

            reader.onload = e => {
                this.photoPreview = e.target.result
            }

            reader.readAsDataURL(this.$refs.photo.files[0])
        },

        deletePhoto() {
            this.$inertia
                // eslint-disable-next-line no-undef
                .delete(route('current-user-photo.destroy'), {
                    preserveScroll: true
                })
                .then(() => {
                    this.photoPreview = null
                })
        }
    }
}
</script>

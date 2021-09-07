import { calculateStepHref } from '@/Utilities/CalculateStepHref'

export default {
    methods: {
        async submitFormData(payload) {
            try {
                const isSucceed = await this.submit(payload)

                if (isSucceed) {
                    if (payload.pageNumberToGo === -1) {
                        // save and close
                        // eslint-disable-next-line no-undef
                        this.$inertia.replace(route('dashboard.client'))
                        return
                    }

                    const routeName = this.$page.isEdit
                        ? 'videos.update'
                        : 'videos.steps'

                    this.$inertia.replace(
                        // eslint-disable-next-line no-undef
                        route(routeName, {
                            video: this.$page.video.id,
                            step: calculateStepHref(
                                this.$page.videoSteps[payload.pageNumberToGo]
                            ),
                            sidebar: this.$page.isSideNavVisible
                                ? this.$page.isSideNavVisible
                                : 'true',
                            edit: this.$page.isEdit
                        })
                    )
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        getSaveAnswerData(input) {
            let data = this.$page.data.remembered[input]
                ? this.$page.data.remembered[input]
                : ''

            if (this.form[input].isRemember) {
                if (this.form[input].value) {
                    data = this.form[input].value
                }
            }

            return data
        }
    }
}

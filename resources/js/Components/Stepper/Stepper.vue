<template>
    <div class="flex flex-wrap items-center">
        <div
            v-for="(step, index) in steps"
            :key="index"
            class="flex items-center mb-4 mr-4 space-x-4 lg:space-x-8 lg:mr-8 lg:mb-0"
        >
            <step-view
                :count="index + 1"
                :is-active="index + 1 == active"
                :is-completed="index + 1 < active"
                :step="step"
            >
            </step-view>
            <step-separator v-if="index + 1 != steps.length"></step-separator>
        </div>
    </div>
</template>

<script>
import StepView from '@/Components/Stepper/StepView'
import StepSeparator from '@/Components/Stepper/StepSeparator'

export default {
    components: {
        StepView,
        StepSeparator
    },

    props: {
        active: {
            type: Number,
            require: false,
            default: 1
        },

        steps: {
            type: Array,
            required: false,
            default: function() {
                return []
            }
        }
    }
}
</script>

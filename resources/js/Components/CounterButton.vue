<template>
    <div class="flex items-center border border-gray-300 rounded-full">
        <button
            class="transition px-1.5 py-1 hover:text-black rounded-tl-full rounded-bl-full focus:outline-none focus:text-black"
            :class="{
                'opacity-30 pointer-events-none':
                    (counter === 0 && !signed) || (signed && !inputCounter)
            }"
            @click="$emit('decrement', inputCounter)"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                :class="[tiny ? 'w-4 h-4' : 'w-6 h-6']"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18 12H6"
                />
            </svg>
        </button>
        <div
            v-if="inputMode"
            class="w-12 px-1 border-l border-r border-gray-300"
        >
            <input
                v-model="inputCounter"
                type="number"
                :min="-1000"
                :max="1000"
                class="w-full p-0 m-0 text-sm text-center border-none focus:ring-0"
            />
            <!-- @input="$emit('input', $event.target.value)" -->
        </div>
        <div
            v-else
            class="w-12 px-2 py-1 text-center border-l border-r border-gray-300"
            :class="[tiny ? 'text-sm' : 'font-semibold']"
        >
            {{ counter }}
        </div>
        <button
            class="transition px-1.5 py-1 hover:text-black rounded-tr-full rounded-br-full focus:outline-none focus:text-black"
            :class="{
                'opacity-30 pointer-events-none': signed && !inputCounter
            }"
            @click="$emit('increment', inputCounter)"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                :class="[tiny ? 'w-4 h-4' : 'w-6 h-6']"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                />
            </svg>
        </button>
    </div>
</template>

<script>
export default {
    props: {
        counter: {
            type: Number,
            required: false,
            default: 0
        },
        tiny: {
            type: Boolean,
            required: false,
            default: false
        },
        signed: {
            type: Boolean,
            required: false,
            default: false
        },
        inputMode: {
            type: Boolean,
            required: false,
            default: false
        },
        isFlushed: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    data() {
        return {
            inputCounter: 0
        }
    },

    watch: {
        isFlushed() {
            this.inputCounter = 0
        },
        inputCounter(value) {
            if (value > 1000) {
                return (this.inputCounter = 1000)
            }
            if (value < -1000) {
                return (this.inputCounter = -1000)
            }
        }
    }
}
</script>

<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type='number'] {
    -moz-appearance: textfield;
}
</style>

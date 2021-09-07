<template>
    <button class="rounded-full focus:outline-none">
        <svg
            v-if="!check"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 40 24"
            class="w-full h-full"
        >
            <g fill="none" fill-rule="evenodd">
                <g>
                    <g>
                        <path
                            d="M0 0H40V24H0z"
                            transform="translate(-1373 -302) matrix(-1 0 0 1 1413 302)"
                        />
                        <path
                            fill="#BBB"
                            fill-rule="nonzero"
                            d="M11.25.75C5.05.75 0 5.794 0 12s5.05 11.25 11.25 11.25h17.5C34.946 23.25 40 18.196 40 12S34.946.75 28.75.75h-17.5z"
                            transform="translate(-1373 -302) matrix(-1 0 0 1 1413 302)"
                        />
                        <path
                            fill="#29243A"
                            fill-opacity=".5"
                            fill-rule="nonzero"
                            d="M28.75 3.25c4.849 0 8.75 3.901 8.75 8.75s-3.901 8.75-8.75 8.75S20 16.849 20 12s3.901-8.75 8.75-8.75z"
                            transform="translate(-1373 -302) matrix(-1 0 0 1 1413 302)"
                        />
                    </g>
                </g>
            </g>
        </svg>

        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 40 24"
            class="w-full h-full"
        >
            <g fill="none" fill-rule="evenodd">
                <g>
                    <g>
                        <path
                            d="M0 0H40V24H0z"
                            opacity=".4"
                            transform="translate(-1373 -362) translate(1373 362)"
                        />
                        <path
                            fill="#BBB"
                            fill-rule="nonzero"
                            d="M11.25.75C5.05.75 0 5.794 0 12s5.05 11.25 11.25 11.25h17.5C34.946 23.25 40 18.196 40 12S34.946.75 28.75.75h-17.5z"
                            transform="translate(-1373 -362) translate(1373 362)"
                        />
                        <path
                            fill="#3502e6"
                            fill-opacity=".69"
                            fill-rule="nonzero"
                            d="M28.75 3.25c4.849 0 8.75 3.901 8.75 8.75s-3.901 8.75-8.75 8.75S20 16.849 20 12s3.901-8.75 8.75-8.75z"
                            transform="translate(-1373 -362) translate(1373 362)"
                        />
                    </g>
                </g>
            </g>
        </svg>
    </button>
</template>

<script>
export default {
    props: {
        check: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

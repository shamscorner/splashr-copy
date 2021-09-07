<template>
    <button
        class="relative text-gray-500 focus:outline-none focus:ring-1 focus:ring-offset-4"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="21"
            viewBox="0 0 22 21"
            class="w-5 h-5"
        >
            <g fill="none" fill-opacity=".5" fill-rule="evenodd">
                <g fill="#000" fill-rule="nonzero">
                    <path
                        d="M1274.948 104h20.104c.474 0 .948.38.948.948v14.604c0 .569-.474.948-.948.948h-7.112l-2.181 3.509c-.38.569-1.233.569-1.612 0l-2.181-3.509h-7.018c-.569 0-.948-.38-.948-.948v-14.604c0-.569.38-.948.948-.948zm19.155 1.897h-18.206v12.706h6.637c.285 0 .664.19.76.475l1.706 2.655 1.612-2.655c.19-.285.474-.475.854-.475h6.637v-12.706z"
                        transform="translate(-1274 -104)"
                    />
                </g>
            </g>
        </svg>
        <div
            v-if="isNotificationDot"
            class="absolute w-3 h-3 bg-red-500 rounded-full -right-1.5 -top-1.5"
        ></div>
    </button>
</template>

<script>
export default {
    props: {
        isNotificationDot: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

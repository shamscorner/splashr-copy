<template>
    <table class="w-full">
        <!-- table header -->
        <thead class="text-xs font-semibold text-left text-gray-400">
            <tr>
                <td
                    v-for="(header, index) in headers"
                    :key="index"
                    class="px-4 py-3"
                >
                    {{ header }}
                </td>
            </tr>
            <tr v-if="isLoading">
                <td
                    class="w-full h-1.5 bg-splashr-blue-light col-span-full animate-pulse"
                    :colspan="headers.length"
                ></td>
            </tr>
            <tr v-else>
                <td
                    class="w-full h-1.5 col-span-full"
                    :colspan="headers.length"
                ></td>
            </tr>
        </thead>

        <!-- table data -->
        <tbody>
            <slot></slot>
        </tbody>
    </table>
</template>

<script>
export default {
    props: {
        headers: {
            type: Array,
            required: true
        },
        isLoading: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>

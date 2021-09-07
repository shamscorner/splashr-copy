<template>
    <div class="h-full">
        <div class="px-4">
            <h1 class="text-lg font-semibold truncate">
                {{ title }}
            </h1>
        </div>
        <div class="flex flex-col h-full">
            <!-- tabs section -->
            <div class="mt-3 border-b">
                <nav class="flex px-4">
                    <slot name="navs"></slot>
                </nav>
            </div>
            <!-- tab content -->
            <div class="flex-1 pb-40 overflow-y-auto">
                <slot name="tab-details"></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: false,
            default: ''
        }
    }
}
</script>

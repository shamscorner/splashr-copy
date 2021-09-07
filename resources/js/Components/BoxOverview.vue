<template>
    <div class="border border-gray-300 rounded-lg ">
        <div
            class="flex items-center justify-between p-3 border-b border-gray-300"
        >
            <div>
                <div class="text-lg font-semibold">
                    <slot name="title"></slot>
                </div>
            </div>
            <div>
                <slot name="action"></slot>
            </div>
        </div>
        <div class="overflow-y-auto max-h-80">
            <slot></slot>
        </div>
    </div>
</template>

<template>
    <table class="w-full text-sm">
        <!-- table header -->
        <thead>
            <slot name="header"></slot>
            <slot name="leading-data"></slot>
            <slot name="leading-title"></slot>
        </thead>

        <!-- table data -->
        <tbody>
            <slot></slot>
        </tbody>
    </table>
</template>

<template>
    <app-layout>
        <div class="h-full pt-16 overflow-hidden sm:pt-20">
            <!-- sidebar for small devices -->
            <div class="sm:hidden">
                <button
                    class="flex w-full px-4 py-3 text-sm font-semibold text-center text-gray-200 bg-splashr-violet-deep focus:outline-none focus:ring-2"
                    @click="isResponsiveSideNav = !isResponsiveSideNav"
                >
                    <p class="flex-1">{{ $page.currentSideNavName }}</p>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        class="w-5 h-5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>
                <div
                    v-if="isResponsiveSideNav"
                    class="flex flex-col p-4 space-y-4 text-white bg-splashr-violet-deep"
                >
                    <side-nav-link
                        :to="route('admin.orders')"
                        :is-selected="
                            $page.currentSideNavRoute == 'admin.orders'
                        "
                    >
                        <div class="flex items-center justify-between">
                            Orders
                        </div>
                    </side-nav-link>
                    <side-nav-link
                        :to="route('admin.clients')"
                        :is-selected="
                            $page.currentSideNavRoute == 'admin.clients'
                        "
                    >
                        <div class="flex items-center justify-between">
                            Clients
                        </div>
                    </side-nav-link>
                    <side-nav-link
                        v-if="$page.manager.role === managerRole.admin"
                        :to="route('admin.settings')"
                        :is-selected="
                            $page.currentSideNavRoute == 'admin.settings'
                        "
                    >
                        <div class="flex items-center justify-between">
                            Admin
                        </div>
                    </side-nav-link>
                </div>
            </div>
            <!-- sidebar for large devices -->
            <div
                class="grid h-full grid-cols-1 overflow-hidden sm:grid-cols-12"
            >
                <!-- sidebar -->
                <div
                    class="justify-between hidden p-5 overflow-hidden text-white sm:flex-col sm:flex sm:col-span-2 lg:col-span-1 bg-splashr-violet-deep"
                >
                    <div class="flex flex-col space-y-4">
                        <side-nav-link
                            :to="route('admin.orders')"
                            :is-selected="
                                $page.currentSideNavRoute == 'admin.orders'
                            "
                            class="mt-4"
                        >
                            <div class="flex flex-col items-center text-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 37 36"
                                    fill="currentColor"
                                    class="w-10 h-10 text-gray-100"
                                >
                                    <g fill-rule="evenodd">
                                        <g fill-rule="nonzero">
                                            <g>
                                                <g>
                                                    <g>
                                                        <path
                                                            d="M33 0c1.657 0 3 1.343 3 3v30c0 1.657-1.343 3-3 3H3c-1.657 0-3-1.343-3-3V3c0-1.657 1.343-3 3-3h30zM8.375 23.625c-1.243 0-2.25 1.007-2.25 2.25s1.007 2.25 2.25 2.25 2.25-1.007 2.25-2.25-1.007-2.25-2.25-2.25zm20.75 1.125h-16.25V27h16.25v-2.25zm-20.75-9c-1.243 0-2.25 1.007-2.25 2.25s1.007 2.25 2.25 2.25 2.25-1.007 2.25-2.25-1.007-2.25-2.25-2.25zm20.75 1.125h-16.25v2.25h16.25v-2.25zm-20.75-9c-1.243 0-2.25 1.007-2.25 2.25s1.007 2.25 2.25 2.25 2.25-1.007 2.25-2.25-1.007-2.25-2.25-2.25zM29.125 9h-16.25v2.25h16.25V9z"
                                                            transform="translate(-50 -117) translate(0 80) translate(37.408 23) translate(13 14)"
                                                        />
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                Orders
                            </div>
                        </side-nav-link>
                        <side-nav-link
                            :to="route('admin.clients')"
                            :is-selected="
                                $page.currentSideNavRoute == 'admin.clients'
                            "
                            class="mt-4"
                        >
                            <div class="flex flex-col items-center text-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="w-10 h-10 text-gray-100"
                                >
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                                    />
                                </svg>
                                Clients
                            </div>
                        </side-nav-link>
                    </div>

                    <div>
                        <side-nav-link
                            v-if="$page.manager.role === managerRole.admin"
                            :to="route('admin.settings')"
                            :is-selected="
                                $page.currentSideNavRoute == 'admin.settings'
                            "
                            class="mt-4"
                        >
                            <div class="flex flex-col items-center text-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="26"
                                    height="36"
                                    viewBox="0 0 26 36"
                                    class="w-10 h-10 text-gray-100"
                                >
                                    <g fill="none" fill-rule="evenodd">
                                        <g fill="#FFF" fill-rule="nonzero">
                                            <g>
                                                <g>
                                                    <g>
                                                        <g>
                                                            <path
                                                                d="M21.87 14.577h-1.035v-6.29c0-4.57-3.717-8.287-8.286-8.287-4.57 0-8.287 3.717-8.287 8.287v6.29H3.227C1.445 14.577 0 16.022 0 17.804v14.97C0 34.554 1.445 36 3.227 36H21.87c1.782 0 3.227-1.445 3.227-3.227V17.804c0-1.782-1.445-3.227-3.227-3.227zM7.163 8.287c0-2.97 2.417-5.388 5.387-5.388 2.97 0 5.387 2.417 5.387 5.388v6.29H7.162v-6.29zM14.2 25.125v4.03c0 .911-.739 1.65-1.651 1.65s-1.65-.739-1.65-1.65v-4.03c-.774-.527-1.281-1.415-1.281-2.421 0-1.62 1.312-2.933 2.93-2.933 1.62 0 2.932 1.313 2.932 2.933 0 1.006-.507 1.893-1.28 2.421z"
                                                                transform="translate(-55 -789) translate(0 80) translate(22 698) translate(23 11) translate(10)"
                                                            />
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>

                                Admin
                            </div>
                        </side-nav-link>
                    </div>
                </div>

                <!-- body -->
                <div class="overflow-y-auto sm:col-span-10 lg:col-span-11">
                    <!-- main section -->
                    <slot></slot>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import SideNavLink from '@/Components/SideNav/SideNavLink'
import { managerRole } from '@/Utilities/User'

export default {
    components: {
        AppLayout,
        SideNavLink
    },

    data() {
        return {
            isResponsiveSideNav: false,
            managerRole
        }
    }
}
</script>

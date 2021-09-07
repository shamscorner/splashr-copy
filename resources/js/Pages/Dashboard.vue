<template>
    <app-layout>
        <!-- style="background: url('/images/bg-wave.svg') no-repeat; background-size: contain; background-position: center bottom;" -->
        <div class="relative mt-16">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="grid items-center grid-cols-2 gap-10 py-10 md:pt-20 md:pb-32"
                >
                    <div>
                        <h1 class="text-3xl font-extrabold">
                            Welcome to Splashr!
                        </h1>
                        <p class="max-w-md mt-10 text-xl">
                            An account manager will contact you shortly to go
                            over your marketing needs and explain how we work.
                            Talk to you soon!
                        </p>
                    </div>
                    <div>
                        <img
                            src="/images/bg-hero-home.png"
                            alt="Hero Image"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
    components: {
        AppLayout
    }
}
</script>

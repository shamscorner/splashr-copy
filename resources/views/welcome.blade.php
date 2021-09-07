<x-guest-layout>
    <div class="relative">
        <nav class="sticky top-0 z-30 bg-white shadow-md">
            <!-- Primary Navigation Menu -->
            <div class="px-4 mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 sm:h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex items-center flex-shrink-0">
                            <a href="/">
                                <img class="w-52" src="{{ asset('images/splashr-logo.png') }}" alt="Splashr">
                            </a>
                        </div>
                    </div>
                    <!-- right nav items -->
                    @if (Route::has('login'))
                    <div class="flex items-center space-x-5">
                        @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-1.5 transition text-xl text-white bg-indigo-600 rounded-full hover:bg-indigo-800"> Dashboard </a>
                        @else

                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-6 py-1.5 transition text-xl text-white bg-indigo-600 rounded-full hover:bg-indigo-800"> Register </a>
                        @endif
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid items-center grid-cols-2 gap-10 py-10 md:pt-20 md:pb-32">
                <div>
                    <h1 class="text-3xl font-extrabold">
                        Des créas vidéos optimisées <br> pour chaque canal d’acquisition
                    </h1>
                    <p class="mt-10 text-xl">
                        Nous créons des vidéos au service de tous les médias digitaux
                        (Facebook, Instagram, Youtube, Snapchat, TikTok). Chaque vidéo
                        est pensée et réalisée en fonction de leurs spécificités : format,
                        temps d’attention de l’internaute, intensité de navigation…
                        Tout est optimisé pour obtenir les meilleures performances possibles !
                    </p>
                </div>
                <div>
                    <img src="{{ asset('images/bg-hero-home.png') }}" alt="Hero Image" class="w-full">
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
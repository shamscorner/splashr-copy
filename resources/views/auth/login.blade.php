<x-guest-layout>
    <div class="w-full sm:min-h-screen bg-splashr-violet-deep">
        <div class="grid sm:grid-cols-2 xl:grid-cols-6">
            <!-- left panel -->
            <div class="text-center text-white sm:flex sm:min-h-screen xl:col-start-2 xl:col-span-2">
                <div class="w-full p-4 mt-8 mb-6 sm:my-auto">
                    <div class="relative pb-1/7 sm:mb-12 lg:pb-20 xl:pb-24">
                        <img class="absolute object-contain w-full h-full" src="{{ asset('images/splash-text.svg') }}" alt="splashr" />
                    </div>
                    <h1 class="my-4 text-xl lg:text-3xl">Boost your business <span class="text-splashr-blue-light">performances</span><br> with <span class="text-splashr-blue-light">stand-out and unique</span> videos <br> thanks to Splashr !
                    </h1>
                </div>
            </div>
            <!-- end of left panel -->

            <!-- right panel -->
            <div class="mb-10 sm:flex sm:h-screen xl:col-start-4 xl:col-span-2">
                <div class="p-5 mx-6 my-2 rounded lg:w-2/3 bg-gray-50 sm:my-auto">
                    <h1 class="text-2xl font-semibold text-center">{{ __('Sign Me In') }}</h1>

                    <!-- sign in with google link component -->
                    <x-sign-in-with-google-link />

                    <div class="flex items-center my-2">
                        <hr class="flex-1"> <span class="mx-4 text-gray-400">{{ __('or') }}</span>
                        <hr class="flex-1">
                    </div>

                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <x-input-text-field class="mb-8" type="email" label="{{ __('Email') }} *" id="email" name="email" :value="old('email')" :required="true" :autofocus="true" />

                        <x-input-text-field class="mb-4" type="password" label="{{ __('Password') }} *" id="password" name="password" :required="true" autocomplete="current-password" />

                        <div class="block mt-8 mb-8">
                            <label for="remember_me" class="flex items-center">
                                <input id="remember_me" type="checkbox" class="text-indigo-600 border-indigo-600 focus:ring-indigo-600" name="remember">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <x-button-auth-form>
                            {{ __('LOG IN') }}
                        </x-button-auth-form>

                        @if (Route::has('password.request'))
                        <a class="block text-sm text-center text-gray-600 underline hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                        @endif

                    </form>

                    <p class="mt-4 text-center">{{ __('You don\'t have any account yet?') }} <a href="{{ route('register') }}" class="ml-2 text-indigo-600 underline hover:text-indigo-700">{{ __('Register') }}</a></p>
                </div>
            </div>
            <!-- end of right panel -->
        </div>
    </div>
</x-guest-layout>
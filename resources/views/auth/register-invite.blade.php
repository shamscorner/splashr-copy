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
                <div class="p-5 mx-6 my-2 rounded bg-gray-50 sm:my-auto">
                    <h1 class="text-2xl font-semibold text-center">{{ __('Sign Me Up') }}</h1>

                    <!-- sign in with google link component -->
                    <!-- <x-sign-in-with-google-link /> -->

                    <div class="flex items-center my-2">
                        <hr class="flex-1"> <span class="mx-4 text-gray-400">{{ __('or') }}</span>
                        <hr class="flex-1">
                    </div>

                    <x-jet-validation-errors class="mb-4" />

                    <form method="POST" action="{{ route('register-new-invited-user', ['token' => $token]) }}">
                        @csrf

                        <x-input-text-field class="mb-8" type="text" id="last-name" label="{{ __('Last Name') }} *" name="last_name" :value="old('last_name')" :required="true" :autofocus="true" autocomplete="name" />

                        <x-input-text-field class="mb-4" type="text" label="{{ __('First Name') }} *" id="first-name" name="first_name" :value="old('first_name')" :required="true" autocomplete="name" />

                        <input type="hidden" id="metadata" name="metadata" value="{{ $metadata }}">

                        <x-input-text-field class="mb-4" type="email" label="{{ __('Email') }} *" id="email" name="email" :value="old('email') ? old('email') : $invitee_email" :required="true" autocomplete="email" />

                        <x-input-text-field class="mb-4" type="text" label="{{ __('Phone Number') }} *" id="phone" name="phone" :value="old('phone')" :required="true" autocomplete="phone" />

                        <x-input-text-field class="mb-4" type="password" label="{{ __('Password') }} *" id="password" name="password" :required="true" autocomplete="new-password" />

                        <x-input-text-field class="mb-4" type="password" label="{{ __('Confirm Password') }} *" id="confirm-password" name="password_confirmation" :required="true" autocomplete="new-password" />

                        <p class="my-8 text-xs text-center text-indigo-600 sm:mx-auto sm:w-3/4">{{ __('By clicking on Create my account, I accept the
                            General Terms and Conditions of Use') }}</p>

                        <x-button-auth-form>
                            {{ __('REGISTER') }}
                        </x-button-auth-form>

                    </form>



                    <p class="text-center">{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="ml-2 text-indigo-600 underline hover:text-indigo-700">{{ __('Login') }}</a></p>
                </div>
            </div>
            <!-- end of right panel -->
        </div>
    </div>
</x-guest-layout>
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
                    <h1 class="text-2xl font-semibold text-center">{{ __('Enter Your Company') }}</h1>

                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register.company-input.create', ['user' => $user_id]) }}" class="my-8">
                        @csrf

                        <x-input-text-field class="mb-4" type="text" label="{{ __('Company Name') }} *" id="company-name" name="company_name" :value="old('company_name')" :required="true" autocomplete="name" />

                        <x-input-text-field class="mb-4" type="text" label="{{ __('Phone Number') }} *" id="phone" name="phone" :value="old('phone')" :required="true" autocomplete="phone" />

                        <x-button-auth-form>
                            {{ __('REGISTER') }}
                        </x-button-auth-form>
                    </form>

                    <p class="mt-6 text-center">{{ __('Back to home?') }} <a href="/" class="ml-2 text-indigo-600 underline hover:text-indigo-700">{{ __('Home') }}</a></p>
                </div>
            </div>
            <!-- end of right panel -->
        </div>
    </div>
</x-guest-layout>
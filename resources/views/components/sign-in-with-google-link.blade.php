<a href="{{ route('auth.google') }}"
    class="flex items-center justify-center w-full px-4 py-2 mt-4 mb-2 text-center text-gray-600 transition border-2 rounded hover:border-gray-400 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">
    <img src="{{ asset('/images/logo-google.svg') }}" width="28" height="28" alt="Google">
    <p class="ml-4">{{ __('Sign in with Google') }}</p>
</a>
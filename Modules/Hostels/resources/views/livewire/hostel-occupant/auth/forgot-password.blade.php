<div class="flex flex-col gap-6">

    <div class="flex flex-col gap-1 text-center">
        <h1 class="text-xl font-semibold text-gray-900">Reset your password</h1>
        <p class="text-sm text-gray-500">Enter your email and we'll send you a reset link</p>
    </div>

    @if ($sent)
        <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 text-center">
            If an account with that email exists, a password reset link has been sent.
        </div>
    @endif

    @if (!$sent)
        <form wire:submit.prevent="sendLink" class="flex flex-col gap-5">

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-medium text-gray-700">Email address</label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    autocomplete="email"
                    autofocus
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                           focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                           @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white
                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
            >
                <span wire:loading.remove>Send reset link</span>
                <span wire:loading>Sending...</span>
            </button>

        </form>
    @endif

    <p class="text-center text-sm text-gray-500">
        <a href="{{ route('hostel_occupant.login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Back to sign in
        </a>
    </p>

</div>

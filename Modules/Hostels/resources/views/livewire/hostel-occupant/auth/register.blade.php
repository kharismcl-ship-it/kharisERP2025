<div class="flex flex-col gap-6">

    <div class="flex flex-col gap-1 text-center">
        <h1 class="text-xl font-semibold text-gray-900">Create your account</h1>
        <p class="text-sm text-gray-500">Fill in your details to register as a resident</p>
    </div>

    <form wire:submit.prevent="register" class="flex flex-col gap-4">

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="first_name" class="text-sm font-medium text-gray-700">First name</label>
                <input
                    type="text"
                    id="first_name"
                    wire:model="first_name"
                    autocomplete="given-name"
                    placeholder="John"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                           focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                           @error('first_name') border-red-400 @enderror"
                >
                @error('first_name')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="last_name" class="text-sm font-medium text-gray-700">Last name</label>
                <input
                    type="text"
                    id="last_name"
                    wire:model="last_name"
                    autocomplete="family-name"
                    placeholder="Doe"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                           focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                           @error('last_name') border-red-400 @enderror"
                >
                @error('last_name')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

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

        <div class="flex flex-col gap-1.5">
            <label for="phone" class="text-sm font-medium text-gray-700">Phone number</label>
            <input
                type="tel"
                id="phone"
                wire:model="phone"
                autocomplete="tel"
                placeholder="+234 800 000 0000"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                       @error('phone') border-red-400 @enderror"
            >
            @error('phone')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="student_id" class="text-sm font-medium text-gray-700">
                Student ID
                <span class="font-normal text-gray-400">(optional)</span>
            </label>
            <input
                type="text"
                id="student_id"
                wire:model="student_id"
                placeholder="e.g. STU/2024/001"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                       @error('student_id') border-red-400 @enderror"
            >
            @error('student_id')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password" class="text-sm font-medium text-gray-700">Password</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="new-password"
                placeholder="At least 8 characters"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                       @error('password') border-red-400 @enderror"
            >
            @error('password')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirm password</label>
            <input
                type="password"
                id="password_confirmation"
                wire:model="password_confirmation"
                autocomplete="new-password"
                placeholder="Repeat your password"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="mt-1 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white
                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                   disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
        >
            <span wire:loading.remove>Create account</span>
            <span wire:loading>Creating account...</span>
        </button>

    </form>

    <p class="text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('hostel_occupant.login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Sign in
        </a>
    </p>

</div>

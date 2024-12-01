<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Perbarui Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- Current Password Input -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
            <x-text-input id="update_password_current_password"
                name="current_password"
                type="password"
                class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500"
                autocomplete="off"
                onpaste="return false;" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- New Password Input -->
        <div>
            <x-input-label for="update_password_password" :value="__('Password Terbaru')" />
            <x-text-input id="update_password_password"
                name="password"
                type="password"
                class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500"
                autocomplete="off"
                onpaste="return false;" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password Input -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500"
                autocomplete="off"
                onpaste="return false;" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4">
            <x-primary-button class="!bg-indigo-500 hover:!bg-indigo-700 !rounded !duration-300">{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
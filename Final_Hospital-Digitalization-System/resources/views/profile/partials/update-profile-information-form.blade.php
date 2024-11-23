<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Perbarui nama dan alamat email akun Anda.") }}
        </p>
    </header>

    <!-- Read-Only Username -->
    <div class="mt-4">
        <x-input-label for="username" :value="__('Username')" />
        <x-text-input id="username" name="username" type="text" class="relative w-full transform border-b-2 bg-gray-100 text-lg duration-300 focus-within:border-indigo-500 mt-6 cursor-not-allowed" :value="old('username', $user->username)" readonly />
    </div>

    <!-- Read-Only Date of Birth -->
    <div class="mt-4">
        <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
        <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="text" class="relative w-full transform border-b-2 bg-gray-100 text-lg duration-300 focus-within:border-indigo-500 mt-6 cursor-not-allowed" :value="old('tanggal_lahir', $user->tanggal_lahir)" readonly />
    </div>

    <!-- Read-Only Gender -->
    <div class="mt-4">
        <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
        <x-text-input id="jenis_kelamin" name="jenis_kelamin" type="text" class="relative w-full transform border-b-2 bg-gray-100 text-lg duration-300 focus-within:border-indigo-500 mt-6 cursor-not-allowed" :value="old('jenis_kelamin', ucfirst($user->jenis_kelamin))" readonly />
    </div>

    <!-- Editable Form (Name and Email) -->
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        
        <!-- Name (Editable) -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" name="name" type="text" class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-4" :value="old('name', $user->name)" required autofocus autocomplete="off" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email (Editable) -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-4" :value="old('email', $user->email)" required autocomplete="off" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-500 hover:bg-indigo-700 rounded duration-300">{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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
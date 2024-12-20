<nav class="bg-indigo-950 w-80 h-screen flex flex-col gap-10 border-r border-slate-100">
    <!-- Logo -->
    <div class="logo text-3xl font-bold text-center h-16 flex items-center justify-center text-white">Hozpitalz</div>

    <!-- User Info -->
    <div class="user flex items-center justify-center flex-col gap-4 border-b border-slate-50 py-4">
        @auth
            @if (auth()->user()->role === 'admin')
                <img class="w-24 rounded-full shadow-xl bg-white" src="{{ asset('/images/svg/adminPP.svg') }}">
            @elseif (auth()->user()->role === 'dokter')
                <img class="w-24 rounded-full shadow-xl bg-white" src="{{ asset('/images/svg/doctorPP.svg') }}">
            @elseif (auth()->user()->role === 'pasien')
                <img class="w-24 rounded-full shadow-xl bg-white" src="{{ asset('/images/svg/pasienPP.svg') }}">
            @endif
        @endauth
        <div class="flex flex-col items-center">
            <span class="font-semibold text-lg text-white">{{ auth()->user()->name }}</span>
            <span class="text-slate-300 text-sm">{{ ucfirst(auth()->user()->role) }}</span>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <ul class="px-6 space-y-2">
        @auth
            @if (auth()->user()->role === 'admin')
                <!-- Admin Menu -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.daftarPengguna') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Daftar Pengguna
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.daftarObat') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Daftar Obat
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.logObat') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Log Obat
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.riwayatPeriksa') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Rekam Medis
                    </a>
                </li>
            @elseif (auth()->user()->role === 'dokter')
                <!-- Dokter Menu -->
                <li>
                    <a href="{{ route('dokter.dashboard') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('dokter.daftarPasien') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Daftar Pasien
                    </a>
                </li>
            @elseif (auth()->user()->role === 'pasien')
                <!-- Pasien Menu -->
                <li>
                    <a href="{{ route('pasien.dashboard') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('pasien.semuaNotifikasi') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Notifikasi
                    </a>
                </li>
            @endif
                <li>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Profil Saya
                    </a>
                </li>
        @endauth
    </ul>

    <!-- Log Out -->
    <div class="px-6 py-4">
        <button onclick="openLogoutModal()" 
                class="w-full text-left px-4 py-2.5 text-red-600 font-semibold hover:bg-red-600 hover:text-white rounded-lg">
            Keluar
        </button>
    </div>

    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Keluar</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari akun ini?</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <div class="flex justify-end space-x-4">
                    <button type="submit" 
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 duration-300">
                        Keluar
                    </button>
                    <button type="button" onclick="closeLogoutModal()" 
                            class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-700 duration-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('hidden');
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.add('hidden');
        }
    </script>

</nav>
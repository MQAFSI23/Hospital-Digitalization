<nav class="bg-indigo-950 w-80 h-screen flex flex-col gap-10 border-r border-slate-100">
    <!-- Logo -->
    <div class="logo text-2xl font-bold text-center h-16 flex items-center justify-center text-white">Hozpitalz</div>

    <!-- User Info -->
    <div class="user flex items-center justify-center flex-col gap-4 border-b border-slate-50 py-4">
        <img class="w-24 rounded-full shadow-xl" src="https://w7.pngwing.com/pngs/340/946/png-transparent-avatar-user-computer-icons-software-developer-avatar-child-face-heroes-thumbnail.png">
        <div class="flex flex-col items-center">
            <span class="font-semibold text-lg text-white">{{ auth()->user()->name ?? 'Guest' }}</span>
            <span class="text-slate-300 text-sm">{{ auth()->user()->role ?? 'User' }}</span>
        </div>
        <div class="text-sm text-slate-400">
            @auth
                @if (auth()->user()->role === 'admin')
                    <span class="font-semibold text-slate-300">Dokter yang Sedang Bertugas</span> ({{ $currentDoctorsCount ?? 0 }})
                @elseif (auth()->user()->role === 'dokter')
                    <span class="font-semibold text-slate-300">Pasien yang Sedang Diperiksa</span> ({{ $currentPatientsCount ?? 0 }})
                @elseif (auth()->user()->role === 'pasien')
                    <span class="font-semibold text-slate-300">Notifikasi Tindakan Medis</span> ({{ $notificationsCount ?? 0 }})
                @endif
            @endauth
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
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Daftar Pengguna
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Daftar Obat
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Laporan & Arsip
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
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Daftar Pasien
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Rekam Medis
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Akses Cepat
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
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
                        Rekam Medis Saya
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2.5 text-slate-200 font-semibold hover:bg-indigo-700 hover:text-white rounded-lg">
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
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2.5 text-red-600 font-semibold hover:bg-red-600 hover:text-white rounded-lg">
                Keluar
            </button>
        </form>
    </div>
</nav>
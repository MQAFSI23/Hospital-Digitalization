<div class="w-64 bg-blue-900 text-white h-screen p-4">
    <div class="text-xl font-semibold mb-8">
        Hospital Digitalization
    </div>
    <ul class="space-y-4">
        <!-- Admin Sidebar Links -->
        @if(Auth::user()->role === 'admin')
            <li>
                <a href="{{ route('admin.dashboard') }}" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Dashboard Admin
                </a>
            </li>
            <li>
                <a href="" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Manage Users
                </a>
            </li>
        @endif

        <!-- Dokter Sidebar Links -->
        @if(Auth::user()->role === 'dokter')
            <li>
                <a href="{{ route('dokter.dashboard') }}" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Dashboard Dokter
                </a>
            </li>
            <li>
                <a href="" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Appointments
                </a>
            </li>
        @endif

        <!-- Pasien Sidebar Links -->
        @if(Auth::user()->role === 'pasien')
            <li>
                <a href="{{ route('pasien.dashboard') }}" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Dashboard Pasien
                </a>
            </li>
            <li>
                <a href="" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Medical Records
                </a>
            </li>
        @endif

        <li class="mt-8">
            <a href="{{ route('profile.edit') }}" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                Profile
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block text-white hover:bg-blue-700 px-4 py-2 rounded-md">
                    Log Out
                </a>
            </form>
        </li>
    </ul>
</div>
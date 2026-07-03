<div class="w-72 xl:w-80 bg-gradient-to-b from-sky-800 via-blue-900 to-blue-950 text-slate-100 min-h-screen p-5 flex flex-col">

    @php
        $user = auth()->user();

        $isSuperadmin = $user && $user->role === 'superadmin';
        $isAdmin = $user && $user->role === 'admin';

        $isKepegawaian = request()->routeIs('superadmin.pegawai.*');
        $isAbsensi = request()->routeIs('superadmin.absensi.*');
        $isLaporan = request()->routeIs('superadmin.laporan.*');
        $isMaster = request()->routeIs('superadmin.master.*')
            || request()->routeIs('superadmin.tipe-pegawai.*')
            || request()->routeIs('superadmin.hari-libur.*');

        function navClass($active = false)
        {
            return $active
                ? 'w-full flex justify-between items-center gap-3 rounded-3xl bg-white/15 px-4 py-3 font-semibold text-white shadow-sm transition'
                : 'w-full flex justify-between items-center gap-3 rounded-3xl px-4 py-3 text-slate-200 hover:bg-white/10 transition';
        }

        function subNavClass($active = false)
        {
            return $active
                ? 'block rounded-2xl bg-white/10 px-4 py-2 font-medium text-white transition'
                : 'block rounded-2xl px-4 py-2 text-slate-300 hover:bg-white/10 transition';
        }
    @endphp

    <div class="mb-8 rounded-3xl bg-blue-950/90 border border-blue-400/15 p-5 shadow-xl backdrop-blur-sm">
        <div class="mb-4 flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-white p-1.5 shadow-inner overflow-hidden">
                <img src="{{ asset('images/logo-kominfo-copy.jpg') }}" alt="Logo" class="h-full w-full object-contain">
            </div>
            <div>
                <h2 class="text-xl font-bold tracking-wide text-white">SIAPMAN</h2>
                <p class="text-[10px] uppercase tracking-[0.2em] text-sky-200">Presensi ASN</p>
            </div>
        </div>
        <p class="text-xs leading-5 text-slate-300">Kelola presensi, jadwal, dan laporan dengan antarmuka yang lebih bersih.</p>
    </div>

    <nav class="flex-1 overflow-y-auto pr-2 text-sm space-y-4">
        @if($isSuperadmin)
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Navigasi</p>
                <a href="{{ route('superadmin.dashboard') }}"
                   class="{{ navClass(request()->routeIs('superadmin.dashboard')) }}">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('superadmin.pengguna.index') }}"
                   class="{{ navClass(request()->routeIs('superadmin.pengguna.*')) }}">
                    <span>Manajemen Admin</span>
                </a>
            </div>
        @else
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Navigasi</p>
                <a href="{{ route('dashboard') }}"
                   class="{{ navClass(request()->routeIs('dashboard')) }}">
                    <span>Dashboard</span>
                </a>
            </div>
        @endif

        @if($isSuperadmin)
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Kepegawaian</p>
                <div x-data="{ openKepegawaian: {{ $isKepegawaian ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="openKepegawaian = !openKepegawaian"
                            type="button"
                            class="{{ navClass($isKepegawaian) }}">
                        <span>Kepegawaian</span>
                        <svg :class="{ 'rotate-180': openKepegawaian }"
                             class="w-4 h-4 transform transition-transform duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openKepegawaian"
                         x-cloak
                         x-transition
                         class="space-y-1 pl-2">
                        <a href="{{ route('superadmin.pegawai.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.pegawai.index')) }}">
                            Data Pegawai
                        </a>
                        <a href="{{ route('superadmin.pegawai.wajah') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.pegawai.wajah')) }}">
                            Wajah Pegawai
                        </a>
                        <a href="{{ route('superadmin.pegawai.ketidakhadiran') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.pegawai.ketidakhadiran')) }}">
                            Dokumen Ketidakhadiran
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Absensi</p>
                <div x-data="{ openAbsensi: {{ $isAbsensi ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="openAbsensi = !openAbsensi"
                            type="button"
                            class="{{ navClass($isAbsensi) }}">
                        <span>Absensi</span>
                        <svg :class="{ 'rotate-180': openAbsensi }"
                             class="w-4 h-4 transform transition-transform duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openAbsensi"
                         x-cloak
                         x-transition
                         class="space-y-1 pl-2">
                        <a href="{{ route('superadmin.absensi.kategori-jadwal-kerja.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.kategori-jadwal-kerja.*')) }}">
                            Kategori Jadwal Kerja
                        </a>
                        <a href="{{ route('superadmin.absensi.jadwal-kerja.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.jadwal-kerja.*')) }}">
                            Jam Absensi
                        </a>
                        <a href="{{ route('superadmin.absensi.lokasi-absen-instansi.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.lokasi-absen-instansi.*')) }}">
                            Lokasi Absen Instansi
                        </a>
                        <a href="{{ route('superadmin.absensi.lokasi-absen.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.lokasi-absen.*')) }}">
                            Lokasi Absen
                        </a>
                        <a href="{{ route('superadmin.absensi.lokasi-absen-pegawai.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.lokasi-absen-pegawai.*')) }}">
                            Lokasi Absen Pegawai
                        </a>
                        <a href="{{ route('superadmin.absensi.perangkat-pengguna.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.perangkat-pengguna.*')) }}">
                            Perangkat Pengguna
                        </a>
                        <a href="{{ route('superadmin.absensi.mesin.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.mesin.*')) }}">
                            Mesin
                        </a>
                        <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.absensi.lapor-kendala-absensi.*')) }}">
                            Lapor Kendala Absensi
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Laporan & Master</p>
                <div x-data="{ openLaporan: {{ $isLaporan ? 'true' : 'false' }}, openMaster: {{ $isMaster ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="openLaporan = !openLaporan"
                            type="button"
                            class="{{ navClass($isLaporan) }}">
                        <span>Laporan</span>
                        <svg :class="{ 'rotate-180': openLaporan }"
                             class="w-4 h-4 transform transition-transform duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openLaporan"
                         x-cloak
                         x-transition
                         class="space-y-1 pl-2">
                        <a href="{{ route('superadmin.laporan.presensi-harian') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.laporan.presensi-harian')) }}">
                            Presensi Harian
                        </a>
                        <a href="{{ route('superadmin.laporan.presensi-bulanan') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.laporan.presensi-bulanan')) }}">
                            Presensi Bulanan
                        </a>
                        <a href="{{ route('superadmin.laporan.tpp') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.laporan.tpp')) }}">
                            Prosentase TPP
                        </a>
                    </div>
                    <button @click="openMaster = !openMaster"
                            type="button"
                            class="{{ navClass($isMaster) }}">
                        <span>Master</span>
                        <svg :class="{ 'rotate-180': openMaster }"
                             class="w-4 h-4 transform transition-transform duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openMaster"
                         x-cloak
                         x-transition
                         class="space-y-1 pl-2">
                        <a href="{{ route('superadmin.master.instansi.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.master.instansi.*')) }}">
                            Instansi
                        </a>
                        <a href="{{ route('superadmin.master.tipe-dokumen.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.master.tipe-dokumen.*')) }}">
                            Tipe Dokumen
                        </a>
                        <a href="{{ route('superadmin.tipe-pegawai.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.tipe-pegawai.*')) }}">
                            Tipe Pegawai
                        </a>
                        <a href="{{ route('superadmin.master.tipe-kendala.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.master.tipe-kendala.*')) }}">
                            Jenis Kendala
                        </a>
                        <a href="{{ route('superadmin.hari-libur.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.hari-libur.*')) }}">
                            Hari Libur
                        </a>
                        <a href="{{ route('superadmin.master.tpp.index') }}"
                           class="{{ subNavClass(request()->routeIs('superadmin.master.tpp.*')) }}">
                            Data TPP Pegawai
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($isAdmin)
            @php
                $isAdminVerifikasi = request()->routeIs('admin.verifikasi.*');
            @endphp
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Admin</p>
                
                <a href="{{ route('admin.monitoring') }}"
                   class="{{ navClass(request()->routeIs('admin.monitoring')) }}">
                    <span>Monitoring ASN</span>
                </a>

                <a href="{{ route('admin.pegawai') }}"
                   class="{{ navClass(request()->routeIs('admin.pegawai')) }}">
                    <span>Data Pegawai</span>
                </a>

                <div x-data="{ openVerifikasi: {{ $isAdminVerifikasi ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="openVerifikasi = !openVerifikasi"
                            type="button"
                            class="{{ navClass($isAdminVerifikasi) }}">
                        <span>Verifikasi</span>
                        <svg :class="{ 'rotate-180': openVerifikasi }"
                             class="w-4 h-4 transform transition-transform duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openVerifikasi"
                         x-cloak
                         x-transition
                         class="space-y-1 pl-2">
                        <a href="{{ route('admin.verifikasi.izin') }}"
                           class="{{ subNavClass(request()->routeIs('admin.verifikasi.izin')) }}">
                            Verifikasi Izin
                        </a>
                        <a href="{{ route('admin.verifikasi.kendala') }}"
                           class="{{ subNavClass(request()->routeIs('admin.verifikasi.kendala')) }}">
                            Verifikasi Kendala
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.presensi') }}"
                   class="{{ navClass(request()->routeIs('admin.presensi')) }}">
                    <span>Presensi Hari Ini</span>
                </a>
            </div>
        @endif
    </nav>

    <div class="mt-6 rounded-3xl bg-blue-950/90 border border-blue-400/15 p-4 text-slate-200 text-sm">
        <p class="font-semibold text-slate-100">Masuk sebagai</p>
        <p class="mt-1 text-slate-100">{{ $user->name }}</p>
        <p class="mt-1 text-sky-200">{{ ucfirst($user->role ?? '-') }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full rounded-2xl bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600">
                Logout
            </button>
        </form>
    </div>
</div>

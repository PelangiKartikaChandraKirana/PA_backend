<x-app-layout>
    <div class="p-6 space-y-6">

        <div class="rounded-3xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm overflow-hidden border border-sky-100 p-1">
                        <img src="{{ asset('images/logo-kominfo-copy.jpg') }}" alt="Logo Kominfo" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900">Dashboard {{ $department_name }}</h2>
                        <p class="mt-2 text-sm text-slate-600 font-medium tracking-tight">Pengelolaan Presensi & Pegawai • {{ $unit_kerja ?? '-' }}</p>
                    </div>
                </div>
                <div class="rounded-3xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-inner">
                    Waktu Server: {{ $server_time }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-3xl border border-sky-200 bg-white p-6 shadow-sm">
                <p class="text-sm uppercase tracking-[0.3em] text-sky-600">Total Pegawai</p>
                <p class="mt-4 text-3xl font-bold text-slate-900">{{ $total_pegawai }}</p>
            </div>

            <div class="rounded-3xl border border-sky-200 bg-white p-6 shadow-sm">
                <p class="text-sm uppercase tracking-[0.3em] text-sky-600">User Aktif</p>
                <p class="mt-4 text-3xl font-bold text-sky-700">{{ $user_aktif }}</p>
            </div>

            <div class="rounded-3xl border border-sky-200 bg-white p-6 shadow-sm">
                <p class="text-sm uppercase tracking-[0.3em] text-sky-600">Hadir Hari Ini</p>
                <p class="mt-4 text-3xl font-bold text-sky-700">{{ $hadir_hari_ini }}</p>
            </div>

            <div class="rounded-3xl border border-sky-200 bg-white p-6 shadow-sm">
                <p class="text-sm uppercase tracking-[0.3em] text-sky-600">Belum Presensi</p>
                <p class="mt-4 text-3xl font-bold text-sky-700">{{ $belum_presensi }}</p>
            </div>

            <div class="rounded-3xl border border-rose-200 bg-rose-50 p-6 shadow-sm ring-1 ring-rose-100">
                <p class="text-sm uppercase tracking-[0.3em] text-rose-600">Pending Approval</p>
                <p class="mt-4 text-3xl font-bold text-rose-700">{{ $izin_pending }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <a href="{{ route('admin.verifikasi.izin') }}" class="block rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-sm hover:bg-amber-100 transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-amber-600">Izin Pending</p>
                        <p class="mt-4 text-3xl font-bold text-amber-700">{{ $izin_pending }}</p>
                    </div>
                    <div class="p-3 bg-amber-200/50 rounded-2xl">
                        <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="mt-4 text-sm text-slate-600 flex items-center gap-1">
                    Tinjau pengajuan dokumen <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </p>
            </a>

            <a href="{{ route('admin.verifikasi.kendala') }}" class="block rounded-3xl border border-rose-200 bg-rose-50 p-6 shadow-sm hover:bg-rose-100 transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-rose-600">Kendala Mesin</p>
                        <p class="mt-4 text-3xl font-bold text-rose-700">{{ $kendala_pending }}</p>
                    </div>
                    <div class="p-3 bg-rose-200/50 rounded-2xl">
                        <svg class="w-6 h-6 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <p class="mt-4 text-sm text-slate-600 flex items-center gap-1">
                    Validasi laporan kendala <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </p>
            </a>
        </div>

        <!-- ANALYTICS CHART -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Tren Kehadiran</h3>
                    <p class="text-xs text-slate-500">Statistik kehadiran 7 hari terakhir di unit Anda</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-sky-600 bg-sky-50 px-2 py-1 rounded-md border border-sky-100 uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                        Hadir
                    </span>
                </div>
            </div>
            <div class="h-[250px] w-full">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Menu Cepat</p>
                    <p class="mt-1 text-sm text-slate-600">Akses langsung ke halaman presensi dan pengelolaan.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.presensi') }}"
                       class="rounded-3xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                        Lihat Presensi
                    </a>
                    <a href="{{ route('admin.pegawai') }}"
                       class="rounded-3xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                        Data Pegawai
                    </a>
                    <a href="{{ route('admin.verifikasi.izin') }}"
                       class="rounded-3xl bg-amber-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-amber-700">
                        Verifikasi Izin
                    </a>
                    <a href="{{ route('admin.verifikasi.kendala') }}"
                       class="rounded-3xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">
                        Verifikasi Kendala
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const dailyData = @json($daily_trends);
            
            const labels = dailyData.map(item => item.day);
            const data = dailyData.map(item => item.count);

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(14, 165, 233, 0.4)');
            gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Hadir',
                        data: data,
                        borderColor: '#0ea5e9',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0ea5e9',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            displayColors: false,
                            callbacks: {
                                label: (context) => `Total Hadir: ${context.raw}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                stepSize: 1,
                                color: '#64748b',
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

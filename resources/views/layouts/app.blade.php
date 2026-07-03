<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SPPA') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900">
<div class="min-h-screen flex bg-slate-100">

    {{-- SIDEBAR --}}
    @include('layouts.navigation')

    {{-- CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOP BAR --}}
        <div class="relative z-50 bg-white/90 border-b border-slate-200/80 shadow-sm px-6 py-4 backdrop-blur-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-slate-800">Dashboard</h1>
                    <p class="text-sm text-slate-500">Akses cepat dan ringkas ke semua modul admin.</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-sm text-slate-600 hidden md:block">
                        <span id="clock" class="font-medium"></span>
                    </div>

                    {{-- NOTIFICATION BELL --}}
                    @php
                        $unreadNotifications = auth()->user()->unreadNotifications;
                        $unreadCount = $unreadNotifications->count();
                    @endphp
                    <div x-data="{ 
                            open: false,
                            unread: {{ $unreadCount }},
                            markRead() {
                                if (this.unread > 0) {
                                    fetch('{{ route('notifications.mark-as-read') }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    this.unread = 0;
                                }
                            }
                        }" class="relative">
                        <button @click="open = !open; if(open) markRead();" class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="unread > 0" x-cloak x-text="unread > 9 ? '9+' : unread" class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white">
                            </span>
                        </button>

                        <div x-show="open" 
                             x-cloak
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-[100] origin-top-right">
                            <div class="px-4 py-2 border-b border-slate-50 flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-800">Notifikasi</span>
                                <div class="flex items-center gap-2">
                                    <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse($unreadNotifications as $notification)
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                        <div class="flex gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-{{ $notification->data['color'] ?? 'blue' }}-100 flex items-center justify-center text-{{ $notification->data['color'] ?? 'blue' }}-600">
                                                <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-800 leading-snug">{{ $notification->data['message'] }}</p>
                                                <p class="text-xs text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-12 text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">Tidak ada notifikasi baru</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- USER AVATAR --}}
                    <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="flex flex-col items-end hidden lg:flex">
                            <span class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">{{ auth()->user()->role }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-blue-600 shadow-md shadow-blue-100 flex items-center justify-center text-white font-bold ring-2 ring-white">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <main class="p-6 overflow-y-auto">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

</div>

<script>
    function updateClock() {
        const now = new Date();
        const formatted = now.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('clock').innerText = formatted;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Notifikasi Toast untuk Notifikasi Baru
    @if(session('notification'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: "{{ session('notification') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    // Logic untuk memantau notifikasi baru (Polling sederhana)
    let lastNotificationCount = {{ auth()->user()->unreadNotifications->count() }};
    
    function checkNotifications() {
        fetch('{{ route('notifications.count') }}')
            .then(response => response.json())
            .then(data => {
                if (data.count > lastNotificationCount) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Ada pengajuan izin baru!',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                            toast.addEventListener('click', () => {
                                window.location.href = "{{ auth()->user()->role === 'superadmin' ? route('superadmin.pegawai.ketidakhadiran') : route('admin.verifikasi.izin') }}";
                            })
                        }
                    });
                    // Refresh halaman atau update UI jika diperlukan
                    // Untuk saat ini cukup beri tahu admin agar dia refresh sendiri atau klik toast
                }
                lastNotificationCount = data.count;
            });
    }

    // Jalankan polling setiap 30 detik
    setInterval(checkNotifications, 30000);
</script>

</body>
</html>

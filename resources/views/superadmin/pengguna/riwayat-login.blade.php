@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Riwayat Login Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">
                Daftar aktivitas login dan aksi akun untuk pengguna
                <span class="font-semibold text-slate-700">{{ $user->name }}</span>.
            </p>
        </div>

        <a href="{{ route('superadmin.pengguna.index') }}"
           class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Nama</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->name }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Username</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->username }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Role</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->role }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->status }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-700">Log Aktivitas</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Device</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Keterangan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $logs instanceof \Illuminate\Pagination\LengthAwarePaginator ? $logs->firstItem() + $loop->index : $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $log->created_at ? $log->created_at->format('d-m-Y H:i:s') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                {{ $log->action ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if(($log->status ?? '') === 'success')
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                        success
                                    </span>
                                @elseif(($log->status ?? '') === 'failed')
                                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                        failed
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                        {{ $log->status ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $log->device ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $log->description ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada riwayat login / aktivitas untuk pengguna ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
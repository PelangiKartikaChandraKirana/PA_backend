@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Lokasi Absen Pegawai (Override)</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola pengecualian titik koordinat khusus untuk pegawai individu di luar aturan instansi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Top Metrics & Quick Info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-5 shadow-sm ring-1 ring-indigo-200/20">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-indigo-600 p-3 text-white shadow-lg shadow-indigo-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-600">Total Lokasi Master</p>
                    <h3 class="text-2xl font-black text-indigo-800">{{ $locations->count() }} <span class="text-sm font-medium text-indigo-400">Titik</span></h3>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 rounded-2xl border border-amber-100 bg-amber-50/50 p-5 shadow-sm ring-1 ring-amber-200/20 flex items-center gap-4">
            <div class="rounded-xl bg-amber-500 p-3 text-white shadow-lg shadow-amber-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-800">Mekanisme Override Aktif</h4>
                <p class="mt-0.5 text-xs text-amber-700 leading-relaxed italic border-l-2 border-amber-200 pl-3">
                    Jika lokasi manual dipilih untuk pegawai di bawah ini, maka lokasi default instansinya akan diabaikan oleh sistem aplikasi.
                </p>
            </div>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md">
        <form method="GET" class="flex flex-col gap-4 p-5 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Cari Nama / NIP Pegawai</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Ketik nama atau NIP..."
                           class="w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all bg-gray-50/30">
                </div>
            </div>
            <div class="w-full md:w-64">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Berdasarkan OPD / Unit</label>
                <select name="unit_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 appearance-none bg-white">
                    <option value="">-- Semua Unit --</option>
                    @foreach($units ?? [] as $u)
                        <option value="{{ $u->id }}" @selected((string)$unitId === (string)$u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-black active:scale-95 transition-all">
                    Filter
                </button>
                @if($search || $unitId)
                    <a href="{{ route('superadmin.absensi.lokasi-absen-pegawai.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 active:scale-95 transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Table & Bulk Action --}}
    <form id="bulkForm" method="POST" action="{{ route('superadmin.absensi.lokasi-absen-pegawai.bulk-store') }}">
        @csrf
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            
            {{-- Bulk Controls Header (Hidden by default, shown when items selected) --}}
            <div id="bulkControls" class="hidden border-b border-indigo-100 bg-indigo-50/80 px-6 py-3 items-center justify-between transition-all">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-bold text-white" id="selectedCount">0</span>
                    <span class="text-sm font-bold text-indigo-800 tracking-tight text-opacity-80 uppercase">Pegawai Terpilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openBulkModal()" 
                            class="rounded-lg bg-indigo-700 px-4 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-800 active:scale-95 transition-all">
                        Assign Lokasi Masal
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-4 text-left w-10">
                                <input type="checkbox" id="selectAll" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-4 text-left font-bold uppercase tracking-wider text-gray-400 text-[11px]">Pegawai & Unit</th>
                            <th class="px-4 py-4 text-center font-bold uppercase tracking-wider text-gray-400 text-[11px]">Status Override</th>
                            <th class="px-4 py-4 text-right font-bold uppercase tracking-wider text-gray-400 text-[11px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($employees as $emp)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-4 py-4">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" 
                                           class="employee-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-600 font-bold uppercase border border-gray-200 shadow-inner">
                                            {{ substr($emp->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 tracking-tight">{{ $emp->name }}</div>
                                            <div class="flex items-center gap-2 text-[10px] uppercase font-bold text-gray-400">
                                                <span>{{ $emp->nip ?? 'TANPA NIP' }}</span>
                                                <span class="text-gray-200">•</span>
                                                <span class="text-indigo-500">{{ $emp->department->name ?? 'UNIT TIDAK SET' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($emp->override_locations_count > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700 border border-blue-200 shadow-sm ring-4 ring-white">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            {{ $emp->override_locations_count }} Lokasi Custom
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-300 uppercase italic tracking-widest">Sesuai Instansi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <button type="button" 
                                            onclick="openManageModal({{ $emp->id }}, '{{ addslashes($emp->name) }}')"
                                            class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-4 py-2 text-xs font-bold text-gray-700 shadow-sm hover:border-indigo-400 hover:text-indigo-600 transition-all active:scale-95 group">
                                        Kelola Lokasi
                                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-400 italic">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </form>

</div>

{{-- Modal: Kelola Lokasi (Individual) --}}
<div id="manageModal" class="fixed inset-0 z-50 hidden overflow-y-auto px-4 py-6 sm:px-0 bg-slate-900/40 backdrop-blur-sm">
    <div class="relative mx-auto mt-20 max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-tight">Kustomisasi Lokasi</h3>
                <p class="text-[10px] font-medium text-indigo-500 mt-0.5" id="modalEmpName"></p>
            </div>
            <button onclick="closeModal('manageModal')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('superadmin.absensi.lokasi-absen-pegawai.store') }}">
            @csrf
            <input type="hidden" name="employee_id" id="modalEmpId">
            <div class="p-6">
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider mb-2">Daftar Semua Lokasi Master:</p>
                    @foreach($locations as $loc)
                        <label class="group flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/50 p-4 transition-all hover:bg-indigo-50/50 hover:border-indigo-200 cursor-pointer border-l-4" style="border-left-color: {{ ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'][($loop->index % 5)] }}">
                            <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}" 
                                   class="loc-checkbox h-5 w-5 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm transition-all group-hover:scale-110">
                            <div>
                                <div class="text-[13px] font-bold text-gray-800 tracking-tight transition-colors group-hover:text-indigo-700">{{ $loc->name }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('manageModal')" class="rounded-xl border border-gray-300 bg-white px-5 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50 shadow-sm transition-all active:scale-95">Batal</button>
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">Simpan Pengecualian</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Bulk Assign (Masal) --}}
<div id="bulkModal" class="fixed inset-0 z-50 hidden overflow-y-auto px-4 py-6 sm:px-0 bg-slate-900/40 backdrop-blur-sm">
    <div class="relative mx-auto mt-20 max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="border-b border-gray-100 bg-indigo-50/50 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-tight">Kustomisasi Masal</h3>
                <p class="text-[10px] font-medium text-indigo-500 mt-0.5">Menugaskan lokasi ke semua pegawai terpilih</p>
            </div>
            <button onclick="closeModal('bulkModal')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-5">
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 text-[11px] text-amber-700 flex gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Peringatan: Aksi ini akan mengganti seluruh riwayat lokasi custom pegawai yang Anda centang dengan pilihan baru di bawah.
            </div>

            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar" id="bulkLocationList">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pilih Lokasi yang Dituju:</p>
                @foreach($locations as $loc)
                    <label class="group flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/50 p-3 transition-all hover:bg-indigo-50/30 cursor-pointer">
                        <input type="checkbox" name="bulk_location_ids[]" value="{{ $loc->id }}" 
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-[12px] font-bold text-gray-700">{{ $loc->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 flex justify-end gap-3">
            <button type="button" onclick="closeModal('bulkModal')" class="rounded-xl border border-gray-300 bg-white px-5 py-2 text-sm font-bold text-gray-600">Batal</button>
            <button type="button" onclick="submitBulk()" class="rounded-xl bg-indigo-700 px-6 py-2 text-sm font-bold text-white shadow-md hover:bg-indigo-800 transition-all">Terapkan Masal</button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    const bulkControls = document.getElementById('bulkControls');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkUI() {
        const count = document.querySelectorAll('.employee-checkbox:checked').length;
        if (count > 0) {
            bulkControls.classList.remove('hidden');
            bulkControls.classList.add('flex');
            selectedCount.innerText = count;
        } else {
            bulkControls.classList.add('hidden');
            bulkControls.classList.remove('flex');
        }
    }

    selectAll.addEventListener('change', (e) => {
        checkboxes.forEach(cb => cb.checked = e.target.checked);
        updateBulkUI();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkUI);
    });

    async function openManageModal(empId, empName) {
        document.getElementById('modalEmpName').innerText = empName;
        document.getElementById('modalEmpId').value = empId;
        
        // Reset checkboxes
        document.querySelectorAll('.loc-checkbox').forEach(cb => cb.checked = false);
        
        // Loading state can be added here
        
        try {
            const response = await fetch(`{{ url('superadmin/absensi/lokasi-absen-pegawai') }}/${empId}`);
            const data = await response.json();
            
            if (data.active_location_ids) {
                data.active_location_ids.forEach(id => {
                    const cb = document.querySelector(`.loc-checkbox[value="${id}"]`);
                    if (cb) cb.checked = true;
                });
            }
            
            document.getElementById('manageModal').classList.remove('hidden');
        } catch (error) {
            alert('Gagal mengambil data lokasi pegawai. Silakan coba lagi.');
        }
    }

    function openBulkModal() {
        document.getElementById('bulkModal').classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function submitBulk() {
        const bulkForm = document.getElementById('bulkForm');
        // Pindahkan lokasi yang dipilih masal ke form utama agar terbaca di PHP
        const selectedLocs = document.querySelectorAll('input[name="bulk_location_ids[]"]:checked');
        
        if (selectedLocs.length === 0) {
            alert('Silakan pilih minimal satu lokasi untuk ditugaskan masal.');
            return;
        }

        // Hapus input lokasi masal lama jika ada
        const oldInputs = bulkForm.querySelectorAll('.bulk-loc-input');
        oldInputs.forEach(i => i.remove());

        // Tambahkan input hidden baru dari pilihan di modal
        selectedLocs.forEach(loc => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'location_ids[]'; // disesuaikan dengan bulkStore controller
            input.className = 'bulk-loc-input';
            input.value = loc.value;
            bulkForm.appendChild(input);
        });

        // Add replace=true default for bulk
        const replaceInput = document.createElement('input');
        replaceInput.type = 'hidden';
        replaceInput.name = 'replace';
        replaceInput.value = '1';
        bulkForm.appendChild(replaceInput);

        bulkForm.submit();
    }
</script>
@endsection
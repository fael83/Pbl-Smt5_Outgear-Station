<div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm overflow-hidden space-y-4">
    <!-- Bar Pencarian & Tab Status -->
    <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Tab Filter Status -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
            @php
            $tabStatus = [
                ['kunci' => '', 'label' => 'Semua'],
                ['kunci' => 'diambil', 'label' => 'Diambil'],
                ['kunci' => 'dikembalikan', 'label' => 'Dikembalikan'],
            ];
        @endphp
            @foreach($tabStatus as $tab)
                <button type="button" @click="pilihStatus('{{ $tab['kunci'] }}')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-xl transition whitespace-nowrap {{ ($filters['status'] ?? '') === $tab['kunci'] ? 'bg-[#1A2024] text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <!-- Input Pencarian -->
        <div class="w-full sm:w-64">
            <input type="text" x-model.debounce.300ms="cari" @input="muatTabel()"
                   placeholder="Cari nama KTP / nomor KTP..."
                   class="w-full bg-zinc-50 border border-zinc-200/80 rounded-xl px-4 py-2 text-xs text-zinc-900 focus:ring-2 focus:ring-[#15AEEA] focus:outline-none">
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 text-zinc-400 text-[11px] uppercase tracking-wider font-bold border-b border-zinc-200/60">
                    <th class="py-3 px-6">ID / Booking</th>
                    <th class="py-3 px-6">Nama Sesuai KTP</th>
                    <th class="py-3 px-6">Nomor KTP (NIK)</th>
                    <th class="py-3 px-6">Waktu Serah Terima</th>
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 text-xs text-zinc-700 font-medium">
                @forelse($daftarPersiapan as $item)
                    <tr class="hover:bg-zinc-50/50 transition">
                        <td class="py-4 px-6 font-mono font-bold text-zinc-900">#{{ $item->id }}</td>
                        <td class="py-4 px-6 font-bold text-zinc-900">{{ $item->nama_ktp }}</td>
                        <td class="py-4 px-6 font-mono text-zinc-500">{{ $item->nomor_ktp }}</td>
                        <td class="py-4 px-6 text-zinc-600">{{ $item->waktu_serah_terima }}</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ strtolower($item->status_serah_terima) == 'dikembalikan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ ucfirst($item->status_serah_terima) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button type="button" 
                                    @click="bukaDetail({{ $item->load(['detailSerahTerima.detailPemesanan.alat', 'petugas']) }})"
                                    class="px-3 py-1.5 rounded-xl bg-[#15AEEA]/10 text-[#15AEEA] hover:bg-[#15AEEA]/20 transition text-[11px] font-bold inline-flex items-center gap-1">
                                    Detail Alat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-zinc-400 italic">Belum ada data serah terima yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi & Info -->
    @if(method_exists($daftarPersiapan, 'hasPages') && $daftarPersiapan->hasPages())
        <div class="p-4 border-t border-zinc-100 flex items-center justify-between">
            <div class="text-xs text-zinc-500">
                Menampilkan {{ $daftarPersiapan->firstItem() ?? 0 }} - {{ $daftarPersiapan->lastItem() ?? 0 }} dari {{ $daftarPersiapan->total() }} data
            </div>
            <div>
                {{ $daftarPersiapan->links() }}
            </div>
        </div>
    @endif
</div>
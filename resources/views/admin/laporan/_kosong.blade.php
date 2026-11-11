{{-- Baris kosong tabel laporan. Variabel: $kolom (jumlah kolom), $adaPencarian. --}}
<tr>
    <td colspan="{{ $kolom }}" class="p-10 text-center">
        <p class="text-3xl">📊</p>
        <p class="mt-2 text-sm font-semibold text-[#1A2024]">
            {{ $adaPencarian ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data pada periode ini.' }}
        </p>
        <p class="mt-1 text-xs text-gray-500">
            {{ $adaPencarian ? 'Coba ubah kata kunci atau rentang tanggal.' : 'Ubah rentang tanggal untuk melihat periode lain.' }}
        </p>
    </td>
</tr>

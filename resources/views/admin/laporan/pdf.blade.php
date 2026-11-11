<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a2024; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f4f4f5; text-align: left; padding: 6px 8px; border-bottom: 1px solid #d4d4d8; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border-bottom: 1px solid #ececee; }
        .kosong { text-align: center; color: #888; padding: 20px; }
    </style>
</head>
<body>
    <h1>OutGear Station &mdash; {{ $judul }}</h1>
    <div class="meta">Periode {{ $periode }} &middot; Dicetak {{ $dicetakPada }}</div>

    <table>
        <thead>
            <tr>@foreach ($kolom as $nama)<th>{{ $nama }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @forelse ($baris as $isi)
                <tr>@foreach ($isi as $sel)<td>{{ $sel }}</td>@endforeach</tr>
            @empty
                <tr><td class="kosong" colspan="{{ count($kolom) }}">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

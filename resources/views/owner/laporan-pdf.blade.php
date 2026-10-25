
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penyewaan OutGear Station</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .tanggal {
            color: #666;
            margin-bottom: 20px;
        }

        .ringkasan {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .ringkasan td {
            border: 1px solid #ddd;
            padding: 10px;
            width: 25%;
        }

        .label {
            color: #666;
            margin-bottom: 6px;
        }

        .nilai {
            font-size: 14px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }

        table.data th {
            background: #f3f4f6;
        }

        .footer {
            margin-top: 15px;
            color: #666;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <h1>Laporan Penyewaan OutGear Station</h1>

    <p class="tanggal">
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </p>

    <table class="ringkasan">
        <tr>
            <td>
                <div class="label">Total Pemesanan</div>
                <div class="nilai">
                    {{ number_format($ringkasan['total_pemesanan'], 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="label">Pemesanan Selesai</div>
                <div class="nilai">
                    {{ number_format($ringkasan['pemesanan_selesai'], 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="label">Menunggu Pembayaran</div>
                <div class="nilai">
                    {{ number_format($ringkasan['menunggu_pembayaran'], 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="label">Pembayaran Berhasil</div>
                <div class="nilai">
                    Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <h3>Data Penyewaan</h3>

    <table class="data">
        <thead>
            <tr>
                <th>Kode Pemesanan</th>
                <th>Pelanggan</th>
                <th>Tanggal Sewa</th>
                <th>Total Biaya</th>
                <th>Status Pemesanan</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($pemesanan as $item)
                <tr>
                    <td>{{ $item->kode_pemesanan }}</td>
                    <td>{{ $item->pelanggan?->name ?? '-' }}</td>
                    <td>
                        {{ $item->tanggal_mulai?->format('d-m-Y') ?? '-' }}
                        s/d
                        {{ $item->tanggal_selesai?->format('d-m-Y') ?? '-' }}
                    </td>
                    <td>
                        Rp {{ number_format($item->total_biaya ?? 0, 0, ',', '.') }}
                    </td>
                    <td>
                        {{ ucfirst(str_replace('_', ' ', $item->status ?? '-')) }}
                    </td>
                    <td>
                        {{ $item->pembayaran->contains('status_transaksi', 'berhasil')
                            ? 'Berhasil'
                            : 'Belum Berhasil' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada data penyewaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        Laporan ini dibuat secara otomatis oleh sistem OutGear Station.
        Pendapatan merupakan akumulasi pembayaran berhasil, bukan keuntungan bersih.
    </p>
</body>
</html>

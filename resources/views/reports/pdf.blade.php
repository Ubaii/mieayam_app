<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - MIE AYAM WENGI'57</title>
    <style>
        @page { margin: 45px 50px 65px 50px; }
        * { margin: 0; padding: 0; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #111; line-height: 1.45; }

        table { width: 100%; border-collapse: collapse; }
        .right { text-align: right; }
        .center { text-align: center; }

        /* Kop laporan */
        .letterhead td { vertical-align: bottom; padding-bottom: 10px; border-bottom: 2px solid #111; }
        .brand { font-size: 17px; font-weight: bold; letter-spacing: 0.5px; }
        .brand-sub { font-size: 9px; color: #555; margin-top: 2px; }
        .doc-title { font-size: 13px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; }

        /* Meta */
        .meta { margin-top: 12px; margin-bottom: 22px; }
        .meta td { padding: 2px 0; font-size: 10px; vertical-align: top; }
        .meta .k { width: 70px; color: #555; }
        .meta .sep { width: 10px; color: #555; }

        /* Ringkasan angka */
        .kpi { margin-bottom: 26px; border-top: 1px solid #111; border-bottom: 1px solid #111; }
        .kpi td { width: 25%; padding: 12px 10px; border-right: 1px solid #ccc; vertical-align: top; }
        .kpi td:first-child { padding-left: 0; }
        .kpi td:last-child { border-right: none; }
        .kpi .label { font-size: 8px; color: #555; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
        .kpi .value { font-size: 14px; font-weight: bold; }

        /* Section */
        .section { margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }

        /* Tabel data */
        .data th { text-align: left; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.6px; color: #333; padding: 6px 8px; border-top: 1px solid #111; border-bottom: 1px solid #111; }
        .data td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        .data tr.total td { border-top: 1px solid #111; border-bottom: 2px solid #111; font-weight: bold; padding-top: 7px; padding-bottom: 7px; }
        .data th.right, .data td.right { text-align: right; }
        .data th.center, .data td.center { text-align: center; }

        .empty { padding: 14px 0; color: #777; font-style: italic; border-top: 1px solid #111; border-bottom: 1px solid #ddd; }

        .note { margin-top: 6px; font-size: 8.5px; color: #666; }

        /* Footer */
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; border-top: 1px solid #bbb; padding-top: 6px; font-size: 8px; color: #666; }
    </style>
</head>
<body>

    <div class="footer">
        MIE AYAM WENGI'57 &mdash; Laporan Penjualan &nbsp;|&nbsp; Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $pdf->page_text(480, 807, "Hal. {PAGE_NUM} dari {PAGE_COUNT}", $font, 8, array(0.4, 0.4, 0.4));
        }
    </script>

    {{-- KOP --}}
    <table class="letterhead">
        <tr>
            <td>
                <div class="brand">MIE AYAM WENGI'57</div>
                <div class="brand-sub">Restaurant Management System</div>
            </td>
            <td class="right">
                <div class="doc-title">Laporan Penjualan</div>
            </td>
        </tr>
    </table>

    {{-- META --}}
    <table class="meta">
        <tr>
            <td class="k">Periode</td>
            <td class="sep">:</td>
            <td>{{ $from->translatedFormat('d F Y') }} s/d {{ $to->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="k">Dicetak</td>
            <td class="sep">:</td>
            <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    {{-- RINGKASAN --}}
    <table class="kpi">
        <tr>
            <td>
                <div class="label">Total Pendapatan</div>
                <div class="value">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Total Transaksi</div>
                <div class="value">{{ number_format($count, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Item Terjual</div>
                <div class="value">{{ number_format($itemsSold, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Rata-rata / Transaksi</div>
                <div class="value">Rp {{ number_format($average, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- 1. METODE PEMBAYARAN --}}
    <div class="section">
        <div class="section-title">1. Pendapatan per Metode Pembayaran</div>
        @if($paymentMethods->isNotEmpty())
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 40%;">Metode</th>
                    <th class="center" style="width: 25%;">Jumlah Transaksi</th>
                    <th class="right" style="width: 35%;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentMethods->take(2) as $method)
                <tr>
                    <td>{{ $method->payment_method }}</td>
                    <td class="center">{{ number_format($method->transactions_count, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($method->revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total">
                    <td>Total</td>
                    <td class="center">{{ number_format($paymentMethods->sum('transactions_count'), 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($paymentMethods->sum('revenue'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <div class="empty">Tidak ada data pembayaran pada periode ini.</div>
        @endif
    </div>

    {{-- 2. MENU TERLARIS --}}
    <div class="section">
        <div class="section-title">2. Menu Terlaris</div>
        @if($bestSellers->isNotEmpty())
        <table class="data">
            <thead>
                <tr>
                    <th class="center" style="width: 8%;">No</th>
                    <th style="width: 44%;">Nama Menu</th>
                    <th class="center" style="width: 18%;">Terjual</th>
                    <th class="right" style="width: 30%;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bestSellers->take(2) as $index => $menu)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $menu->name }}</td>
                    <td class="center">{{ number_format($menu->quantity, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($menu->revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Tidak ada data menu pada periode ini.</div>
        @endif
    </div>

    {{-- 3. RINGKASAN HARIAN --}}
    <div class="section">
        <div class="section-title">3. Ringkasan Harian</div>
        @if(count($sales))
        <table class="data">
            <thead>
                <tr>
                    <th class="center" style="width: 8%;">No</th>
                    <th style="width: 52%;">Tanggal</th>
                    <th class="right" style="width: 40%;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales->take(7) as $i => $day)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $day['label'] }}</td>
                    <td class="right">Rp {{ number_format($day['revenue'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total">
                    <td></td>
                    <td>Total</td>
                    <td class="right">Rp {{ number_format(collect($sales)->sum('revenue'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <div class="empty">Tidak ada data penjualan harian pada periode ini.</div>
        @endif
        <div class="note">Data hanya mencakup transaksi dengan status selesai.</div>
    </div>

</body>
</html>
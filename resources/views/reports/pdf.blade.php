<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pergerakan Stok</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1a202c;
            background: #fff;
        }

        /* ─────────────────────────────────────────────
           HEADER
        ───────────────────────────────────────────── */
        .header-wrap {
            background: #2c3e7a;
            padding: 0;
            margin-bottom: 0;
        }

        .header-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            padding: 18px 24px;
            vertical-align: middle;
            width: 55%;
        }

        .header-right {
            padding: 18px 24px;
            vertical-align: middle;
            text-align: right;
            width: 45%;
            background: #1a2a5e;
        }

        .brand-icon {
            font-size: 26px;
            color: #f6c90e;
            display: block;
            margin-bottom: 4px;
        }

        .brand-name {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 9px;
            color: #a0b3e0;
            margin-top: 2px;
        }

        .report-type {
            font-size: 9px;
            color: #a0b3e0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
        }

        .report-generated {
            font-size: 8.5px;
            color: #a0b3e0;
            margin-top: 5px;
        }

        /* Accent bar bawah header */
        .header-accent {
            height: 4px;
            background: linear-gradient(to right, #f6c90e 0%, #f39c12 40%, #2980b9 100%);
            margin-bottom: 18px;
        }

        /* ─────────────────────────────────────────────
           META INFO
        ───────────────────────────────────────────── */
        .meta-row {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 16px 0;
            padding: 0 24px;
        }

        .meta-box {
            background: #f0f4ff;
            border: 1px solid #c7d4f0;
            border-radius: 4px;
            padding: 8px 14px;
            font-size: 9px;
            color: #4a5568;
        }

        .meta-box strong {
            color: #2c3e7a;
        }

        /* ─────────────────────────────────────────────
           SUMMARY CARDS (table-based)
        ───────────────────────────────────────────── */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #718096;
            margin: 0 24px 6px;
        }

        .stats-table {
            width: calc(100% - 48px);
            margin: 0 24px 18px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .stat-cell {
            text-align: center;
            padding: 12px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .stat-cell.blue   { background: #ebf4ff; border-left: 4px solid #3182ce; }
        .stat-cell.green  { background: #f0fff4; border-left: 4px solid #38a169; }
        .stat-cell.red    { background: #fff5f5; border-left: 4px solid #e53e3e; }
        .stat-cell.yellow { background: #fffff0; border-left: 4px solid #d69e2e; }
        .stat-cell.purple { background: #faf5ff; border-left: 4px solid #805ad5; }

        .stat-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #718096;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
        }

        .stat-value.blue   { color: #2b6cb0; }
        .stat-value.green  { color: #276749; }
        .stat-value.red    { color: #c53030; }
        .stat-value.yellow { color: #975a16; }
        .stat-value.purple { color: #553c9a; }

        /* ─────────────────────────────────────────────
           TABLE
        ───────────────────────────────────────────── */
        .table-wrap {
            margin: 0 24px 24px;
        }

        .table-header-bar {
            background: #2c3e7a;
            color: white;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
            letter-spacing: 0.3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: #3d5a9e;
        }

        .data-table thead th {
            padding: 8px 10px;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-right: 1px solid #4a6baf;
        }

        .data-table thead th:last-child {
            border-right: none;
        }

        .data-table tbody tr.even { background: #ffffff; }
        .data-table tbody tr.odd  { background: #f7f9ff; }

        .data-table tbody td {
            padding: 7px 10px;
            font-size: 9.5px;
            border-bottom: 1px solid #e8edf5;
            border-right: 1px solid #eef0f6;
            vertical-align: middle;
        }

        .data-table tbody td:last-child {
            border-right: none;
        }

        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-in  { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .badge-out { background: #fed7d7; color: #742a2a; border: 1px solid #feb2b2; }

        .qty-in  { color: #276749; font-weight: bold; }
        .qty-out { color: #c53030; font-weight: bold; }

        .no-col   { text-align: center; color: #718096; font-size: 9px; }
        .unit-txt { color: #718096; font-size: 8.5px; }
        .name-bold { font-weight: bold; color: #2d3748; }

        /* Empty state */
        .empty-row td {
            text-align: center;
            padding: 28px;
            color: #a0aec0;
            font-style: italic;
            background: #fafafa;
        }

        /* ─────────────────────────────────────────────
           FOOTER
        ───────────────────────────────────────────── */
        .footer-wrap {
            margin: 0 24px;
            border-top: 2px solid #2c3e7a;
            padding-top: 12px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-note {
            font-size: 8.5px;
            color: #718096;
            vertical-align: bottom;
        }

        .footer-note .total-count {
            font-weight: bold;
            color: #2c3e7a;
            font-size: 10px;
        }

        .footer-note .auto-generated {
            margin-top: 3px;
            color: #a0aec0;
        }

        .signature-area {
            text-align: center;
            vertical-align: bottom;
            width: 180px;
        }

        .signature-space {
            height: 40px;
            border-bottom: 1px solid #4a5568;
            width: 160px;
            margin: 0 auto 4px;
        }

        .signature-name {
            font-size: 8.5px;
            color: #4a5568;
            font-weight: bold;
        }

        .signature-role {
            font-size: 8px;
            color: #718096;
        }

        .footer-brand {
            text-align: right;
            vertical-align: bottom;
            font-size: 8px;
            color: #a0aec0;
        }

        /* Divider section */
        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 14px 24px;
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════ HEADER ═══════════════════════════════ --}}
<div class="header-wrap">
    <table class="header-inner">
        <tr>
            <td class="header-left">
                <span class="brand-icon">&#127859;</span>
                <div class="brand-name">Inventaris F&amp;B</div>
                <div class="brand-sub">Sistem Manajemen Inventaris Food &amp; Beverage</div>
            </td>
            <td class="header-right">
                <div class="report-type">Laporan Resmi</div>
                <div class="report-period">
                    @if($bulan && $tahun)
                        Pergerakan Stok — {{ \Carbon\Carbon::create($tahun, $bulan)->locale('id')->translatedFormat('F Y') }}
                    @else
                        Pergerakan Stok — Semua Periode
                    @endif
                </div>
                <div class="report-generated">
                    Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                    &nbsp;|&nbsp; Oleh: {{ Auth::user()->name }}
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="header-accent"></div>

{{-- ═══════════════════════════════ META INFO ═══════════════════════════════ --}}
<div style="padding: 0 24px; margin-bottom: 14px;">
    <div class="meta-box">
        <strong>Filter Aktif:</strong>
        Periode:
        @if($bulan && $tahun)
            <strong>{{ \Carbon\Carbon::create($tahun, $bulan)->locale('id')->translatedFormat('F Y') }}</strong>
        @else
            <strong>Semua Periode</strong>
        @endif
        &nbsp;|&nbsp;
        Bahan Baku: <strong>{{ $ingredient_name ?? 'Semua Bahan Baku' }}</strong>
        &nbsp;|&nbsp;
        Pengguna: <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})
    </div>
</div>

{{-- ═══════════════════════════════ RINGKASAN STATISTIK ═══════════════════════════════ --}}
<div class="section-title">&#9642; Ringkasan Statistik</div>
<table class="stats-table">
    <tr>
        <td class="stat-cell blue">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value blue">{{ $movements->count() }}</div>
        </td>
        <td class="stat-cell green">
            <div class="stat-label">Jml. Stok Masuk</div>
            <div class="stat-value green">{{ $movements->where('type', 'in')->count() }}</div>
        </td>
        <td class="stat-cell red">
            <div class="stat-label">Jml. Stok Keluar</div>
            <div class="stat-value red">{{ $movements->where('type', 'out')->count() }}</div>
        </td>
        <td class="stat-cell yellow">
            <div class="stat-label">Total Qty Masuk</div>
            <div class="stat-value yellow">{{ number_format($movements->where('type','in')->sum('quantity'), 2) }}</div>
        </td>
        <td class="stat-cell purple">
            <div class="stat-label">Total Qty Keluar</div>
            <div class="stat-value purple">{{ number_format($movements->where('type','out')->sum('quantity'), 2) }}</div>
        </td>
    </tr>
</table>

{{-- ═══════════════════════════════ TABEL DETAIL ═══════════════════════════════ --}}
<div class="table-wrap">
    <div class="table-header-bar">
        &#9776;&nbsp; Detail Pergerakan Stok
        ({{ $movements->count() }} transaksi)
    </div>

    @if($movements->isEmpty())
        <table class="data-table">
            <tr class="empty-row">
                <td colspan="7">Tidak ada data pergerakan stok untuk periode ini.</td>
            </tr>
        </table>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:4%; text-align:center;">#</th>
                    <th style="width:14%;">Waktu</th>
                    <th style="width:20%;">Bahan Baku</th>
                    <th style="width:9%; text-align:center;">Jenis</th>
                    <th style="width:11%; text-align:right;">Jumlah</th>
                    <th style="width:7%; text-align:center;">Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $i => $movement)
                <tr class="{{ $i % 2 === 0 ? 'even' : 'odd' }}">
                    <td class="no-col">{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('d/m/Y') }}<br>
                        <span style="color:#a0aec0; font-size:8px;">{{ \Carbon\Carbon::parse($movement->created_at)->format('H:i') }}</span>
                    </td>
                    <td class="name-bold">{{ $movement->ingredient->name }}</td>
                    <td style="text-align:center;">
                        @if($movement->type === 'in')
                            <span class="badge badge-in">&#8595; Masuk</span>
                        @else
                            <span class="badge badge-out">&#8593; Keluar</span>
                        @endif
                    </td>
                    <td style="text-align:right;" class="{{ $movement->type === 'in' ? 'qty-in' : 'qty-out' }}">
                        {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                    </td>
                    <td style="text-align:center;" class="unit-txt">{{ $movement->ingredient->unit }}</td>
                    <td style="color:#4a5568;">{{ $movement->description ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ═══════════════════════════════ FOOTER ═══════════════════════════════ --}}
<div class="footer-wrap">
    <table class="footer-table">
        <tr>
            <td class="footer-note">
                <div class="total-count">{{ $movements->count() }} transaksi ditemukan</div>
                <div class="auto-generated">
                    Dokumen ini digenerate otomatis oleh Sistem Inventaris F&amp;B pada
                    {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB.
                    Dokumen ini sah tanpa stempel basah.
                </div>
            </td>
            <td class="signature-area">
                <div class="signature-space"></div>
                <div class="signature-name">{{ Auth::user()->name }}</div>
                <div class="signature-role">{{ ucfirst(Auth::user()->role) }} — Inventaris F&amp;B</div>
            </td>
            <td class="footer-brand">
                <div style="color:#2c3e7a; font-weight:bold; font-size:10px;">&#127859; Inventaris F&amp;B</div>
                <div>Sistem Manajemen Inventaris</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>

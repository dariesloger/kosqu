@extends('layouts.app')

@section('content')
<style>
    .report-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
        position: relative;
    }

    /* Hover lebih stabil tanpa bikin layout goyang */
    .report-card:hover {
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: #CBD5E1;
    }

    .widget {
        overflow: hidden;
        background: white;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .icon-primary {
        background: #EFF6FF;
        color: #2563EB;
    }

    .icon-success {
        background: #ECFDF5;
        color: #059669;
    }

    .icon-warning {
        background: #FFFBEB;
        color: #D97706;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1E293B;
        margin-top: 0.25rem;
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    /* Hover tabel lebih smooth */
    .data-table tbody tr:hover {
        background: #F8FAFC;
    }

    .data-table th {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .data-table td {
        vertical-align: middle;
    }

    @media print {
        .sidebar,
        .dashboard-title p,
        .filter-section,
        .no-print {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .report-card,
        .widget {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="dashboard-title"
    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
    <div>
        <h1>Laporan Keuangan</h1>
        <p>Ringkasan pendapatan dan performa bisnis kos Anda.</p>
    </div>

    <button onclick="window.print()" class="no-print"
        style="padding: 0.75rem 1.5rem; background: #1E293B; color: white; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fa-solid fa-print"></i>
        Cetak Laporan
    </button>
</div>

<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

    <div class="report-card">
        <div class="card-icon icon-primary">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div class="stat-label">Pendapatan Bulan Ini</div>

        <div class="stat-value">
            Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
        </div>
    </div>

    <div class="report-card">
        <div class="card-icon icon-success">
            <i class="fa-solid fa-calendar-check"></i>
        </div>

        <div class="stat-label">Pendapatan Tahun Ini</div>

        <div class="stat-value">
            Rp {{ number_format($pendapatanTahunIni, 0, ',', '.') }}
        </div>
    </div>

    <div class="report-card">
        <div class="card-icon icon-warning">
            <i class="fa-solid fa-wallet"></i>
        </div>

        <div class="stat-label">Total Pendapatan</div>

        <div class="stat-value">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </div>
    </div>
</div>

<div class="widget filter-section" style="margin-bottom: 2rem; padding: 1.5rem;">
    <form action="{{ route('laporan.index') }}" method="GET"
        style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">

        <div style="min-width: 150px;">
            <label
                style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; margin-bottom: 0.5rem; text-transform: uppercase;">
                Pilih Tahun
            </label>

            <select name="year" onchange="this.form.submit()"
                style="width: 100%; padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid #E2E8F0; outline: none; font-size: 0.9rem; background: white;">

                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 150px;">
            <label
                style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; margin-bottom: 0.5rem; text-transform: uppercase;">
                Pilih Bulan
            </label>

            <select name="month" onchange="this.form.submit()"
                style="width: 100%; padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid #E2E8F0; outline: none; font-size: 0.9rem; background: white;">

                <option value="">Semua Bulan</option>

                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <a href="{{ route('laporan.index') }}"
            style="padding: 0.75rem 1.5rem; border-radius: 10px; background: #F1F5F9; color: #475569; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            Reset Filter
        </a>
    </form>
</div>

<div class="widget">
    <div
        style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0;">

        <h3 style="font-weight: 800; color: #1E293B;">
            Rincian Transaksi Valid
        </h3>

        <span
            style="font-size: 0.85rem; color: #64748B; font-weight: 600; background: #F1F5F9; padding: 0.25rem 0.75rem; border-radius: 20px;">

            {{ $transaksis->count() }} Transaksi
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid #E2E8F0;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Tanggal</th>
                    <th style="padding: 1rem;">Penghuni</th>
                    <th style="padding: 1rem;">Unit</th>
                    <th style="padding: 1rem;">Bulan Tagihan</th>
                    <th style="padding: 1rem;">Jumlah</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transaksis as $t)
                    <tr style="border-bottom: 1px solid #F1F5F9;">
                        <td style="padding: 1rem; font-weight: 600; color: #64748B;">
                            #TRX-{{ $t->id }}
                        </td>

                        <td style="padding: 1rem;">
                            {{ $t->created_at->format('d/m/Y') }}
                        </td>

                        <td style="padding: 1rem;">
                            <div style="font-weight: 700; color: #1E293B;">
                                {{ $t->penghuni->nama }}
                            </div>
                        </td>

                        <td style="padding: 1rem;">
                            <span
                                style="font-weight: 600; color: #2563EB; background: #EFF6FF; padding: 0.2rem 0.5rem; border-radius: 6px;">
                                {{ $t->penghuni->kamar->nomor_kamar }}
                            </span>
                        </td>

                        <td style="padding: 1rem;">
                            {{ $t->bulan_tagihan }}
                        </td>

                        <td style="padding: 1rem; font-weight: 700; color: #059669;">
                            Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            style="text-align: center; padding: 4rem 2rem; color: #94A3B8;">

                            <i class="fa-solid fa-receipt"
                                style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>

                            <p>Tidak ada transaksi valid ditemukan untuk periode ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if($transaksis->isNotEmpty())
                <tfoot>
                    <tr style="background: #F8FAFC; font-weight: 800;">
                        <td colspan="5"
                            style="padding: 1rem; text-align: right; border-top: 1px solid #E2E8F0;">
                            TOTAL PERIODE INI
                        </td>

                        <td
                            style="padding: 1rem; color: #059669; border-top: 1px solid #E2E8F0;">
                            Rp {{ number_format($transaksis->sum('jumlah_bayar'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
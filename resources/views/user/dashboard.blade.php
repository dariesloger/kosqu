@extends('layouts.user')

@section('content')
<div class="dashboard-title">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Halo, {{ $penghuni->nama }}!</h1>
            <p>Berikut adalah ringkasan status hunian Anda bulan ini.</p>
        </div>
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;">
            <i class="fa-solid fa-bed"></i>
        </div>
        <div class="stat-info">
            <h3>STATUS HUNIAN</h3>
            <div class="value">Kamar {{ $kamar->nomor_kamar ?? '-' }}</div>
            <div style="font-size: 0.75rem; margin-top: 0.4rem; color: {{ $penghuni->status == 'Aktif' ? 'var(--success)' : '#EF4444' }}; font-weight: 600;">
                <i class="fa-regular {{ $penghuni->status == 'Aktif' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i> {{ $penghuni->status }}
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #FFFBEB; color: #D97706;">
            <i class="fa-regular fa-calendar"></i>
        </div>
        <div class="stat-info">
            <h3>JATUH TEMPO BERIKUTNYA</h3>
            <div class="value">{{ $tglJatuhTempo ? $tglJatuhTempo->translatedFormat('d M Y') : '-' }}</div>
            <div style="font-size: 0.75rem; margin-top: 0.4rem; color: {{ $sisaHari !== null && $sisaHari <= 3 ? '#DC2626' : 'var(--text-muted)' }}; font-weight: 600;">
                <i class="fa-regular fa-clock"></i> {{ $sisaHari !== null ? ($sisaHari >= 0 ? $sisaHari . ' Hari Lagi' : abs($sisaHari) . ' Hari Terlewat') : 'Belum diatur' }}
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: {{ $statusPembayaran === 'Lunas' ? '#ECFDF5' : ($statusPembayaran === 'Cicilan' ? '#FFFBEB' : '#FEF2F2') }}; color: {{ $statusPembayaran === 'Lunas' ? '#10B981' : ($statusPembayaran === 'Cicilan' ? '#D97706' : '#DC2626') }};">
            <i class="fa-solid {{ $statusPembayaran === 'Lunas' ? 'fa-check-double' : ($statusPembayaran === 'Cicilan' ? 'fa-hourglass-half' : 'fa-circle-exclamation') }}"></i>
        </div>
        <div class="stat-info">
            <h3>STATUS PEMBAYARAN</h3>
            <div class="value" style="color: {{ $statusPembayaran === 'Lunas' ? '#10B981' : ($statusPembayaran === 'Cicilan' ? '#D97706' : '#DC2626') }};">
                {{ $statusPembayaran }}
            </div>
            @if($statusPembayaran === 'Cicilan')
            <div style="margin-top: 0.5rem;">
                <div style="height: 6px; background: #E2E8F0; border-radius: 3px; overflow: hidden;">
                    <div style="width: {{ $progressPersen }}%; height: 100%; background: linear-gradient(90deg, #D97706, #F59E0B); border-radius: 3px;"></div>
                </div>
                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.3rem; font-weight: 600;">
                    Rp {{ number_format($totalDibayar, 0, ',', '.') }} / Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                </div>
            </div>
            @elseif($statusPembayaran === 'Lunas')
            <div style="font-size: 0.75rem; margin-top: 0.4rem; color: var(--text-muted); font-weight: 600;">
                Terima kasih! Semua tagihan beres.
            </div>
            @else
            <div style="font-size: 0.75rem; margin-top: 0.4rem; color: var(--text-muted); font-weight: 600;">
                Silakan lakukan pembayaran segera.
            </div>
            @endif
        </div>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">
    
    <div class="widget" style="width: 100%; display: flex; flex-direction: column;">
        <div class="widget-header">
            <div class="widget-title">Transaksi Aktif</div>
            <i class="fa-solid fa-receipt" style="color: var(--text-muted);"></i>
        </div>
        
        <div style="flex-grow: 1; display: flex; flex-direction: column;">
            @if($tagihanAktif)
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0 0 0.2rem 0; font-size: 1.1rem; font-weight: 700; color: var(--primary);">Tagihan {{ $tagihanAktif->bulan_tagihan }}</h3>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">ID Transaksi: #TRX-{{ $tagihanAktif->id }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">Rp {{ number_format($tagihanAktif->jumlah_bayar, 0, ',', '.') }}</div>
                    <div style="font-size: 0.75rem; font-weight: 600; color: #D97706;">Menunggu Validasi</div>
                </div>
            </div>

            <div style="background: #F8FAFC; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.85rem;">
                    <span style="color: var(--text-muted);">Biaya Sewa Kamar</span>
                    <span style="font-weight: 600;">Rp {{ number_format($kamar->harga_sewa ?? 0, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                    <span style="color: var(--text-muted);">Total Tagihan</span>
                    <span style="font-weight: 600;">Rp {{ number_format($tagihanAktif->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: auto;">
                <button class="btn-primary" style="flex: 2; justify-content: center; padding: 0.8rem 1rem;" disabled>
                    <i class="fa-solid fa-clock"></i> Sedang Diperiksa
                </button>
                <a href="{{ route('user.invoice') }}" style="flex: 1; background: #FFF; border: 1px solid #E2E8F0; color: var(--text-muted); font-weight: 600; border-radius: 8px; cursor: pointer; transition: 0.2s; text-decoration: none; display: flex; align-items: center; justify-content: center; padding: 0.8rem 1rem;">
                    Detail
                </a>
            </div>
            @else
            <div style="text-align: center; padding: 2rem 1rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; background: #F1F5F9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #CBD5E1; font-size: 1.5rem;">
                    <i class="fa-solid fa-file-circle-check"></i>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--primary);">Tidak Ada Transaksi Aktif</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Semua pembayaran Anda sudah divalidasi.</div>
                </div>
                @if(!$sudahBayarBulanIni)
                <a href="{{ route('user.payment') }}" class="btn-primary" style="margin-top: 1rem; text-decoration: none;">
                    <i class="fa-solid fa-plus"></i> Buat Laporan Bayar
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="widget" style="width: 100%;">
        <div class="widget-header">
            <div class="widget-title">Pengumuman Kos</div>
            <i class="fa-solid fa-bullhorn" style="color: var(--text-muted);"></i>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($pengumumans as $p)
            <div style="display: flex; gap: 1rem; background: #F8FAFC; padding: 1.25rem; border-radius: 12px; border: 1px solid #F1F5F9;">
                <div style="width: 44px; height: 44px; background: {{ $p->warna_bg }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: {{ $p->warna_ikon }}; flex-shrink: 0;">
                    <i class="fa-solid {{ $p->ikon }}"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.3rem;">{{ $p->created_at->translatedFormat('d M Y') }}</div>
                    <h4 style="margin: 0 0 0.4rem 0; font-size: 1rem; font-weight: 700; color: var(--primary);">{{ $p->judul }}</h4>
                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                        {{ $p->konten }}
                    </p>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem;">
                Belum ada pengumuman terbaru.
            </div>
            @endforelse
        </div>

        @if($totalPengumuman > 10)
        <div style="border-top: 1px solid #E2E8F0; margin-top: 1.5rem; padding-top: 1rem; text-align: center;">
            <a href="{{ route('user.pengumuman') }}" style="font-size: 0.8rem; font-weight: 600; color: var(--secondary); text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                Lihat Semua Pengumuman ({{ $totalPengumuman }}) <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        @endif
    </div>
    
</div>
@endsection
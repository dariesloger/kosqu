@forelse($transaksis as $trx)
<tr style="border-bottom: 1px solid #F1F5F9;">
    <td style="padding: 1rem 0.5rem;">
        <span style="font-weight: 700; color: var(--primary); font-family: 'Courier New', monospace;">#TRX-{{ $trx->id }}</span>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->penghuni->nama ?? 'N/A' }}</div>
        <div style="font-size: 0.7rem; color: var(--text-muted);">Kamar {{ $trx->penghuni->kamar->nomor_kamar ?? '-' }}</div>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="font-size: 0.9rem; font-weight: 500;">{{ $trx->bulan_tagihan }}</div>
        <div style="font-size: 0.7rem; color: var(--text-muted);">Dibuat: {{ $trx->created_at->format('d/m/Y') }}</div>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="font-weight: 700; color: #10B981;">Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}</div>
    </td>
    <td style="padding: 1rem 0.5rem;">
        @if($trx->bukti_transfer && str_contains($trx->bukti_transfer, '/'))
            <button onclick="viewProof('{{ asset('storage/' . $trx->bukti_transfer) }}', '#TRX-{{ $trx->id }}')" class="btn-proof">
                <i class="fa-solid fa-image"></i> Lihat Bukti
            </button>
        @else
            <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Tidak ada bukti</span>
        @endif
    </td>
    <td style="padding: 1rem 0.5rem;">
        @php
            $badgeClass = 'badge-warning';
            if($trx->status_validasi == 'Valid') $badgeClass = 'badge-success';
            if($trx->status_validasi == 'Ditolak') $badgeClass = 'badge-danger';
        @endphp
        <span class="badge {{ $badgeClass }}">{{ $trx->status_validasi }}</span>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="display: flex; justify-content: center; gap: 0.5rem;">
            @if($trx->status_validasi == 'Pending')
                <form action="{{ route('transaksi.validasi', $trx->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="status" value="Valid">
                    <button type="submit" class="btn-accept" title="Terima Pembayaran">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </form>
                <form action="{{ route('transaksi.validasi', $trx->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="status" value="Ditolak">
                    <button type="submit" class="action-btn" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" title="Tolak Pembayaran">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </form>
            @else
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Selesai</span>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" style="text-align: center; padding: 4rem 2rem;">
        <i class="fa-solid fa-receipt" style="font-size: 3rem; color: #CBD5E1; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
        <p style="color: var(--text-muted); font-weight: 500;">Tidak ada data transaksi ditemukan.</p>
    </td>
</tr>
@endforelse

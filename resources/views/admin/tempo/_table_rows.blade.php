@php
    $now = \Carbon\Carbon::now();
@endphp
@forelse($penghunis as $p)
@php
    $tempo = \Carbon\Carbon::parse($p->tgl_jatuh_tempo);
    $daysLeft = (int) $now->diffInDays($tempo, false);
    
    $badgeClass = 'badge-success';
    $statusText = 'Aman';
    
    if($daysLeft < 0) {
        $badgeClass = 'badge-danger';
        $statusText = 'Terlambat';
    } elseif($daysLeft <= 7) {
        $badgeClass = 'badge-warning';
        $statusText = 'Mendatang';
    }

    $periodeLabel = ($p->tempo_periode ?: 1) . ' Bln';
    if($p->tempo_periode == 6) $periodeLabel = '6 Bln (Semester)';
    if($p->tempo_periode == 12) $periodeLabel = '12 Bln (Tahun)';
@endphp
<tr style="border-bottom: 1px solid #F1F5F9;">
    <td style="padding: 1rem 0.5rem;">
        <div style="width: 40px; height: 40px; background: #EFF6FF; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #2563EB;">
            {{ $p->kamar->nomor_kamar ?? '?' }}
        </div>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="font-weight: 700; color: var(--text-main);">{{ $p->nama }}</div>
        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $p->no_hp }}</div>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="font-weight: 600;">{{ $tempo->translatedFormat('d F Y') }}</div>
    </td>
    <td style="padding: 1rem 0.5rem;">
        @if($daysLeft < 0)
            <span style="color: #DC2626; font-weight: 700;">Lewat {{ abs($daysLeft) }} Hari</span>
        @elseif($daysLeft == 0)
            <span style="color: #D97706; font-weight: 700;">Hari Ini</span>
        @else
            <span style="font-weight: 600; color: var(--text-main);">{{ $daysLeft }} Hari Lagi</span>
        @endif
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="font-weight: 700; color: var(--primary);">Rp {{ number_format($p->sisaTagihan(), 0, ',', '.') }}</div>
        @if($p->jumlah_tagihan)
            <div style="font-size: 0.65rem; color: #059669; font-weight: 600; text-transform: uppercase;">Custom Rate</div>
        @endif
    </td>
    <td style="padding: 1rem 0.5rem;">
        <span class="tempo-badge">
            <i class="fa-solid fa-repeat" style="font-size: 0.55rem;"></i>
            {{ $periodeLabel }}
        </span>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
    </td>
    <td style="padding: 1rem 0.5rem;">
        <div style="display: flex; justify-content: center; gap: 0.5rem;">
            {{-- Send WA via Fonnte --}}
            @if($fonnteConfigured ?? false)
            <form action="{{ route('tempo.send-reminder', $p->id) }}" method="POST" class="wa-reminder-form" style="display: inline;">
                @csrf
                <button type="submit" class="btn-action-wa" title="Kirim Pengingat WA">
                    <i class="fa-brands fa-whatsapp"></i>
                </button>
            </form>
            @else
            <a href="https://wa.me/{{ $p->no_hp }}?text=Halo%20{{ $p->nama }},%20kami%20dari%20pengelola%20KOSQU%20mengingatkan%20bahwa%20masa%20sewa%20Anda%20unit%20{{ $p->kamar->nomor_kamar }}%20akan%20jatuh%20tempo%20pada%20{{ $tempo->format('d/m/Y') }}.%20Terima%20kasih." 
               target="_blank" class="btn-action-wa" title="Kirim WhatsApp (Manual)">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
            @endif

            <button type="button" class="btn-action-bill" title="Atur Tagihan & Tempo" 
                    onclick="openBillModal('{{ $p->id }}', '{{ $p->nama }}', '{{ $p->tgl_jatuh_tempo }}', '{{ $p->jumlah_tagihan ?? $p->kamar->harga_sewa }}', '{{ $p->tempo_periode ?: ($defaultTempo ?? 1) }}')">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" style="text-align: center; padding: 4rem 2rem;">
        <i class="fa-solid fa-calendar-day" style="font-size: 3rem; color: #CBD5E1; opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
        <p style="color: var(--text-muted); font-weight: 600;">Tidak ada data jatuh tempo ditemukan.</p>
    </td>
</tr>
@endforelse

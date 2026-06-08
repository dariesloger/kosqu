@extends('layouts.app')

@section('content')
<style>
    .booking-form .form-group {
        margin-bottom: 1.5rem;
    }
    .booking-form .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748B;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .booking-form .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        outline: none;
        font-size: 0.95rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: white;
    }
    .booking-form .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    .booking-form .form-hint {
        font-size: 0.7rem;
        color: #94A3B8;
        margin-top: 0.4rem;
    }
    .kamar-card {
        padding: 1rem;
        border: 2px solid #E2E8F0;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    .kamar-card:hover {
        border-color: var(--primary);
        background: #F0F9FF;
    }
    .kamar-card.selected {
        border-color: var(--primary);
        background: #EFF6FF;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .kamar-card input[type="radio"] {
        display: none;
    }
</style>

<div class="dashboard-title">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <a href="{{ route('booking.index') }}" style="width: 40px; height: 40px; background: #F1F5F9; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #475569; transition: all 0.2s;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1>Buat Booking Baru</h1>
            <p>Reservasi kamar untuk calon penghuni.</p>
        </div>
    </div>
</div>

<form action="{{ route('booking.store') }}" method="POST" class="booking-form">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        {{-- Left: Room Selection --}}
        <div class="widget" style="padding: 2rem;">
            <h3 style="font-weight: 800; color: #1E293B; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-bed" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Pilih Kamar
            </h3>

            @if($kamars->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem;">
                @foreach($kamars as $kamar)
                <label class="kamar-card" id="kamar-label-{{ $kamar->id }}">
                    <input type="radio" name="id_kamar" value="{{ $kamar->id }}" {{ old('id_kamar') == $kamar->id ? 'checked' : '' }} onchange="selectKamar(this)" required>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $kamar->nomor_kamar }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">Rp {{ number_format($kamar->harga_sewa, 0, ',', '.') }}</div>
                    @if($kamar->fasilitas)
                    <div style="font-size: 0.65rem; color: #94A3B8; margin-top: 0.5rem; line-height: 1.4;">{{ Str::limit($kamar->fasilitas, 30) }}</div>
                    @endif
                </label>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 3rem 1rem; color: #94A3B8;">
                <i class="fa-solid fa-bed" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                <p style="font-weight: 600;">Tidak ada kamar tersedia saat ini.</p>
            </div>
            @endif

            @error('id_kamar')
            <p style="color: #DC2626; font-size: 0.8rem; margin-top: 0.75rem; font-weight: 600;"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- Right: Tenant Info --}}
        <div class="widget" style="padding: 2rem;">
            <h3 style="font-weight: 800; color: #1E293B; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-user-plus" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Data Calon Penghuni
            </h3>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" placeholder="Masukkan nama lengkap..." required>
                @error('nama')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>NIK (Nomor Induk Kependudukan)</label>
                <input type="text" name="nik" value="{{ old('nik') }}" class="form-input" pattern="\d{16}" maxlength="16" minlength="16" required title="Harus 16 digit angka" placeholder="16 Digit NIK" onchange="this.value = this.value.replace(/[^0-9]/g, '')">
                @error('nik')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-input" placeholder="08xxxxxxxxxx" required onchange="{
                        this.value = this.value.replace(/[^0-9]/g, '') 
                        if (this.value.startsWith('62')) {
                            this.value = '0' + this.value.slice(2)
                        }
                    }" >
                    @error('no_hp')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Password Login</label>
                    <input type="text" name="password" value="{{ old('password') }}" class="form-input" placeholder="Min. 6 karakter" required>
                    <p class="form-hint">Password untuk login penghuni nanti.</p>
                    @error('password')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Tanggal Rencana Masuk</label>
                <input type="date" name="tgl_rencana_masuk" value="{{ old('tgl_rencana_masuk') }}" class="form-input" required>
                @error('tgl_rencana_masuk')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Periode Pembayaran (Sewa)</label>
                    <select name="tempo_periode" class="form-input" required>
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan (Triwulan)</option>
                        <option value="6">6 Bulan (Semester)</option>
                        <option value="12">12 Bulan (Tahunan)</option>
                    </select>
                    @error('tempo_periode')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
                </div>
                
                <div class="form-group">
                    <label>Jumlah DP / Uang Muka (Rp)</label>
                    <input type="number" name="jumlah_dp" class="form-input" min="0" required id="dp-input">
                    <p class="form-hint">Jika kurang dari total harga sewa periode tersebut, sisanya akan ditagih.</p>
                    @error('jumlah_dp')<p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Catatan (Opsional)</label>
                <textarea name="catatan" class="form-input" rows="3" placeholder="Catatan tambahan, misal: mahasiswa UGM, kontrak 6 bulan...">{{ old('catatan') }}</textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('booking.index') }}" style="flex: 1; padding: 0.85rem; background: #F1F5F9; color: #475569; border-radius: 12px; text-align: center; text-decoration: none; font-weight: 700; transition: all 0.2s;">
                    Batal
                </a>
                <button type="submit" style="flex: 2; padding: 0.85rem; background: var(--primary); color: white; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 0.95rem;" {{ $kamars->count() == 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-calendar-check"></i> Buat Booking
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function selectKamar(radio) {
        document.querySelectorAll('.kamar-card').forEach(card => card.classList.remove('selected'));
        radio.closest('.kamar-card').classList.add('selected');
    }

    // Restore selection on validation error
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[name="id_kamar"]:checked');
        if (checked) checked.closest('.kamar-card').classList.add('selected');
    });
</script>
@endsection

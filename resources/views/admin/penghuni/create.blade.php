@extends('layouts.app')

@section('content')
<div class="dashboard-title">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Tambah Penghuni</h1>
            <p>Daftarkan penghuni baru ke dalam sistem Wisma AAM.</p>
        </div>
        <a href="{{ route('penghuni.index') }}" class="btn-primary" style="background: #FFF; color: var(--text-muted); border: 1px solid #E2E8F0; text-decoration: none; box-shadow: none;">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

<div class="widget" style="max-width: 900px;">
    <div class="widget-header" style="border-bottom: 1px solid #E2E8F0; padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div class="widget-title" style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 36px; height: 36px; background: #E0F2FE; color: #0284C7; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-id-card"></i>
            </div>
            Informasi Personal & Hunian
        </div>
    </div>

    <form action="{{ route('penghuni.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Pilih Kamar</label>
                    <select name="id_kamar" required style="width: 100%; box-sizing: border-box; padding: 0.8rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; outline: none;">
                        <option value="" disabled selected>Pilih Kamar Tersedia</option>
                        @foreach($kamars as $k)
                            <option value="{{ $k->id }}">{{ $k->nomor_kamar }} - (Rp {{ number_format($k->harga_sewa, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    @error('id_kamar') <div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Tanggal Masuk</label>
                    <input type="date" name="tgl_masuk" required style="width: 100%; box-sizing: border-box; padding: 0.8rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; outline: none;">
                    @error('tgl_masuk') <div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Nama sesuai KTP" style="width: 100%; box-sizing: border-box; padding: 0.8rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; outline: none;">
                    @error('nama') <div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">NIK</label>
                        <input type="text" name="nik" pattern="\d{16}" maxlength="16" minlength="16" required title="Harus 16 digit angka" placeholder="16 Digit NIK" onchange="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 100%; box-sizing: border-box; padding: 0.8rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; outline: none;">
                        @error('nik') <div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">No. HP</label>
                        <input type="text" name="no_hp" required placeholder="08xxxxxxxxxx" onchange="{
                            this.value = this.value.replace(/[^0-9]/g, '') 
                            if (this.value.startsWith('62')) {
                                this.value = '0' + this.value.slice(2)
                            }
                        }" style="width: 100%; box-sizing: border-box; padding: 0.8rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; outline: none;">
                        @error('no_hp') <div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 1.5rem; margin-top: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 32px; height: 32px; background: #E0F2FE; color: #0284C7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3 style="margin: 0; font-size: 1rem; color: var(--primary);">Keamanan Akun</h3>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Password (Untuk Login Penghuni)</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" style="width: 100%; box-sizing: border-box; padding: 0.8rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #FFF; outline: none;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;"><i class="fa-solid fa-circle-info" style="color: #06B6D4; margin-right: 0.25rem;"></i> Gunakan NIK atau kata sandi unik untuk penghuni.</p>
                @error('password') <div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary" style="flex: 2; padding: 1rem; justify-content: center; font-size: 1rem;">
                <i class="fa-solid fa-save"></i>
                Simpan Data Penghuni
            </button>
            <a href="{{ route('penghuni.index') }}" style="flex: 1; display: flex; align-items: center; justify-content: center; background: #FFF; border: 1px solid #E2E8F0; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-weight: 600; transition: 0.2s;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
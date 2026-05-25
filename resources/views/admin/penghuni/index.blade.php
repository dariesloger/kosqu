@extends('layouts.app')

@section('content')
<div class="dashboard-title">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>Data Penghuni</h1>
            <p>Kelola informasi seluruh penghuni Wisma AAM secara efisien.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            @if($trashedCount > 0)
            <a href="{{ route('penghuni.trashed') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: #FEF2F2; color: #DC2626; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 0.85rem; border: 1px solid #FECACA; transition: all 0.2s;">
                <i class="fa-solid fa-trash-can-arrow-up"></i>
                Arsip Terhapus
                <span style="background: #DC2626; color: white; padding: 0.15rem 0.5rem; border-radius: 20px; font-size: 0.7rem; font-weight: 800;">{{ $trashedCount }}</span>
            </a>
            @endif
            <a href="{{ route('penghuni.create') }}" class="btn-primary" style="text-decoration: none;">
                <i class="fa-solid fa-user-plus"></i>
                Tambah Penghuni
            </a>
        </div>
    </div>
</div>

{{-- Stats Summary --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>TOTAL PENGHUNI</h3>
            <div class="value" id="stat-total">{{ $penghunis->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #ECFDF5; color: #10B981;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="stat-info">
            <h3>STATUS AKTIF</h3>
            <div class="value" style="color: #10B981;" id="stat-aktif">{{ $penghunis->where('status', 'Aktif')->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #FEF2F2; color: #DC2626;">
            <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div class="stat-info">
            <h3>STATUS KELUAR</h3>
            <div class="value" style="color: #DC2626;" id="stat-keluar">{{ $penghunis->where('status', 'Keluar')->count() }}</div>
        </div>
    </div>
</div>

{{-- Search & Filter Bar --}}
<div class="widget" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        {{-- Search Input --}}
        <div style="flex: 2; min-width: 250px; position: relative;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.85rem;"></i>
            <input type="text" id="search-input" placeholder="Cari nama, NIK, atau No. HP..."
                   style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border-radius: 12px; border: 1px solid #E2E8F0; outline: none; font-size: 0.9rem; transition: border-color 0.2s, box-shadow 0.2s; background: #F8FAFC;"
                   onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 4px rgba(37,99,235,0.08)';"
                   onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none';">
        </div>

        {{-- Status Filter --}}
        <div style="min-width: 160px;">
            <select id="filter-status" style="width: 100%; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #E2E8F0; outline: none; font-size: 0.85rem; background: white; cursor: pointer; font-weight: 600; color: #475569;">
                <option value="Semua">Semua Status</option>
                <option value="Aktif">✅ Aktif</option>
                <option value="Keluar">🚪 Keluar</option>
            </select>
        </div>

        {{-- Kamar Filter --}}
        <div style="min-width: 160px;">
            <select id="filter-kamar" style="width: 100%; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #E2E8F0; outline: none; font-size: 0.85rem; background: white; cursor: pointer; font-weight: 600; color: #475569;">
                <option value="Semua">Semua Kamar</option>
                @foreach($kamars as $kamar)
                <option value="{{ $kamar->id }}">🛏️ Unit {{ $kamar->nomor_kamar }}</option>
                @endforeach
            </select>
        </div>

        {{-- Reset Button --}}
        <button type="button" id="btn-reset" onclick="resetFilters()" style="padding: 0.75rem 1.25rem; border-radius: 12px; border: 1px solid #E2E8F0; background: white; color: #64748B; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;"
                onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#CBD5E1';"
                onmouseout="this.style.background='white'; this.style.borderColor='#E2E8F0';">
            <i class="fa-solid fa-rotate-right"></i> Reset
        </button>
    </div>
</div>

{{-- Data Table --}}
<div class="widget">
    <div class="widget-header">
        <div class="widget-title" style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 36px; height: 36px; background: var(--primary-light); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-address-book"></i>
            </div>
            Daftar Penghuni
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div id="loading-indicator" style="display: none;">
                <i class="fa-solid fa-spinner fa-spin" style="color: var(--primary); font-size: 1rem;"></i>
            </div>
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;" id="data-count">
                {{ $penghunis->count() }} data
            </span>
        </div>
    </div>

    <div style="overflow-x: auto; margin: 0 -1.5rem; padding: 0 1.5rem;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kamar</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Kontak</th>
                    <th>Tgl. Masuk</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="penghuni-tbody">
                @include('admin.penghuni._table_rows', ['penghunis' => $penghunis])
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('styles')
<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.9) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    #penghuni-tbody tr {
        animation: fadeIn 0.3s ease-out;
    }
</style>
@endsection

@section('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const filterStatus = document.getElementById('filter-status');
    const filterKamar = document.getElementById('filter-kamar');
    const tbody = document.getElementById('penghuni-tbody');
    const dataCount = document.getElementById('data-count');
    const loadingIndicator = document.getElementById('loading-indicator');

    // Debounced search on input
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchData(), 300);
    });

    // Instant filter on select change
    filterStatus.addEventListener('change', () => fetchData());
    filterKamar.addEventListener('change', () => fetchData());

    function fetchData() {
        const search = searchInput.value.trim();
        const status = filterStatus.value;
        const kamar = filterKamar.value;

        loadingIndicator.style.display = 'block';
        tbody.style.opacity = '0.5';

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status !== 'Semua') params.append('status', status);
        if (kamar !== 'Semua') params.append('kamar', kamar);

        fetch(`{{ route('penghuni.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = data.html;
            dataCount.textContent = data.total + ' data';
            document.getElementById('stat-total').textContent = data.total;
            document.getElementById('stat-aktif').textContent = data.aktif;
            document.getElementById('stat-keluar').textContent = data.keluar;
            loadingIndicator.style.display = 'none';
            tbody.style.opacity = '1';
        })
        .catch(err => {
            console.error('Fetch error:', err);
            loadingIndicator.style.display = 'none';
            tbody.style.opacity = '1';
        });
    }

    function resetFilters() {
        searchInput.value = '';
        filterStatus.value = 'Semua';
        filterKamar.value = 'Semua';
        fetchData();
    }

    function openDeleteModal(actionUrl, name) {
        confirmAction({
            title: 'Hapus Penghuni?',
            text: `Anda akan menghapus data penghuni "${name}". Data bisa dikembalikan dari Arsip.`,
            confirmText: 'Ya, Hapus Data',
            callback: function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = actionUrl;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
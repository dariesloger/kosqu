<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOSQU - Management System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <h2>KOSQU</h2>
            <p>MANAGEMENT SYSTEM</p>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-cells-large"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('kamar.index') }}" class="nav-link {{ request()->routeIs('kamar.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed"></i>
                    Data Kamar
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('penghuni.index') }}" class="nav-link {{ request()->routeIs('penghuni.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-group"></i>
                    Data Penghuni
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('booking.index') }}" class="nav-link {{ request()->routeIs('booking.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Booking
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i>
                    Transaksi & Validasi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pengumuman.index') }}" class="nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                    Pengumuman
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tempo.index') }}" class="nav-link {{ request()->routeIs('tempo.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    Tempo Tagihan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet"></i>
                    Laporan Keuangan
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <li class="nav-item" style="list-style: none;">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item" style="list-style: none;">
                <a href="{{ route('logout') }}" class="nav-link" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left" style="display: flex; align-items: center; flex-grow: 1;">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            
            <div class="header-right">
                <div class="user-profile">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1E293B;">{{ auth('admin')->user()->nama_admin ?? 'Admin' }}</div>
                        <div style="font-size: 0.8rem; color: #94A3B8;">Administrator</div>
                    </div>
                    <img src="{{ asset('images/admin-profile.jpg') }}" alt="Profile" class="profile-img">
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scripts')

    <script>
        // Global SweetAlert Notification Handler
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffffff',
                iconColor: '#10B981',
                customClass: {
                    popup: 'premium-swal-popup'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                background: '#ffffff',
                iconColor: '#EF4444',
                confirmButtonColor: '#2563EB'
            });
        @endif

        // Utility for confirmation dialogs
        window.confirmAction = function(options) {
            Swal.fire({
                title: options.title || 'Apakah Anda yakin?',
                text: options.text || "Tindakan ini tidak dapat dibatalkan!",
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: options.confirmColor || '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: options.confirmText || 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed && options.callback) {
                    options.callback();
                }
            });
        }

        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (mobileToggle && sidebar && sidebarOverlay) {
                function toggleSidebar() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                }

                mobileToggle.addEventListener('click', toggleSidebar);
                sidebarOverlay.addEventListener('click', toggleSidebar);

                // Close sidebar on nav link click in mobile view
                const navLinks = document.querySelectorAll('.nav-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('active');
                            sidebarOverlay.classList.remove('active');
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>

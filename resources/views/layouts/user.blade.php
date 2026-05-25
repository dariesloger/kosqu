<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOSQU - Tenant Portal</title>
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
            <p style="text-transform: capitalize; letter-spacing: 0;">Tenant Portal</p>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ url('/user/dashboard') }}" class="nav-link {{ request()->is('user/dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-cells-large"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.invoice') }}" class="nav-link {{ request()->routeIs('user.invoice') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i>
                    Kwitansi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.payment') }}" class="nav-link {{ request()->routeIs('user.payment') ? 'active' : '' }}">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    Pembayaran
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.history') }}" class="nav-link {{ request()->routeIs('user.history') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Riwayat
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    <i class="fa-regular fa-user"></i>
                    Profil
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <li class="nav-item" style="list-style: none;">
                <a href="{{ route('user.help') }}" class="nav-link {{ request()->routeIs('user.help') ? 'active' : '' }}">
                    <i class="fa-regular fa-circle-question"></i>
                    Pusat Bantuan
                </a>
            </li>
            <li class="nav-item" style="list-style: none;">
                <a href="{{ route('logout') }}" class="nav-link" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Keluar
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
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1E293B;">{{ auth('penghuni')->user()->nama ?? 'Penghuni' }}</div>
                        <div style="font-size: 0.8rem; color: #94A3B8;">Kamar {{ auth('penghuni')->user()->kamar->nomor_kamar ?? '-' }}</div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #0A9396); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.85rem;">{{ strtoupper(substr(auth('penghuni')->user()->nama ?? 'U', 0, 2)) }}</div>
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
        // Global SweetAlert Notification Handler for User
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffffff',
                iconColor: '#10B981'
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

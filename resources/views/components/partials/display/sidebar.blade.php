        <aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
            <div class="sidebar-header">
                <a class="brand-mark" href="{{ route('beranda') }}" aria-label="adminHMD dashboard">
                    <span class="brand-icon"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
                    <span class="brand-copy">
                        <span class="brand-title">adminHMD</span>
                        <span class="brand-subtitle">Admin Template</span>
                    </span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <a class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}" href="{{ route('beranda') }}"
                    aria-current="page">
                    <span class="nav-icon"><i class="bi bi-house"></i></span>
                    <span class="nav-text">Beranda</span>
                </a>
                <a class="nav-link {{ request()->routeIs('instansi') ? 'active' : '' }}" href="{{ route('instansi') }}">
                    <span class="nav-icon"><i class="bi bi-bank" aria-hidden="true"></i></span>
                    <span class="nav-text">Instansi</span>
                </a>
                <a class="nav-link {{ request()->routeIs('divisi') ? 'active' : '' }}" href="{{ route('divisi') }}">
                    <span class="nav-icon"><i class="bi bi-buildings" aria-hidden="true"></i></span>
                    <span class="nav-text">Divisi</span>
                </a>
                <a class="nav-link {{ request()->routeIs('pegawai.index') ? 'active' : '' }}"
                    href="{{ route('pegawai.index') }}">
                    <span class="nav-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    <span class="nav-text">Pegawai</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start text-danger"
                        style="cursor: pointer;">
                        <span class="nav-icon"><i class="bi bi-box-arrow-right text-danger"></i></span>
                        <span class="nav-text">Keluar</span>
                    </button>
                </form>
            </nav>
            <div class="sidebar-user d-flex align-items-center gap-3 px-3 py-3 border-bottom border-light-subtle">
                <!-- Avatar Pengguna dengan efek border/shadow tipis -->
                <div class="flex-shrink-0">
                    <img class="avatar-img avatar-md sidebar-user-avatar rounded-circle shadow-sm object-fit-cover"
                        src="{{ asset('assets/images/avatar/avatar.jpg') }}" alt="Avatar {{ Auth::user()->nama }}"
                        style="width: 45px; height: 45px;">
                </div>

                <!-- Informasi Nama dan Username -->
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="mb-0 text-truncate fw-semibold text-dark" style="font-size: 0.95rem;">
                        {{ Auth::user()->nama }}
                    </h6>
                    <div class="text-muted text-truncate d-block" style="font-size: 0.8rem;">
                        @<span>{{ Auth::user()->username }}</span>
                    </div>
                </div>
            </div>
        </aside>

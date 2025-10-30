<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    {{-- Logo dan Nama Aplikasi --}}
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="{{ route('admin.dashboard.index') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/logo.jpg') }}" alt="logo" width="40" height="40" class="img-fluid rounded-circle">
        </div>
        <div class="sidebar-brand-text mx-3">SPK Moora</div>
    </a>

    <hr class="sidebar-divider my-0">

    @auth
        @can('admin')
            {{-- Dashboard --}}
            <li class="nav-item {{ (request()->routeIs('admin.dashboard.index')) ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            {{-- HEADING: DATA MASTER --}}
            <div class="sidebar-heading">
                DATA MASTER
            </div>

            {{-- Data Master --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#dataMaster" aria-expanded="false"
                    aria-controls="dataMaster">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Master Data</span>
                </a>
                <div id="dataMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pengaturan Data Dasar:</h6>
                        <a class="collapse-item" href="{{ route('admin.major.index') }}">
                            <i class="fas fa-warehouse fa-fw"></i> Jurusan
                        </a>
                        <a class="collapse-item" href="{{ route('admin.subject.index') }}">
                            <i class="fas fa-book fa-fw"></i> Mata Pelajaran
                        </a>
                        <a class="collapse-item" href="{{ route('admin.user.index') }}">
                            <i class="fas fa-users fa-fw"></i> Data User
                        </a>
                    </div>
                </div>
            </li>

            {{-- Data Siswa --}}
            <li class="nav-item {{ (request()->routeIs('admin.student.index')) ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.student.index') }}">
                    <i class="fas fa-fw fa-user-graduate"></i>
                    <span>Data Siswa</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            {{-- HEADING: SPK MOORA --}}
            <div class="sidebar-heading">
                SPK MOORA
            </div>

            {{-- Menu Setup Kriteria & Nilai Rapor --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#spkSetup" aria-expanded="false"
                    aria-controls="spkSetup">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Setup Kriteria & Nilai</span>
                </a>
                <div id="spkSetup" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pengaturan MOORA:</h6>
                        <a class="collapse-item" href="{{ route('admin.criteria.index') }}">
                            <i class="fas fa-list fa-fw"></i> Kriteria & Bobot
                        </a>
                        <a class="collapse-item" href="{{ route('admin.subcriteria.index') }}">
                            <i class="fas fa-stream fa-fw"></i> Sub Kriteria (Skala)
                        </a>
                        <a class="collapse-item" href="{{ route('admin.alternative.index') }}">
                            <i class="fas fa-th-list fa-fw"></i> Alternatif (Mapping)
                        </a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Input Data:</h6>
                        <a class="collapse-item" href="{{ route('admin.rapor.index') }}">
                            <i class="fas fa-book-reader fa-fw"></i> Input Nilai Rapor
                        </a>
                    </div>
                </div>
            </li>

            {{-- Perhitungan MOORA --}}
            <li class="nav-item {{ (request()->routeIs('admin.calculation')) ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.calculation') }}">
                    <i class="fas fa-fw fa-calculator"></i>
                    <span>Hitung MOORA & Ranking</span>
                </a>
            </li>
        @endcan
    @endauth

    {{-- Menu untuk Siswa/User Biasa (Non-Admin) --}}
    @cannot('admin')
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('calculation.user') }}">
            <i class="fas fa-fw fa-bullseye"></i>
            <span>Rekomendasi Jurusan Anda</span>
        </a>
    </li>
    @endcannot

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
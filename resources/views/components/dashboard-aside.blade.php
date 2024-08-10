<style>
    .sidebar-collapse a.brand-link{
        position: relative;
    }
    .sidebar-collapse span.brand-text{
        display: none;
    }
    .sidebar-collapse:hover span.brand-text{
        display: default;
    }
</style>

<script>
    function logout(el,event)
    {
        event.preventDefault();

        Swal.fire({
            title: `Apakah anda yakin keluar?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6E7881',
            confirmButtonText: 'Iya',
            cancelButtonText: 'tutup',
        }).then((result) => {
            if (result.isConfirmed) {
                showLoadingSpinner();
                window.location.replace(`${BASE_URL}/logout`);
            }
        })
    }
</script>

<aside class="main-sidebar sidebar-dark-secondary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link text-center d-flex align-items-center" style="flex-direction: column">
        <img src="{{ asset('images/main-logo.webp') }}" alt="logo dinas sosial dki jakarta" class="img-circle elevation-3" style="opacity: .8; width: 70%">
        <span class="brand-text font-weight-bold mt-4">
            SISTEM INFORMASI <br>
            PILAR-PILAR SOSIAL
        </span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image d-flex align-items-center">
                <img src="{{ asset('images/default-profile.webp') }}" class="img-circle bg-secondary elevation-2" alt="User Image">
            </div>
            <div class="info d-flex ml-1" style="flex-direction: column;">
                <span class="text-white text-bold" style="font-size: 14px;">
                    Hai, {{ ucfirst(explode(" ", $user->name)[0]) }}
                </span>
            </div>
        </div>

        <nav class="user-panel pb-2 pt-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                <li class="nav-item mb-2">
                    <a href="{{ route('dashboard.main') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-home"></i>
                        <p>Beranda</p>
                    </a>
                </li>
                <li class="nav-item mb-2 {{ request()->is('site*') || request()->is('user*') || request()->is('education*') || request()->is('bank*') || request()->is('layanan_lks*') || request()->is('akreditasi_lks*') ? 'menu-open' : '' }}" style="display: {{ $user->level_id == 1 ? 'default' : 'none' }};">
                    <a href="#" class="nav-link mb-2">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            Master
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item mb-2" style="font-size: 0.8em;">
                            <a href="{{ route('user.main') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>User</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div class="mt-3 pb-3 mb-3 d-flex">
            <a href="" class="w-100 text-center py-3 btn btn-danger elevation-3" onclick="logout(this,event)">
                <i class="fa fa-power-off" style="font-size: 1.5em;"></i>
            </a>
        </div>
    </div>
</aside>

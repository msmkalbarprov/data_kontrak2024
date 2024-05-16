<div class="primary-menu">
    <nav class="navbar navbar-expand-lg align-items-center">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="">
                        <img src="{{ asset('template/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
                    </div>
                    <div class="">
                        <h4 class="logo-text">Data Kontrak</h4>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav align-items-center flex-grow-1">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <div class="menu-title d-flex align-items-center">Dashboard</div>
                        </a>
                    </li>
                    @php
                        $menu_tipe1 = filter_menu();
                        $menu_tipe2 = sub_menu();
                    @endphp
                    @foreach ($menu_tipe1 as $tipe1)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                                data-bs-toggle="dropdown">
                                <div class="parent-icon"><i class='bx bx-cube'></i>
                                </div>
                                <div class="menu-title d-flex align-items-center">{{ $tipe1->name }}</div>
                                <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($menu_tipe2 as $tipe2)
                                    @if ($tipe2->parent == $tipe1->uuid)
                                        <li><a class="dropdown-item" href="{{ route($tipe2->link) }}"><i
                                                    class='bx bx-envelope'></i>{{ $tipe2->name }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</div>

<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">





    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0   d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>


    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

        <div class="navbar-nav align-items-center me-auto">
            <div class="nav-item d-flex align-items-center">
               <img width="40" height="40" style="margin-right: 10px" src="../assets/img/layouts/actvn_big_icon.png"> Học viện kỹ thuật mật mã
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-md-auto">


            <!-- Place this tag where you want the button to render. -->
            <li class="nav-item lh-1 me-4">
                <span></span>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="true">
                    <div class="avatar avatar-online">
                        <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                    <small class="text-body-secondary">({{ auth()->user()->getRoleNames()->first() }})</small>
                                    <small class="text-body-secondary">{{ auth()->user()->email }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li>
                        <a href="#" class="dropdown-item" id="btnLogout">
                            <i class="icon-base bx bx-power-off icon-md me-3"></i>
                            <span>Log Out</span>
                        </a>
                    </li>
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                </ul>
            </li>
            <!--/ User -->

        </ul>
    </div>

</nav>

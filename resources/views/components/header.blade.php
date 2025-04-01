<header class="navbar navbar-expand-md navbar-light d-none d-lg-flex">
    <div class="container-xl p-2">
        <div class="row g-2 align-items-center">
            <!-- Page Pre-title and Title -->
            <div class="col">
                <div class="page-pretitle">
                    {{ auth()->user()->role }}
                </div>
                <h2 class="page-title">
                    {{ $title ?? 'Dashboard' }}
                </h2>
            </div>

            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @isset($actionButtons)
                        {{ $actionButtons }}
                    @endisset
                </div>
            </div>
        </div>
    </div>

    <div class="container-xl justify-content-end p-2">
        <div class="navbar-nav flex-row order-md-last me-5">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-color: #206bc4">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }} {{-- Changed name to nama --}}
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->nama }}</div> {{-- Changed name to nama --}}
                        <div class="mt-1 small text-muted">{{ auth()->user()->username }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</header>

<x-layout>
    <x-slot name="title">Panitia</x-slot>
    <x-slot name="card_title">Data Panitia</x-slot>

    {{-- @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif --}}

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('panitia.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Panitia
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%">No</th>
                            <th style="width: 20%">Nama</th>
                            <th style="width: 15%">Unit</th>
                            <th style="width: 20%">Alamat</th>
                            <th style="width: 15%">Telepon</th>
                            <th style="width: 15%">Email</th>
                            <th style="width: 10%">Status</th>
                            <th class="text-center" style="width: 10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list_panitia as $panitia)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $panitia->nama }}</td>
                                <td>{{ $panitia->unit }}</td>
                                <td>{{ $panitia->alamat }}</td>
                                <td>{{ $panitia->no_hp }}</td>
                                <td>{{ $panitia->email }}</td>
                                <td class="text-center">
                                    <span class="badge text-white bg-{{ $panitia->user ? 'success' : 'danger' }}">
                                        {{ $panitia->user ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded"
                                            data-bs-toggle="modal" data-bs-target="#detailModal{{ $panitia->id }}"
                                            data-bs-placement="top" title="Detail">
                                            <i class="fas fa-eye text-info"></i>
                                        </button>


                                        <a href="{{ route('panitia.edit', $panitia->id) }}"
                                            class="btn btn-sm btn-outline-secondary rounded mx-1"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>

                                        <form action="{{ route('panitia.destroy', $panitia->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="detailModal{{ $panitia->id }}" tabindex="-1"
                                aria-labelledby="detailModalLabel{{ $panitia->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-3">
                                        <!-- Modal Header -->
                                        <div
                                            class="modal-header bg-primary bg-gradient text-white border-0 rounded-top">
                                            <h5 class="modal-title fs-5 fw-semibold"
                                                id="detailModalLabel{{ $panitia->id }}">
                                                <i class="fas fa-user-circle me-2"></i>Detail Panitia
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body p-4">
                                            <!-- Profile Section -->
                                            <div class="text-center mb-4">
                                                <div class="avatar avatar-xl mb-3">
                                                    <span
                                                        class="avatar-title rounded-circle bg-primary-subtle text-primary fs-2">
                                                        {{ strtoupper(substr($panitia->nama, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <h5 class="fw-bold mb-1">{{ $panitia->nama }}</h5>
                                                <p class="text-muted small mb-3">{{ $panitia->unit }}</p>
                                                <span
                                                    class="badge bg-{{ $panitia->user ? 'success' : 'danger' }}-subtle text-{{ $panitia->user ? 'success' : 'danger' }} px-3 py-2">
                                                    <i
                                                        class="fas fa-{{ $panitia->user ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                    {{ $panitia->user ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </div>

                                            <!-- Information Cards -->
                                            <div class="row g-3">
                                                <!-- Personal Information -->
                                                <div class="col-12">
                                                    <div class="card border-0 bg-light-subtle rounded-3">
                                                        <div class="card-body p-3">
                                                            <h6
                                                                class="card-title mb-3 d-flex align-items-center text-primary">
                                                                <i class="fas fa-user me-2"></i>
                                                                <span>Informasi Pribadi</span>
                                                            </h6>
                                                            <div class="vstack gap-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="text-muted" style="width: 100px;">Email
                                                                    </div>
                                                                    <div class="text-truncate fw-medium">
                                                                        {{ $panitia->email }}</div>
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="text-muted" style="width: 100px;">
                                                                        Telepon</div>
                                                                    <div class="fw-medium">{{ $panitia->no_hp }}</div>
                                                                </div>
                                                                <div class="d-flex align-items-start">
                                                                    <div class="text-muted" style="width: 100px;">Alamat
                                                                    </div>
                                                                    <div class="fw-medium">{{ $panitia->alamat }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Account Information -->
                                                @if ($panitia->user)
                                                    <div class="col-12">
                                                        <div class="card border-0 bg-light-subtle rounded-3">
                                                            <div class="card-body p-3">
                                                                <h6
                                                                    class="card-title mb-3 d-flex align-items-center text-primary">
                                                                    <i class="fas fa-shield-alt me-2"></i>
                                                                    <span>Informasi Akun</span>
                                                                </h6>
                                                                <div class="vstack gap-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="text-muted" style="width: 100px;">
                                                                            Username</div>
                                                                        <div class="fw-medium">
                                                                            {{ $panitia->user->username }}</div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="text-muted" style="width: 100px;">
                                                                            Role</div>
                                                                        <div class="fw-medium text-capitalize">
                                                                            {{ $panitia->user->role }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                <i class="fas fa-times me-1"></i>
                                                Tutup
                                            </button>
                                            <a href="{{ route('panitia.edit', $panitia->id) }}"
                                                class="btn btn-primary">
                                                <i class="fas fa-edit me-1"></i>
                                                Edit Data
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data panitia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            .modal-content {
                overflow: hidden;
            }

            .btn-group {
                display: inline-flex;
                gap: 4px;
            }

            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                border: 1px solid #dee2e6;
                background-color: white;
                transition: all 0.2s ease;
            }

            .btn-group .btn:hover {
                background-color: #f8f9fa;
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .btn-group .btn i {
                font-size: 14px;
            }

            /* Form within button group */
            .btn-group form {
                margin: 0;
                padding: 0;
                display: inline-block;
            }

            /* Ensure icons are centered */
            .btn i {
                width: 16px;
                height: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Optional: Add tooltips */
            [data-bs-toggle="tooltip"] {
                position: relative;
            }

            .avatar {
                position: relative;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 4rem;
                height: 4rem;
            }

            .avatar-xl {
                width: 5rem;
                height: 5rem;
            }

            .avatar-title {
                width: 100%;
                height: 100%;
                background-color: #eef3f7;
                color: #206bc4;
            }

            .vstack {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .card {
                transition: transform 0.2s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }

            .bg-light-subtle {
                background-color: #f8f9fa;
            }

            .text-capitalize {
                text-transform: capitalize;
            }

            /* Perbaikan untuk dropdown */
            .table-responsive {
                overflow: visible !important;
                /* Penting untuk dropdown */
            }

            .dropdown {
                position: relative;
            }

            .dropdown-menu {
                position: absolute !important;
                z-index: 1050 !important;
                /* Nilai lebih tinggi */
                transform: none !important;
                right: 0;
                left: auto !important;
            }

            /* Pastikan parent elements tidak memblok dropdown */
            .card,
            .card-body,
            .table-responsive,
            .table {
                transform: none !important;
                position: relative;
            }

            /* Optional: Jika masih ada masalah, tambahkan ini */
            .table td {
                position: relative;
            }

            .table td:last-child {
                overflow: visible !important;
            }
        </style>
    @endpush
    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            });
        </script>
    @endpush


</x-layout>

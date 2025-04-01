<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Pendaftar</h5>
                <div>
                    <a href="{{ route('pendaftaran.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Pendaftar
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filter_jurusan">
                            <option value="">Semua Jurusan</option>
                            @foreach ($jurusans as $jurusan)
                                <option value="{{ $jurusan->nama_jurusan }}">{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter_status">
                            <option value="">Semua Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Lulus">Lulus</option>
                            <option value="Tidak Lulus">Tidak Lulus</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter_pembayaran">
                            <option value="">Semua Pembayaran</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter_tahun_ajaran">
                            <option value="">Tahun Ajaran</option>
                            @foreach ($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun_ajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="datatable-pendaftar" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Pembayaran</th>
                                <th>Rata-rata Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($pendaftars as $pendaftar)
    <div class="modal fade" id="detailModal{{ $pendaftar->id }}" tabindex="-1"
        aria-labelledby="detailModalLabel{{ $pendaftar->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <!-- Modal Header -->
                <div class="modal-header border-0 bg-primary bg-gradient text-white px-4 py-3">
                    <h5 class="modal-title fs-4 fw-semibold" id="detailModalLabel{{ $pendaftar->id }}">
                        <i class="fas fa-user-circle me-2"></i>Detail Pendaftar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Profile Section -->
                        <div class="col-md-4 text-center border-end">
                            <div class="position-relative mb-4">
                                <div class="rounded-circle overflow-hidden mx-auto mb-3"
                                    style="width: 150px; height: 150px; border: 3px solid #e3e6f0">
                                    <img src="{{ asset('storage/' . $pendaftar->foto) }}"
                                        alt="Foto {{ $pendaftar->nama }}"
                                        class="img-fluid w-100 h-100 object-fit-cover">
                                </div>
                                <span
                                    class="badge text-white bg-primary position-absolute bottom-0 start-50 translate-middle-x px-3 py-2 rounded-pill">
                                    ID: {{ $pendaftar->daftar_id }}
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1">{{ $pendaftar->nama }}</h5>
                            <p class="text-muted small mb-3">{{ $pendaftar->NISN }}</p>

                            <!-- Status Badges -->
                            <div class="d-flex flex-column gap-2">
                                <!-- Status Seleksi Badge -->
                                <div class="badge text-white bg-{{ $pendaftar->status_seleksi === 'Lulus' ? 'success' : ($pendaftar->status_seleksi === 'Pending' ? 'warning' : 'danger') }} bg-opacity-10 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $pendaftar->status_seleksi }}
                                </div>
                                <div class="badge text-white bg-{{ $pendaftar->administrasi->status_pembayaran === 'Lunas' ? 'success' : 'warning' }} bg-opacity-10 px-3 py-2">
                                    <i class="fas fa-credit-card me-1"></i>
                                    {{ $pendaftar->administrasi->status_pembayaran }}
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="col-md-8">
                            <div class="row g-4">
                                <!-- Personal Information -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-body p-4">
                                            <h6 class="card-title mb-4">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                Informasi Pribadi
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Tempat Lahir</p>
                                                    <p class="fw-semibold">{{ $pendaftar->tmp_lahir }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Tanggal Lahir</p>
                                                    <p class="fw-semibold">
                                                        {{ \Carbon\Carbon::parse($pendaftar->tgl_lahir)->format('d F Y') }}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Jenis Kelamin</p>
                                                    <p class="fw-semibold">
                                                        {{ $pendaftar->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Agama</p>
                                                    <p class="fw-semibold">{{ $pendaftar->agama }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <p class="text-muted mb-1 small">Alamat</p>
                                                    <p class="fw-semibold">{{ $pendaftar->alamat }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Information -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-body p-4">
                                            <h6 class="card-title mb-4">
                                                <i class="fas fa-graduation-cap text-primary me-2"></i>
                                                Informasi Akademik
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Asal Sekolah</p>
                                                    <p class="fw-semibold">{{ $pendaftar->asal_sekolah }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Program Studi</p>
                                                    <p class="fw-semibold">{{ $pendaftar->jurusan->nama_jurusan }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Rata-rata Nilai</p>
                                                    <p class="fw-semibold">
                                                        {{ number_format($pendaftar->rata_rata_nilai, 2) }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Tahun Ajaran</p>
                                                    <p class="fw-semibold">
                                                        {{ $pendaftar->tahunAjaran->tahun_ajaran }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent Information -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-body p-4">
                                            <h6 class="card-title mb-4">
                                                <i class="fas fa-users text-primary me-2"></i>
                                                Informasi Orang Tua
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Nama Orang Tua</p>
                                                    <p class="fw-semibold">{{ $pendaftar->nama_ortu }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Pekerjaan</p>
                                                    <p class="fw-semibold">{{ $pendaftar->pekerjaan_ortu }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <p class="text-muted mb-1 small">No. Telepon</p>
                                                    <p class="fw-semibold">{{ $pendaftar->no_telp_ortu }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Information -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-body p-4">
                                            <h6 class="card-title mb-4">
                                                <i class="fas fa-money-bill text-primary me-2"></i>
                                                Informasi Pembayaran
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">No. Pembayaran</p>
                                                    <p class="fw-semibold">{{ $pendaftar->administrasi->no_bayar }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Total Bayar</p>
                                                    <p class="fw-semibold">Rp
                                                        {{ number_format($pendaftar->administrasi->total_bayar, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 small">Sisa Pembayaran</p>
                                                    <p class="fw-semibold">Rp
                                                        {{ number_format($pendaftar->administrasi->sisa_pembayaran, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light text-dark" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Tutup
                    </button>
                    <a href="{{ route('pendaftaran.edit', $pendaftar->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        Edit Data
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#datatable-pendaftar').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('pendaftaran.index') }}",
                        data: function(d) {
                            d.jurusan = $('#filter_jurusan').val();
                            d.status = $('#filter_status').val();
                            d.pembayaran = $('#filter_pembayaran').val();
                            d.tahun_ajaran = $('#filter_tahun_ajaran').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'NISN',
                            name: 'NISN'
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'jurusan.nama_jurusan',
                            name: 'jurusan.nama_jurusan'
                        },
                        {
                            data: 'status_seleksi',
                            name: 'status_seleksi'
                        },
                        {
                            data: 'administrasi.status_pembayaran',
                            name: 'administrasi.status_pembayaran'
                        },
                        {
                            data: 'rata_rata_nilai',
                            name: 'rata_rata_nilai'
                        },

                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                    }
                });

                // Apply filters
                $('#filter_jurusan, #filter_status, #filter_pembayaran, #filter_tahun_ajaran').change(function() {
                    table.draw();
                });
            });
        </script>
    @endpush
    @push('style')
        <style>
            /* Custom styles for modal */
            .modal-content {
                overflow: hidden;
            }

            .object-fit-cover {
                object-fit: cover;
            }

            .dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                display: inline-block;
            }

            /* Card hover effects */
            .card {
                transition: transform 0.2s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }

            /* Custom scrollbar styling */
            .modal-body::-webkit-scrollbar {
                width: 8px;
            }

            .modal-body::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .modal-body::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 4px;
            }

            .modal-body::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            /* Badge styles */
            .badge {
                font-weight: 500;
                letter-spacing: 0.3px;
            }

            /* Card title styles */
            .card-title {
                font-size: 1rem;
                font-weight: 600;
                color: #4a5568;
            }

            /* Text styles */
            .text-muted {
                color: #718096 !important;
            }

            .fw-semibold {
                font-weight: 600 !important;
            }

            /* Button styles */
            .btn {
                padding: 0.5rem 1rem;
                font-weight: 500;
                letter-spacing: 0.3px;
            }

            .btn i {
                font-size: 0.875rem;
            }
        </style>
    @endpush
</x-layout>

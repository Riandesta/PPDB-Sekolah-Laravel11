<x-layout>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 text-gray-800 mb-0">
                    <i class="fas fa-chalkboard me-2"></i>{{ $kelas->nama_kelas }}
                </h2>
                <p class="text-muted">
                    <i class="fas fa-graduation-cap me-1"></i>{{ $kelas->jurusan->nama_jurusan }} | 
                    <i class="fas fa-calendar me-1"></i>{{ $kelas->tahunAjaran->tahun_ajaran }}
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-file-export me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('kelas.export-absensi', $kelas) }}">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('kelas.print-absensi', $kelas) }}">
                            <i class="fas fa-print me-2"></i> Print PDF
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <!-- Statistik Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fas fa-chart-pie me-2"></i>
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Kelas</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <!-- Total Siswa -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <span>Total Siswa</span>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    {{ $kelasDetail['statistik']['total_siswa'] }}
                                </span>
                            </div>

                            <!-- Siswa Laki-laki -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-male text-info me-2"></i>
                                    <span>Siswa Laki-laki</span>
                                </div>
                                <span class="badge bg-info rounded-pill">
                                    {{ $kelasDetail['statistik']['siswa_laki'] }}
                                </span>
                            </div>

                            <!-- Siswa Perempuan -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-female text-pink me-2"></i>
                                    <span>Siswa Perempuan</span>
                                </div>
                                <span class="badge bg-pink rounded-pill">
                                    {{ $kelasDetail['statistik']['siswa_perempuan'] }}
                                </span>
                            </div>

                            <!-- Pembayaran Lunas -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>Pembayaran Lunas</span>
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    {{ $kelasDetail['statistik']['siswa_lunas'] }}
                                </span>
                            </div>

                            <!-- Rata-rata Nilai -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-star text-warning me-2"></i>
                                    <span>Rata-rata Nilai</span>
                                </div>
                                <span class="badge bg-warning rounded-pill">
                                    {{ number_format($kelasDetail['statistik']['rata_rata_nilai'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Siswa Card -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fas fa-list me-2"></i>
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="siswaTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Status Pembayaran</th>
                                        <th>Nilai Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kelasDetail['siswa'] as $index => $siswa)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $siswa->NISN }}</td>
                                        <td>{{ $siswa->nama }}</td>
                                        <td>
                                            <i class="fas fa-{{ $siswa->jenis_kelamin == 'L' ? 'male text-info' : 'female text-pink' }} me-1"></i>
                                            {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $siswa->administrasi?->status_pembayaran === 'Lunas' ? 'success' : 'warning' }}">
                                                <i class="fas fa-{{ $siswa->administrasi?->status_pembayaran === 'Lunas' ? 'check-circle' : 'clock' }} me-1"></i>
                                                {{ $siswa->administrasi?->status_pembayaran ?? 'Belum Ada' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $siswa->rata_rata_nilai >= 75 ? 'success' : 'warning' }}">
                                                {{ number_format($siswa->rata_rata_nilai, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data siswa</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#siswaTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                pageLength: 10,
                ordering: true,
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                order: [[2, 'asc']], // Sort by nama
            });
        });
    </script>
    @endpush

</x-layout>

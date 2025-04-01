<x-layout>
    <x-slot name="title">
        Pengumuman Kelulusan PPDB
    </x-slot>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pengumuman Kelulusan PPDB</h5>
                @auth
                    <div>
                        <a href="{{ route('calonSiswa.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select" id="filterJurusan">
                        <option value="">Semua Jurusan</option>
                        @foreach($pengumuman->pluck('jurusan')->unique() as $jurusan)
                            <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Tidak Lulus">Tidak Lulus</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-responsive">
                <table class="table table-hover" id="pengumumanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Rata-rata Nilai</th>
                            <th>Status</th>
                            @auth
                                <th>Kelas</th>
                                <th>Status Pembayaran</th>
                            @endauth
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengumuman as $index => $siswa)
                            <tr class="siswa-row"
                                data-jurusan="{{ $siswa->jurusan_id }}"
                                data-status="{{ $siswa->status_seleksi }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $siswa->NISN }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>{{ $siswa->jurusan->nama_jurusan }}</td>
                                <td>{{ number_format($siswa->rata_rata_nilai, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $siswa->status_seleksi === 'Lulus' ? 'success' : ($siswa->status_seleksi === 'Pending' ? 'warning' : 'danger') }}">
                                        {{ $siswa->status_seleksi }}
                                    </span>
                                </td>
                                @auth
                                    <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $siswa->administrasi->status_pembayaran === 'Lunas' ? 'success' : 'warning' }}">
                                            {{ $siswa->administrasi->status_pembayaran }}
                                        </span>
                                    </td>
                                @endauth
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#pengumumanTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            });

            // Filter handling
            $('#filterJurusan, #filterStatus').on('change', function() {
                let jurusan = $('#filterJurusan').val();
                let status = $('#filterStatus').val();

                $('.siswa-row').each(function() {
                    let row = $(this);
                    let showJurusan = !jurusan || row.data('jurusan') == jurusan;
                    let showStatus = !status || row.data('status') == status;

                    if (showJurusan && showStatus) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            });

            // Print functionality
            $('#btnPrint').on('click', function() {
                window.print();
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
    @endpush
</x-layout>

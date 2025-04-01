<x-layout>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Pendaftaran</h3>
        </div>
        <div class="card-body">
            <!-- Form Export Laporan -->
            <form action="{{ route('laporan.pendaftaran.export') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Tahun Ajaran</label>
                    </div>
                    <div class="col-auto">
                        <select name="tahun_ajaran_id" class="form-select" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}"
                                    {{ request('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                    {{ $tahunAjaran->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-export" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3"></path>
                            </svg>
                            Export Laporan Pendaftaran
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabel Ringkasan -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Pendaftar</td>
                            <td>{{ $summary['total_pendaftar'] ?? 0 }} Siswa</td>
                            <td>
                                <span class="badge bg-primary">100%</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Lulus Seleksi</td>
                            <td>{{ $summary['total_lulus'] ?? 0 }} Siswa</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $summary['persentase_lulus'] ?? 0 }}%">
                                        {{ $summary['persentase_lulus'] ?? 0 }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Tidak Lulus</td>
                            <td>{{ $summary['total_tidak_lulus'] ?? 0 }} Siswa</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width: {{ $summary['persentase_tidak_lulus'] ?? 0 }}%">
                                        {{ $summary['persentase_tidak_lulus'] ?? 0 }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grafik -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pendaftar Per Jurusan</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="jurusanChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Status Seleksi</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Script untuk menampilkan grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Pendaftar per Jurusan
        const jurusanCtx = document.getElementById('jurusanChart').getContext('2d');
        new Chart(jurusanCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($summary['labels_jurusan'] ?? []) !!},
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: {!! json_encode($summary['data_jurusan'] ?? []) !!},
                    backgroundColor: ['#206bc4', '#4299e1', '#63b3ed', '#90cdf4']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Grafik Status Seleksi
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Lulus', 'Tidak Lulus', 'Pending'],
                datasets: [{
                    data: [
                        {{ $summary['total_lulus'] ?? 0 }},
                        {{ $summary['total_tidak_lulus'] ?? 0 }},
                        {{ $summary['total_pending'] ?? 0 }}
                    ],
                    backgroundColor: ['#2fb344', '#d63939', '#f59f00']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
    </script>
    @endpush
</x-layout>

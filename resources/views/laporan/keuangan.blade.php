<x-layout>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Keuangan</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('laporan.export.keuangan') }}" method="GET" class="mb-4">
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
                        Export Laporan Keuangan
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabel Ringkasan -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Pendapatan</td>
                        <td>Rp {{ number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-success">
                                {{ $summary['persentase_pendapatan'] ?? 0 }}%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Pembayaran Lunas</td>
                        <td>{{ $summary['pembayaran_lunas'] ?? 0 }} Siswa</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: {{ $summary['persentase_lunas'] ?? 0 }}%">
                                    {{ $summary['persentase_lunas'] ?? 0 }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Pembayaran Belum Lunas</td>
                        <td>{{ $summary['pembayaran_belum_lunas'] ?? 0 }} Siswa</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: {{ $summary['persentase_belum_lunas'] ?? 0 }}%">
                                    {{ $summary['persentase_belum_lunas'] ?? 0 }}%
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
                        <h3 class="card-title">Pembayaran Per Komponen</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="componentChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Status Pembayaran</h3>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grafik Komponen Pembayaran
    const componentCtx = document.getElementById('componentChart').getContext('2d');
    new Chart(componentCtx, {
        type: 'bar',
        data: {
            labels: ['Pendaftaran', 'PPDB', 'MPLS', 'Awal Tahun'],
            datasets: [{
                label: 'Total Pembayaran',
                data: [
                    {{ $summary['total_pendaftaran'] ?? 0 }},
                    {{ $summary['total_ppdb'] ?? 0 }},
                    {{ $summary['total_mpls'] ?? 0 }},
                    {{ $summary['total_awal_tahun'] ?? 0 }}
                ],
                backgroundColor: ['#206bc4', '#4299e1', '#63b3ed', '#90cdf4']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Grafik Status Pembayaran
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Lunas', 'Belum Lunas'],
            datasets: [{
                data: [
                    {{ $summary['pembayaran_lunas'] ?? 0 }},
                    {{ $summary['pembayaran_belum_lunas'] ?? 0 }}
                ],
                backgroundColor: ['#2fb344', '#f59f00']
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

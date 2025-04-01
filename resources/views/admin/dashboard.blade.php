<x-layout>
    <x-slot:title>
        {{ $title }}
    </x-slot:title>

    <div class="container-fluid px-4">
        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Pendaftar Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-primary border-4 shadow h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Total Pendaftar
                                </div>
                                <div class="h3 mb-0 fw-bold text-gray-800">
                                    {{ $statistics['total_pendaftar'] }}
                                </div>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diterima Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-success border-4 shadow h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Diterima
                                </div>
                                <div class="h3 mb-0 fw-bold">
                                    {{ $statistics['total_diterima'] }}
                                </div>
                            </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pembayaran Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-warning border-4 shadow h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Total Pembayaran
                                </div>
                                <div class="h3 mb-0 fw-bold">
                                    Rp {{ number_format($statistics['total_pembayaran'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sisa Kuota Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-info border-4 shadow h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Sisa Kuota
                                </div>
                                <div class="h3 mb-0 fw-bold">
                                    {{ $statistics['sisa_kuota'] }}
                                </div>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="fas fa-user-plus fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-chart-line me-1"></i> Statistik Pendaftaran
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Export Data</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="pendaftaranChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Charts Row -->
        <div class="row g-4">
            <div class="col-xl-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-chart-pie me-1"></i> Pendaftar per Jurusan
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="pendaftarPerJurusanChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-chart-bar me-1"></i> Status Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="statusPembayaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Konfigurasi umum untuk charts
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        };

        // Chart Pendaftar per Jurusan
        new Chart(document.getElementById('pendaftarPerJurusanChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($statistics['pendaftar_per_jurusan']->pluck('jurusan.nama_jurusan')) !!},
                datasets: [{
                    data: {!! json_encode($statistics['pendaftar_per_jurusan']->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(13, 202, 240, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });

        // Chart Status Pembayaran
        new Chart(document.getElementById('statusPembayaranChart'), {
            type: 'bar',
            data: {
                labels: ['Lunas', 'Belum Lunas'],
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: [
                        {{ $statistics['pembayaran_lunas'] }},
                        {{ $statistics['pembayaran_belum_lunas'] }}
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart Pendaftaran
        new Chart(document.getElementById('pendaftaranChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($statistics['labels']) !!},
                datasets: [
                    {
                        label: 'Pendaftar per Hari',
                        data: {!! json_encode($statistics['data']) !!},
                        borderColor: 'rgba(13, 110, 253, 0.8)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Total Kumulatif',
                        data: {!! json_encode($statistics['data_kumulatif']) !!},
                        borderColor: 'rgba(25, 135, 84, 0.8)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    ...chartOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' +
                                    context.raw.toLocaleString('id-ID') + ' pendaftar';
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .card {
            transition: transform 0.2s;
        }

        canvas {
            width: 100% !important;
            height: 300px !important;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .border-start {
            border-left-width: 4px !important;
        }

        .bg-opacity-10 {
            opacity: 0.1;
        }
    </style>
    @endpush
</x-layout>

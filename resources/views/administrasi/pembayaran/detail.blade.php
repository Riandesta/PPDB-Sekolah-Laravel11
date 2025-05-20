{{-- administrasi/pembayaran/detail.blade.php --}}
<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detail Pembayaran</h3>
                <a href="{{ route('administrasi.pembayaran.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h4>Informasi Siswa</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Nama Siswa</th>
                                <td>{{ $administrasi->pendaftaran->nama }}</td>
                            </tr>
                            <tr>
                                <th>No. Pendaftaran</th>
                                <td>{{ $administrasi->pendaftaran->daftar_id }}</td>
                            </tr>
                            <tr>
                                <th>Jurusan</th>
                                <td>{{ $administrasi->pendaftaran->jurusan->nama_jurusan }}</td>
                            </tr>
                        </table>

                        <h4 class="mt-4">Rincian Biaya</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>Biaya Pendaftaran</th>
                                <td>Rp {{ number_format($administrasi->biaya_pendaftaran, 0, ',', '.') }}</td>
                                <td>{!! $administrasi->is_pendaftaran_lunas ? '<span class="badge bg-success text-white">Lunas</span>' : '<span class="badge bg-warning">Belum Lunas</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Biaya PPDB </th>
                                <td>Rp {{ number_format($administrasi->biaya_ppdb, 0, ',', '.') }}</td>
                                <td>{!! $administrasi->is_ppdb_lunas ? '<span class="badge bg-success text-white">Lunas</span>' : '<span class="badge bg-warning">Belum Lunas</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Biaya MPLS</th>
                                <td>Rp {{ number_format($administrasi->biaya_mpls, 0, ',', '.') }}</td>
                                <td>{!! $administrasi->is_mpls_lunas ? '<span class="badge bg-success text-white">Lunas</span>' : '<span class="badge bg-warning">Belum Lunas</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Biaya Awal Tahun</th>
                                <td>Rp {{ number_format($administrasi->biaya_awal_tahun, 0, ',', '.') }}</td>
                                <td>{!! $administrasi->is_awal_tahun_lunas ? '<span class="badge bg-success text-white">Lunas</span>' : '<span class="badge bg-warning">Belum Lunas</span>' !!}</td>
                            </tr>
                        </table>

                        <div class="mt-4">
                            <h5>Total Pembayaran: Rp {{ number_format($administrasi->total_bayar, 0, ',', '.') }}</h5>
                            <h5>Sisa Pembayaran: Rp {{ number_format($administrasi->sisa_pembayaran, 0, ',', '.') }}</h5>
                            <h5>Status:
                                <span class="badge text-white {{ $administrasi->status_pembayaran === 'Lunas' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $administrasi->status_pembayaran }}
                                </span>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h4>Riwayat Pembayaran</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($administrasi->riwayatPembayaran as $riwayat)
                                <tr id="payment-row-{{ $riwayat->id }}" data-order-id="{{ $riwayat->no_pembayaran }}"
                                    data-status="{{ $riwayat->status }}" data-payment-method="{{ $riwayat->metode_pembayaran }}">
                                    <td>{{ $riwayat->tanggal_bayar->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($riwayat->jenis_pembayaran) }}</td>
                                    <td>Rp {{ number_format($riwayat->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($riwayat->metode_pembayaran) }}</td>
                                    <td class="payment-status">
                                        {!! $riwayat->status_badge !!}
                                        @if($riwayat->metode_pembayaran === 'midtrans' && $riwayat->status === 'pending')
                                            <button class="btn btn-sm btn-info check-status" data-order-id="{{ $riwayat->no_pembayaran }}">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($riwayat->bukti_pembayaran)
                                            <a href="{{ Storage::url($riwayat->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-image"></i>
                                            </a>
                                        @elseif($riwayat->metode_pembayaran === 'midtrans' && $riwayat->status === 'pending')
                                            <a href="#" class="btn btn-sm btn-success continue-payment" data-order-id="{{ $riwayat->no_pembayaran }}">
                                                <i class="fas fa-credit-card"></i> Bayar
                                            </a>
                                        @endif

                                        @if($riwayat->status === 'success')
                                            <a href="{{ route('administrasi.pembayaran.struk', $administrasi->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada riwayat pembayaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($administrasi->status_pembayaran !== 'Lunas')
                            <div class="mt-4 text-end">
                                <a href="{{ route('administrasi.pembayaran.bayar', $administrasi->id) }}"
                                   class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Pembayaran
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Check Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Status Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="status-loading" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Memeriksa status pembayaran...</p>
                    </div>
                    <div id="status-result" style="display:none;">
                        <table class="table">
                            <tr>
                                <th>Order ID:</th>
                                <td id="status-order-id"></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td id="status-transaction-status"></td>
                            </tr>
                            <tr>
                                <th>Metode Pembayaran:</th>
                                <td id="status-payment-type"></td>
                            </tr>
                            <tr>
                                <th>Waktu Transaksi:</th>
                                <td id="status-transaction-time"></td>
                            </tr>
                        </table>
                    </div>
                    <div id="status-error" class="alert alert-danger" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="pay-again-btn" class="btn btn-primary" style="display:none;">Bayar Sekarang</a>
                    <button type="button" id="refresh-page-btn" class="btn btn-success" style="display:none;">Refresh Halaman</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Check Payment Status
                $('.check-status').on('click', function() {
                    const orderId = $(this).data('order-id');
                    checkPaymentStatus(orderId);
                });

                // Continue Payment
                $('.continue-payment').on('click', function(e) {
                    e.preventDefault();
                    const orderId = $(this).data('order-id');
                    continuePayment(orderId);
                });

                // Function to check payment status
                function checkPaymentStatus(orderId) {
                    // Reset modal
                    $('#status-result').hide();
                    $('#status-error').hide();
                    $('#status-loading').show();
                    $('#pay-again-btn').hide();
                    $('#refresh-page-btn').hide();

                    // Show modal
                    $('#statusModal').modal('show');

                    // Check status via AJAX
                    $.ajax({
                        url: "{{ route('midtrans.status') }}",
                        type: 'GET',
                        data: {
                            order_id: orderId
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('#status-loading').hide();

                            if (response.status === 'success') {
                                // Show status result
                                $('#status-order-id').text(response.data.order_id);
                                $('#status-transaction-status').text(response.data.transaction_status);
                                $('#status-payment-type').text(response.data.payment_type);
                                $('#status-transaction-time').text(response.data.transaction_time);

                                $('#status-result').show();

                                // Show appropriate buttons based on status
                                if (response.data.transaction_status === 'pending') {
                                    $('#pay-again-btn').attr('href', "{{ route('midtrans.unfinish') }}?order_id=" + orderId).show();
                                } else if (['settlement', 'capture', 'success'].includes(response.data.transaction_status)) {
                                    $('#refresh-page-btn').show();
                                }
                            } else {
                                // Show error
                                $('#status-error').text(response.message).show();
                            }
                        },
                        error: function(xhr) {
                            $('#status-loading').hide();

                            let errorMessage = 'Gagal memeriksa status pembayaran';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            $('#status-error').text(errorMessage).show();

                            // Show pay again button for all errors
                            $('#pay-again-btn').attr('href', "{{ route('midtrans.unfinish') }}?order_id=" + orderId).show();
                        }
                    });
                }

                // Function to continue payment
                function continuePayment(orderId) {
                    window.location.href = "{{ route('midtrans.unfinish') }}?order_id=" + orderId;
                }

                // Refresh page button
                $('#refresh-page-btn').on('click', function() {
                    location.reload();
                });
            });
        </script>
    @endpush
</x-layout>

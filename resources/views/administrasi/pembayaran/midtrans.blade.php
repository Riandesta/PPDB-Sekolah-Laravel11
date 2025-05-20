{{-- administrasi/pembayaran/midtrans.blade.php --}}
<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pembayaran via Midtrans</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Informasi Siswa -->
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" class="form-control"
                                value="{{ $administrasi->pendaftaran->nama }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" class="form-control"
                                value="{{ $administrasi->pendaftaran->jurusan->nama_jurusan }}" readonly>
                        </div>
                        <!-- Rincian Biaya -->
                        <div class="mb-3">
                            <label class="form-label">Total Biaya</label>
                            <input type="text" class="form-control" id="total-biaya"
                                value="Rp {{ number_format($administrasi->biaya_pendaftaran + $administrasi->biaya_ppdb + $administrasi->biaya_mpls + $administrasi->biaya_awal_tahun, 0, ',', '.') }}"
                                data-total="{{ (int)($administrasi->biaya_pendaftaran + $administrasi->biaya_ppdb + $administrasi->biaya_mpls + $administrasi->biaya_awal_tahun) }}"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sudah Dibayar</label>
                            <input type="text" class="form-control" id="sudah-dibayar"
                                value="Rp {{ number_format($administrasi->total_bayar, 0, ',', '.') }}"
                                data-total="{{ (int)($administrasi->total_bayar) }}"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sisa Pembayaran</label>
                            <input type="text" class="form-control" id="total-sisa-pembayaran"
                                value="Rp {{ number_format(
                                    $administrasi->biaya_pendaftaran +
                                        $administrasi->biaya_ppdb +
                                        $administrasi->biaya_mpls +
                                        $administrasi->biaya_awal_tahun -
                                        $administrasi->total_bayar,
                                    0,
                                    ',',
                                    '.',
                                ) }}"
                                data-total="{{ (int)($administrasi->biaya_pendaftaran + $administrasi->biaya_ppdb + $administrasi->biaya_mpls + $administrasi->biaya_awal_tahun - $administrasi->total_bayar) }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Form Pembayaran -->
                        <form id="payment-form">
                            <div class="mb-3">
                                <label class="form-label">Jenis Pembayaran</label>
                                <div id="jenis-pembayaran-container">
                                    @if (!$administrasi->is_pendaftaran_lunas)
                                        <div class="form-check">
                                            <input class="form-check-input jenis-pembayaran" type="checkbox" name="jenis_pembayaran[]"
                                                value="pendaftaran" id="pendaftaranCheck" data-biaya="{{ $administrasi->biaya_pendaftaran }}" data-sisa="{{ $administrasi->biaya_pendaftaran - $administrasi->totalBayarUntukJenis('pendaftaran') }}">
                                            <label class="form-check-label" for="pendaftaranCheck">
                                                Pendaftaran (Rp
                                                {{ number_format($administrasi->biaya_pendaftaran, 0, ',', '.') }}) -
                                                Sisa: Rp <span id="sisa-pendaftaran">{{ number_format($administrasi->biaya_pendaftaran - $administrasi->totalBayarUntukJenis('pendaftaran'), 0, ',', '.') }}</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (!$administrasi->is_ppdb_lunas)
                                        <div class="form-check">
                                            <input class="form-check-input jenis-pembayaran" type="checkbox" name="jenis_pembayaran[]"
                                                value="ppdb" id="ppdbCheck" data-biaya="{{ $administrasi->biaya_ppdb }}" data-sisa="{{ $administrasi->biaya_ppdb - $administrasi->totalBayarUntukJenis('ppdb') }}">
                                            <label class="form-check-label" for="ppdbCheck">
                                                PPDB (Rp {{ number_format($administrasi->biaya_ppdb, 0, ',', '.') }}) -
                                                Sisa: Rp <span id="sisa-ppdb">{{ number_format($administrasi->biaya_ppdb - $administrasi->totalBayarUntukJenis('ppdb'), 0, ',', '.') }}</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (!$administrasi->is_mpls_lunas)
                                        <div class="form-check">
                                            <input class="form-check-input jenis-pembayaran" type="checkbox" name="jenis_pembayaran[]" value="mpls" id="mplsCheck" data-biaya="{{ $administrasi->biaya_mpls }}" data-sisa="{{ $administrasi->biaya_mpls - $administrasi->totalBayarUntukJenis('mpls') }}">
                                            <label class="form-check-label" for="mplsCheck">
                                                MPLS (Rp {{ number_format($administrasi->biaya_mpls, 0, ',', '.') }}) -
                                                Sisa: Rp <span id="sisa-mpls">{{ number_format($administrasi->biaya_mpls - $administrasi->totalBayarUntukJenis('mpls'), 0, ',', '.') }}</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (!$administrasi->is_awal_tahun_lunas)
                                        <div class="form-check">
                                            <input class="form-check-input jenis-pembayaran" type="checkbox" name="jenis_pembayaran[]" value="awal_tahun" id="awalTahunCheck" data-biaya="{{ $administrasi->biaya_awal_tahun }}" data-sisa="{{ $administrasi->biaya_awal_tahun - $administrasi->totalBayarUntukJenis('awal_tahun') }}">
                                            <label class="form-check-label" for="awalTahunCheck">
                                                Awal Tahun (Rp {{ number_format($administrasi->biaya_awal_tahun, 0, ',', '.') }}) -
                                                Sisa: Rp <span id="sisa-awal_tahun">{{ number_format($administrasi->biaya_awal_tahun - $administrasi->totalBayarUntukJenis('awal_tahun'), 0, ',', '.') }}</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Pembayaran</label>
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar"
                                    class="form-control"
                                    value="{{ $paymentData['jumlah_bayar'] ?? 0 }}" required min="1">
                                <div id="jumlah-error" class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <p>Metode Pembayaran: <strong>Midtrans Payment Gateway</strong></p>
                                <p class="text-muted">Anda akan diarahkan ke halaman pembayaran Midtrans yang menyediakan berbagai metode pembayaran seperti kartu kredit, transfer bank, e-wallet, dll.</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('administrasi.pembayaran.detail', $administrasi->id) }}"
                                    class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="button" id="pay-button" class="btn btn-primary">
                                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                                </button>
                            </div>
                        </form>

                        <!-- Payment Result -->
                        <div id="result-json" class="mt-3" style="display:none;"></div>

                        <!-- Loading indicator -->
                        <div id="loading" style="display:none;" class="text-center mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Memproses pembayaran...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Include Midtrans Snap JS -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>

        <script>
            $(document).ready(function() {
                // Set initial values based on session data
                const paymentData = @json($paymentData ?? []);
                if (paymentData && paymentData.jenis_pembayaran) {
                    paymentData.jenis_pembayaran.forEach(jenis => {
                        $(`#${jenis}Check`).prop('checked', true);
                    });
                }

                // Fungsi untuk format rupiah
                function formatRupiah(angka) {
                    let number_string = angka.toString().replace(/[^,\d]/g, ''),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    // tambahkan titik jika yang di input sudah menjadi angka ribuan
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return 'Rp ' + rupiah;
                }

                // Validate selected payment types
                function validateSelection() {
                    const selectedTypes = $('.jenis-pembayaran:checked').length;
                    const jumlahBayar = parseInt($('#jumlah_bayar').val()) || 0;

                    if (selectedTypes === 0) {
                        $('#jumlah-error').text('Pilih minimal satu jenis pembayaran');
                        $('#jumlah-error').show();
                        return false;
                    }

                    if (jumlahBayar <= 0) {
                        $('#jumlah-error').text('Jumlah pembayaran harus lebih dari 0');
                        $('#jumlah-error').show();
                        return false;
                    }

                    // Get maximum allowed payment
                    let maxPayment = 0;
                    $('.jenis-pembayaran:checked').each(function() {
                        maxPayment += parseInt($(this).data('sisa')) || 0;
                    });

                    if (jumlahBayar > maxPayment) {
                        $('#jumlah-error').text('Jumlah pembayaran melebihi sisa tagihan');
                        $('#jumlah-error').show();
                        return false;
                    }

                    $('#jumlah-error').hide();
                    return true;
                }

                // Update sisa pembayaran berdasarkan input jumlah pembayaran
                $('#jumlah_bayar').on('input', function() {
                    let jumlahBayar = parseInt($(this).val()) || 0;
                    let totalBiaya = parseInt($('#total-biaya').data('total'));
                    let sudahDibayar = parseInt($('#sudah-dibayar').data('total'));
                    let sisaPembayaran = totalBiaya - sudahDibayar - jumlahBayar;

                    if (sisaPembayaran < 0) sisaPembayaran = 0;

                    $('#total-sisa-pembayaran').val(formatRupiah(sisaPembayaran));
                    validateSelection();
                });

                // Validate on checkbox change
                $('.jenis-pembayaran').on('change', function() {
                    validateSelection();
                });

                // Pay button handler
                $('#pay-button').on('click', function(e) {
                    e.preventDefault();

                    if (!validateSelection()) {
                        return false;
                    }

                    // Show loading
                    $('#loading').show();
                    $('#pay-button').prop('disabled', true);

                    // Get selected payment types
                    const selectedTypes = [];
                    $('.jenis-pembayaran:checked').each(function() {
                        selectedTypes.push($(this).val());
                    });

                    // Prepare data for AJAX request
                    const paymentData = {
                        jenis_pembayaran: selectedTypes,
                        jumlah_bayar: $('#jumlah_bayar').val(),
                        _token: '{{ csrf_token() }}'
                    };

                    // Request Snap token
                    $.ajax({
                        url: '{{ route('midtrans.get-snap-token', $administrasi->id) }}',
                        type: 'POST',
                        data: paymentData,
                        dataType: 'json',
                        success: function(response) {
                            $('#loading').hide();
                            $('#pay-button').prop('disabled', false);

                            // Open Snap payment page
                            snap.pay(response.snap_token, {
                                onSuccess: function(result) {
                                    $('#result-json').html(JSON.stringify(result, null, 2));
                                    window.location.href = '{{ route('midtrans.finish') }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                                },
                                onPending: function(result) {
                                    $('#result-json').html(JSON.stringify(result, null, 2));
                                    window.location.href = '{{ route('midtrans.finish') }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                                },
                                onError: function(result) {
                                    $('#result-json').html(JSON.stringify(result, null, 2));
                                    window.location.href = '{{ route('midtrans.error') }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                                },
                                onClose: function() {
                                    $('#loading').hide();
                                    $('#pay-button').prop('disabled', false);
                                    alert('Anda menutup popup pembayaran sebelum menyelesaikan pembayaran');
                                }
                            });
                        },
                        error: function(xhr) {
                            $('#loading').hide();
                            $('#pay-button').prop('disabled', false);

                            let errorMessage = 'Terjadi kesalahan saat memproses pembayaran';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            alert(errorMessage);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layout>

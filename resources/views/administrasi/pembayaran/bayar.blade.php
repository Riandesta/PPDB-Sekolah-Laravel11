<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Pembayaran</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('administrasi.pembayaran.store', $administrasi->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
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
                            <div class="mb-3">
                                <label class="form-label">Jenis Pembayaran</label>
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
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pembayaran</label>
                                    <input type="number" name="jumlah_bayar" id="jumlah_bayar"
                                        class="form-control @error('jumlah_bayar') is-invalid @enderror"
                                        value="{{ old('jumlah_bayar') }}" required min="1">
                                    @error('jumlah_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="metode_pembayaran"
                                        class="form-select @error('metode_pembayaran') is-invalid @enderror" required>
                                        <option value="tunai">Tunai</option>
                                        <option value="transfer">Transfer Bank</option>
                                    </select>
                                    @error('metode_pembayaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3" id="bukti-pembayaran" style="display: none;">
                                    <label class="form-label">Bukti Pembayaran</label>
                                    <input type="file" name="bukti_pembayaran"
                                        class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                                        accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                                    @error('bukti_pembayaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('administrasi.pembayaran.detail', $administrasi->id) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Proses Pembayaran
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Toggle bukti pembayaran
                $('select[name="metode_pembayaran"]').on('change', function() {
                    if ($(this).val() === 'transfer') {
                        $('#bukti-pembayaran').show();
                        $('input[name="bukti_pembayaran"]').prop('required', true);
                    } else {
                        $('#bukti-pembayaran').hide();
                        $('input[name="bukti_pembayaran"]').prop('required', false);
                    }
                });

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

               // Update sisa pembayaran berdasarkan input jumlah pembayaran
               $('#jumlah_bayar').on('input', function() {
                    let jumlahBayar = parseInt($(this).val()) || 0;
                    let totalBiaya = parseInt($('#total-biaya').data('total'));
                    let sudahDibayar = parseInt($('#sudah-dibayar').data('total'));
                    let sisaPembayaran = totalBiaya - sudahDibayar - jumlahBayar;
                    $('#total-sisa-pembayaran').val(formatRupiah(sisaPembayaran));
                });
            });
        </script>
    @endpush

</x-layout>

<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Struk Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="struk">
                    <!-- Header Struk -->
                    <div class="struk-header text-center">
                        <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="struk-logo" style="max-width: 100px;">
                        <h4 class="struk-title">SMK IGASAR PINDAD BANDUNG</h4>
                    </div>

                    <!-- Body Struk -->
                    <div class="struk-body">
                        <div class="struk-info">
                            <p><strong>No. Pembayaran:</strong> {{ $administrasi->no_bayar }}</p>
                            <p><strong>Nama Siswa:</strong> {{ $administrasi->pendaftaran->nama }}</p>
                            <p><strong>No. Pendaftaran:</strong> {{ $administrasi->pendaftaran->daftar_id }}</p>
                            <p><strong>Jurusan:</strong> {{ $administrasi->pendaftaran->jurusan->nama_jurusan }}</p>
                            <p><strong>Tanggal Pembayaran:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                        </div>

                        @php
                            // Ambil pembayaran terbaru
                            $latestPayment = $administrasi->riwayatPembayaran->first();
                        @endphp

                        <!-- Tabel Detail Pembayaran -->
                        <table class="struk-table table table-bordered">
                            <thead>
                                <tr>
                                    <th>Jenis Pembayaran</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Status</th>
                                    <th>Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($latestPayment)
                                    <tr>
                                        <td>{{ ucfirst($latestPayment->jenis_pembayaran) }}</td>
                                        <td>Rp {{ number_format($latestPayment->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>{{ $latestPayment->status }}</td>
                                        <td>Rp {{ number_format($administrasi->sisa_pembayaran, 0, ',', '.') }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data pembayaran.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Struk -->
                    <div class="struk-footer text-center">
                        <p><strong>Total Sisa Pembayaran:</strong> Rp {{ number_format($administrasi->sisa_pembayaran, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="text-center mt-4">
                    <a href="{{ route('administrasi.pembayaran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button id="printStruk" class="btn btn-primary">
                        <i class="fas fa-print"></i> Cetak Struk
                    </button>
                    <button id="sendWhatsApp" class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> Kirim ke WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Cetak Struk
                $('#printStruk').on('click', function() {
                    window.print();
                });

                // Kirim ke WhatsApp
                $('#sendWhatsApp').on('click', function() {
                    let noWa = "{{ $administrasi->pendaftaran->no_telp_ortu }}";
                    let latestPayment = @json(@isset($administrasi->riwayatPembayaran[0]) ? $administrasi->riwayatPembayaran[0] : null);

                    if (!latestPayment) {
                        alert("Tidak ada data pembayaran terbaru.");
                        return;
                    }

                    let message = encodeURIComponent(`
SMK IGASAR PINDAD BANDUNG
No. Pembayaran: {{ $administrasi->no_bayar }}
Nama Siswa: {{ $administrasi->pendaftaran->nama }}
No. Pendaftaran: {{ $administrasi->pendaftaran->daftar_id }}
Jurusan: {{ $administrasi->pendaftaran->jurusan->nama_jurusan }}
Tanggal Pembayaran: {{ now()->format('d/m/Y H:i') }}

Jenis Pembayaran: ${latestPayment.jenis_pembayaran}
Jumlah Pembayaran: Rp ${latestPayment.jumlah_bayar}
Status Pembayaran: ${latestPayment.status}
Sisa Pembayaran: Rp {{ number_format($administrasi->sisa_pembayaran, 0, ',', '.') }}

Total Sisa Pembayaran: Rp {{ number_format($administrasi->sisa_pembayaran, 0, ',', '.') }}
`);
                    window.open("https://wa.me/" + noWa + "?text=" + message, '_blank');
                });
            });
        </script>
    @endpush
</x-layout>

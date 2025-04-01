<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detail Pembayaran</h3>
                <a href="{{ route('administrasi.pembayaran.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="card-body">
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
                                    <th>Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($administrasi->riwayatPembayaran as $riwayat)
                                <tr>
                                    <td>{{ $riwayat->tanggal_bayar->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($riwayat->jenis_pembayaran) }}</td>
                                    <td>Rp {{ number_format($riwayat->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($riwayat->metode_pembayaran) }}</td>
                                    <td>
                                        @if($riwayat->bukti_pembayaran)
                                            <a href="{{ Storage::url($riwayat->bukti_pembayaran) }}" target="_parent">
                                                Lihat Bukti
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada riwayat pembayaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($administrasi->status_pembayaran !== 'Lunas')
                            <div class="mt-4 text-end">
                                <a href="{{ route('administrasi.pembayaran.bayar', $administrasi->id) }}"
                                   class="btn btn-primary">
                                    Tambah Pembayaran
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

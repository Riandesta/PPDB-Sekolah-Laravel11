<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detail Tahun Ajaran</h3>
                <div>
                    <a href="{{ route('tahun-ajaran.edit', $tahunAjaran->id) }}" class="btn btn-info">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Umum</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Tahun Ajaran</th>
                                <td>{{ $tahunAjaran->tahun_ajaran }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge text-white {{ $tahunAjaran->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $tahunAjaran->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Periode Tahun</th>
                                <td>{{ $tahunAjaran->tahun_mulai }} - {{ $tahunAjaran->tahun_selesai }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>{{ $tahunAjaran->tanggal_mulai->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai</th>
                                <td>{{ $tahunAjaran->tanggal_selesai->format('d F Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Rincian Biaya</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Biaya Pendaftaran</th>
                                <td>Rp {{ number_format($tahunAjaran->biaya_pendaftaran, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Biaya PPDB</th>
                                <td>Rp {{ number_format($tahunAjaran->biaya_ppdb, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Biaya MPLS</th>
                                <td>Rp {{ number_format($tahunAjaran->biaya_mpls, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Biaya Awal Tahun</th>
                                <td>Rp {{ number_format($tahunAjaran->biaya_awal_tahun, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-primary">
                                <th>Total Biaya</th>
                                <td>Rp {{ number_format(
                                    $tahunAjaran->biaya_pendaftaran +
                                    $tahunAjaran->biaya_ppdb +
                                    $tahunAjaran->biaya_mpls +
                                    $tahunAjaran->biaya_awal_tahun,
                                    0, ',', '.'
                                ) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($tahunAjaran->pendaftarans->count() > 0)
                <div class="mt-4">
                    <h5>Daftar Siswa Terdaftar</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Pendaftaran</th>
                                    <th>Nama Siswa</th>
                                    <th>Jurusan</th>
                                    <th>Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tahunAjaran->pendaftarans as $index => $pendaftaran)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pendaftaran->daftar_id }}</td>
                                    <td>{{ $pendaftaran->nama }}</td>
                                    <td>{{ $pendaftaran->jurusan->nama_jurusan }}</td>
                                    <td>
                                        <span class="badge {{ $pendaftaran->administrasi->status_pembayaran == 'Lunas' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $pendaftaran->administrasi->status_pembayaran }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>

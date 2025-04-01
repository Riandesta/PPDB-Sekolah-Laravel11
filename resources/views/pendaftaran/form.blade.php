<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ isset($pendaftaran) ? 'Edit Pendaftar' : 'Tambah Pendaftar' }}</h5>
            </div>
            <div class="card-body">
                <form
                    action="{{ isset($pendaftaran) ? route('pendaftaran.update', $pendaftaran) : route('pendaftaran.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($pendaftaran))
                        @method('PUT')
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="NISN" class="form-label">NISN</label>
                        <input type="text" class="form-control @error('NISN') is-invalid @enderror" id="NISN"
                            name="NISN" value="{{ old('NISN', $pendaftaran->NISN ?? '') }}"
                            @if (isset($pendaftaran)) required @endif>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                            name="nama" value="{{ old('nama', $pendaftaran->nama ?? '') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <select name="tahun_ajaran_id" class="form-control" required>
                        @if ($tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun_ajaran }}</option>
                        @else
                            <option value="">Tidak ada tahun ajaran aktif</option>
                        @endif
                    </select>



                    <div class="mb-3">
                        <label for="tmp_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tmp_lahir') is-invalid @enderror"
                            id="tmp_lahir" name="tmp_lahir"
                            value="{{ old('tmp_lahir', $pendaftaran->tmp_lahir ?? '') }}" required>
                        @error('tmp_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror"
                            id="tgl_lahir" name="tgl_lahir"
                            value="{{ old('tgl_lahir', isset($pendaftaran) ? $pendaftaran->tgl_lahir->format('Y-m-d') : '') }}"
                            required>
                        @error('tgl_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="jenis_kelamin" value="L"
                                {{ old('jenis_kelamin', $pendaftaran->jenis_kelamin ?? '') == 'L' ? 'checked' : '' }}
                                required>
                            <label class="form-check-label">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="jenis_kelamin" value="P"
                                {{ old('jenis_kelamin', $pendaftaran->jenis_kelamin ?? '') == 'P' ? 'checked' : '' }}
                                required>
                            <label class="form-check-label">Perempuan</label>
                        </div>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="agama" class="form-label">Agama</label>
                        <select class="form-control @error('agama') is-invalid @enderror" id="agama" name="agama"
                            required>
                            <option value="">Pilih Agama</option>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                <option value="{{ $agama }}"
                                    {{ old('agama', $pendaftaran->agama ?? '') == $agama ? 'selected' : '' }}>
                                    {{ $agama }}
                                </option>
                            @endforeach
                        </select>
                        @error('agama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                            required>{{ old('alamat', $pendaftaran->alamat ?? '') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data Sekolah -->
                    <div class="mb-3">
                        <label for="asal_sekolah" class="form-label">Asal Sekolah</label>
                        <input type="text" class="form-control @error('asal_sekolah') is-invalid @enderror"
                            id="asal_sekolah" name="asal_sekolah"
                            value="{{ old('asal_sekolah', $pendaftaran->asal_sekolah ?? '') }}" required>
                        @error('asal_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Data Orang Tua -->
                    <div class="mb-3">
                        <label for="nama_ortu" class="form-label">Nama Orang Tua</label>
                        <input type="text" class="form-control @error('nama_ortu') is-invalid @enderror"
                        id="nama_ortu" name="nama_ortu"
                        value="{{ old('nama_ortu', $pendaftaran->nama_ortu ?? '') }}" required>
                        @error('nama_ortu')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pekerjaan_ortu" class="form-label">Pekerjaan Orang Tua</label>
                        <input type="text" class="form-control @error('pekerjaan_ortu') is-invalid @enderror"
                        id="pekerjaan_ortu" name="pekerjaan_ortu"
                        value="{{ old('pekerjaan_ortu', $pendaftaran->pekerjaan_ortu ?? '') }}" required>
                        @error('pekerjaan_ortu')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_telp_ortu" class="form-label">Nomor Telepon Orang Tua</label>
                        <input type="text" class="form-control @error('no_telp_ortu') is-invalid @enderror"
                        id="no_telp_ortu" name="no_telp_ortu"
                        value="{{ old('no_telp_ortu', $pendaftaran->no_telp_ortu ?? '') }}" required>
                        @error('no_telp_ortu')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Jurusan -->
                    <div class="mb-3">
                        <label for="jurusan_id" class="form-label">Jurusan</label>
                        <select class="form-control @error('jurusan_id') is-invalid @enderror" id="jurusan_id"
                        name="jurusan_id" required>
                        <option value="">Pilih Jurusan</option>
                        @foreach ($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}"
                            {{ old('jurusan_id', $pendaftaran->jurusan_id ?? '') == $jurusan->id ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}
                        </option>
                        @endforeach
                    </select>
                    @error('jurusan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Upload Foto -->
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    @if (isset($pendaftaran) && $pendaftaran->foto)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $pendaftaran->foto) }}" alt="Foto Siswa"
                        class="img-thumbnail" style="max-width: 200px" id="preview">
                    </div>
                    @endif
                    <input type="file" class="form-control @error('foto') is-invalid @enderror"
                    id="foto" name="foto" accept="image/*">
                    @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="card shadow-sm my-4 mx-4">
                <div class="card-body">
                    <div class="card-header">
                    <h5 class="card-title ">Nilai Akademik</h5>
                    </div>
                    <div class="row mt-5">
                        @for ($i = 1; $i <= 5; $i++)
                            <div class="col-md-4 mb-3">
                                <label for="nilai_semester_{{ $i }}" class="form-label">Nilai Semester {{ $i }}</label>
                                <input type="number" step="0.01" min="0" max="100"
                                    class="form-control @error('nilai_semester_' . $i) is-invalid @enderror"
                                    id="nilai_semester_{{ $i }}" name="nilai_semester_{{ $i }}"
                                    value="{{ old('nilai_semester_' . $i, $pendaftaran->{'nilai_semester_' . $i} ?? '') }}">
                                @error('nilai_semester_' . $i)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

                    <div class="card-body">
                        @if (!isset($pendaftaran))
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Pembayaran Pendaftaran</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="alert alert-info">
                                                <h6>Rincian Biaya Pendaftaran:</h6>
                                                <table class="table table-sm mb-0">
                                                    <tr>
                                                        <td>Biaya Pendaftaran</td>
                                                        <td>: Rp
                                                            {{ number_format(config('ppdb.biaya_pendaftaran', 100000), 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Total Biaya</strong></td>
                                                        <td><strong>: Rp
                                                                {{ number_format(config('ppdb.biaya_pendaftaran', 100000), 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- Status Dokumen -->
                                      <div class="-mt-5 p-1">
                                          <label class="form-label">Status Dokumen</label>
                                          <div class="form-check">
                                              <input type="checkbox" class="form-check-input" name="status_dokumen"
                                                  value="1"
                                                  {{ old('status_dokumen', $pendaftaran->status_dokumen ?? false) ? 'checked' : '' }}>
                                              <label class="form-check-label">Dokumen Lengkap</label>
                                          </div>
                                      </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Pembayaran</label>
                                                <input type="number" name="pembayaran_awal"
                                                    class="form-control @error('pembayaran_awal') is-invalid @enderror"
                                                    value="{{ config('ppdb.biaya_pendaftaran', 100000) }}" readonly>
                                                <small class="text-muted">Biaya pendaftaran harus dibayar penuh</small>
                                            </div>

                                            <div class="mb-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Metode Pembayaran</label>
                                                    <select name="metode_pembayaran"
                                                        class="form-select @error('metode_pembayaran') is-invalid @enderror"
                                                        required>
                                                        <option value="">Pilih Metode Pembayaran</option>
                                                        <option value="tunai">Tunai</option>
                                                        <option value="transfer">Transfer Bank</option>
                                                    </select>
                                                    @error('metode_pembayaran')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div id="bukti-pembayaran-section" style="display: none;">
                                                <div class="mb-3">
                                                    <label class="form-label">Bukti Transfer</label>
                                                    <input type="file" name="bukti_pembayaran"
                                                        class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                                                        accept="image/*">
                                                    <small class="text-muted">Upload bukti transfer (max: 2MB)</small>
                                                    @error('bukti_pembayaran')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Untuk melakukan pembayaran biaya PPDB lainnya,
                                silakan hubungi Admin.
                            </div>
                        @endif
                        <div>


                            <!-- Tombol aksi -->
                            <div class="d-flex justify-content-between align-items-center mt-3 p-5">
                                <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ isset($pendaftaran) ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </div>
                </form>

                @push('scripts')
                    <script>
                        $(document).ready(function() {
                            // Toggle bukti pembayaran section
                            $('select[name="metode_pembayaran"]').change(function() {
                                if ($(this).val() == 'transfer') {
                                    $('#bukti-pembayaran-section').slideDown();
                                    $('input[name="bukti_pembayaran"]').prop('required', true);
                                } else {
                                    $('#bukti-pembayaran-section').slideUp();
                                    $('input[name="bukti_pembayaran"]').prop('required', false);
                                }
                            });

                            // Preview foto sebelum upload
                            function readURL(input) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        $('#preview').attr('src', e.target.result);
                                        $('#preview').show();
                                    };
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }

                            $("#foto").change(function() {
                                readURL(this);
                            });

                            // Form validation
                            $('form').submit(function(e) {
                                let isValid = true;

                                // Validate NISN
                                const nisn = $('#NISN').val();
                                if (!/^\d{10}$/.test(nisn)) {
                                    alert('NISN harus 10 digit angka');
                                    isValid = false;
                                }

                                // Validate grades
                                $('input[name^="nilai_semester_"]').each(function() {
                                    const nilai = $(this).val();
                                    if (nilai && (nilai < 0 || nilai > 100)) {
                                        alert('Nilai harus antara 0 dan 100');
                                        isValid = false;
                                    }
                                });

                                if (!isValid) {
                                    e.preventDefault();
                                }
                            });
                        });
                    </script>
                @endpush
</x-layout>

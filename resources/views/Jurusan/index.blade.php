<x-layout>
    <x-slot name="title">Manajemen Jurusan</x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Daftar Jurusan</h3>
            <button class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#addJurusanModal">
                Tambah Jurusan
            </button>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Jurusan</th>
                        <th>Deskripsi</th>
                        <th>Kapasitas/Kelas</th>
                        <th>Max Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jurusans as $jurusan)
                        <tr>
                            <td>{{ $jurusan->kode_jurusan }}</td>
                            <td>{{ $jurusan->nama_jurusan }}</td>
                            <td>{{ $jurusan->deskripsi }}</td>
                            <td>{{ $jurusan->kapasitas_per_kelas }}</td>
                            <td>{{ $jurusan->max_kelas }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editJurusanModal-{{ $jurusan->id }}">
                                    Edit
                                </button>
                                <form action="{{ route('jurusan.destroy', $jurusan->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus jurusan ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Jurusan -->
                        <div class="modal fade" id="editJurusanModal-{{ $jurusan->id }}" tabindex="-1"
                            aria-labelledby="editJurusanModalLabel-{{ $jurusan->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editJurusanModalLabel-{{ $jurusan->id }}">Edit Jurusan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('jurusan.update', $jurusan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="kode_jurusan" class="form-label">Kode Jurusan</label>
                                                <input type="text" class="form-control" id="kode_jurusan"
                                                    name="kode_jurusan" value="{{ $jurusan->kode_jurusan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nama_jurusan" class="form-label">Nama Jurusan</label>
                                                <input type="text" class="form-control" id="nama_jurusan"
                                                    name="nama_jurusan" value="{{ $jurusan->nama_jurusan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ $jurusan->deskripsi }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="kapasitas_per_kelas" class="form-label">Kapasitas Per Kelas</label>
                                                <input type="number" class="form-control" id="kapasitas_per_kelas"
                                                    name="kapasitas_per_kelas"
                                                    value="{{ $jurusan->kapasitas_per_kelas }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="max_kelas" class="form-label">Max Kelas</label>
                                                <input type="number" class="form-control" id="max_kelas"
                                                    name="max_kelas" value="{{ $jurusan->max_kelas }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Jurusan -->
    <div class="modal fade" id="addJurusanModal" tabindex="-1" aria-labelledby="addJurusanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJurusanModalLabel">Tambah Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jurusan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_jurusan" class="form-label">Kode Jurusan</label>
                            <input type="text" class="form-control" id="kode_jurusan" name="kode_jurusan" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_jurusan" class="form-label">Nama Jurusan</label>
                            <input type="text" class="form-control" id="nama_jurusan" name="nama_jurusan"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas_per_kelas" class="form-label">Kapasitas Per Kelas</label>
                            <input type="number" class="form-control" id="kapasitas_per_kelas"
                                name="kapasitas_per_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_kelas" class="form-label">Max Kelas</label>
                            <input type="number" class="form-control" id="max_kelas" name="max_kelas" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

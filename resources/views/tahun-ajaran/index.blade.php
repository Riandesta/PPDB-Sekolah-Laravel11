<x-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Manajemen Tahun Ajaran</h3>
                <a href="{{ route('tahun-ajaran.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Tahun Ajaran
                </a>
            </div>
            <div class="card-body">
                {{-- @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif --}}

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tahunAjaranTable">
                        <thead>
                            <tr>
                                <th>Tahun Ajaran</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Total Biaya</th>
                                <th>Total Pendaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <tr>
                                    <td>{{ $tahunAjaran->tahun_ajaran }}</td>
                                    <td>
                                        {{ $tahunAjaran->tanggal_mulai->format('d/m/Y') }} -
                                        {{ $tahunAjaran->tanggal_selesai->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge text-white {{ $tahunAjaran->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $tahunAjaran->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($tahunAjaran->total_biaya, 0, ',', '.') }}</td>
                                    <td>{{ $tahunAjaran->pendaftarans_count ?? 0 }} siswa</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('tahun-ajaran.edit', $tahunAjaran->id) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('tahun-ajaran.show', $tahunAjaran->id) }}"
                                               class="btn btn-sm btn-secondary">
                                                <i class="fas fa-info-circle"></i> Detail
                                            </a>
                                            <form action="{{ route('tahun-ajaran.destroy', $tahunAjaran->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?');"
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
$(document).ready(function() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#tahunAjaranTable')) {
        $('#tahunAjaranTable').DataTable().destroy();
    }

    // Initialize DataTable
    $('#tahunAjaranTable').DataTable({
        order: [[0, 'desc']]
    });

    // Confirm delete
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')) {
            $(this).closest('form').submit();
        }
    });
});
</script>
@endpush

</x-layout>

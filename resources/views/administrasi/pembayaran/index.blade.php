<x-layout>
    @slot('title')
        Manajemen Pembayaran
    @endslot

    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    @endpush

    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Pembayaran</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable-pembayaran">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Siswa</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Total Bayar</th>
                                <th>Sisa Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatable-pembayaran').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('administrasi.pembayaran.data') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'pendaftaran.daftar_id', name: 'pendaftaran.daftar_id' },
                    { data: 'pendaftaran.nama', name: 'pendaftaran.nama' },
                    { data: 'pendaftaran.jurusan.nama_jurusan', name: 'pendaftaran.jurusan.nama_jurusan' },
                    { data: 'total_bayar_formatted', name: 'total_bayar' },
                    { data: 'sisa_pembayaran_formatted', name: 'sisa_pembayaran' },
                    { data: 'status_badge', name: 'status_pembayaran' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'asc']],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
    </script>
    @endpush
</x-layout>

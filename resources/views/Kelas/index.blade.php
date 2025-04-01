<x-layout>
    <x-slot name="header">Manajemen Kelas</x-slot>

    <div class="container">
        <div class="row">
            @foreach ($kelasGroup as $jurusanNama => $kelasCollection)
                <div class="col-12 mb-4">
                    <h4>{{ $jurusanNama }}</h4>
                    <div class="row">
                        @foreach ($kelasCollection as $kelas)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $kelas->nama_kelas }}</h>
                                        <p class="card-text">
                                            Tahun Ajaran: {{ $kelas->tahun_ajaran }}<br>
                                            Kapasitas: {{ $kelas->kapasitas_saat_ini }} /
                                            {{ $kelas->jurusan->kapasitas_per_kelas ?? config('ppdb.default_kapasitas', 30) }}
                                        </p>
                                        <a href="{{ route('kelas.show', $kelas->id) }}" class="btn btn-primary btn-sm">
                                            Lihat Siswa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>

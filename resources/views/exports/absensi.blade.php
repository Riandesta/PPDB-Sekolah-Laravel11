<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>L/P</th>
            <th>Jurusan</th>
            <th>Tahun Ajaran</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kelas->pendaftaran as $index => $siswa)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $siswa->NISN }}</td>
            <td>{{ $siswa->nama }}</td>
            <td>{{ $siswa->jenis_kelamin }}</td>
            <td>{{ $kelas->jurusan->nama_jurusan }}</td>
            <td>{{ $kelas->tahun_ajaran }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

use Carbon\Carbon;
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Data Siswa Kelas {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.3;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 0.5px solid #000;
            padding: 7px 10px;
            font-size: 11px;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        td {
            height: 20px; /* Fixed height for rows */
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Siswa</h2>
        <p>Kelas: {{ $kelas->nama_kelas }}</p>
        <p>Jurusan: {{ $kelas->jurusan->nama_jurusan }}</p>
        <p>Tahun Ajaran: {{ $kelas->tahun_ajaran }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">NISN</th>
                <th style="width: 30%">Nama Siswa</th>
                <th style="width: 15%">Tanggal Lahir</th>
                <th style="width: 20%">Jurusan</th>
                <th style="width: 15%">Tahun Ajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelas->pendaftaran as $index => $siswa)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>{{ $siswa->NISN }}</td>
                <td>{{ $siswa->nama }}</td>
                <td style="text-align: center">{{ \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d/m/Y') }}</td>
                <td>{{ $kelas->jurusan->nama_jurusan }}</td>
                <td style="text-align: center">{{ $kelas->tahun_ajaran }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

<x-guest-layout>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-blue-600 to-blue-800 py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full md:w-1/2 text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Pendaftaran Peserta Didik Baru
                    </h1>
                    <p class="text-xl mb-8">
                        Selamat datang di Portal PPDB Online. Mari bergabung dengan kami untuk memulai perjalanan pendidikan Anda.
                    </p>
                    <div class="space-x-4">
                        <a href="{{ route('pendaftaran.create') }}"
                           class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                            Daftar Sekarang
                        </a>
                        <a href="#jurusan"
                           class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                            Lihat Jurusan
                        </a>
                    </div>
                </div>
                <div class="w-full md:w-1/2 mt-8 md:mt-0">
                    <img src="/images/hero-illustration.svg" alt="Hero" class="w-full">
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Section -->
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($statistics as $key => $stat)
                <div class="bg-white rounded-lg p-6 shadow-sm text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">
                        {{ $stat['value'] }}
                    </div>
                    <div class="text-gray-600">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Jurusan Section -->
    <div id="jurusan" class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Program Keahlian</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($jurusans as $jurusan)
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition transform hover:-translate-y-1 hover:shadow-xl">
                    <div class="p-6">
                        <div class="text-lg font-semibold text-blue-600 mb-2">
                            {{ $jurusan->kode_jurusan }}
                        </div>
                        <h3 class="text-xl font-bold mb-3">
                            {{ $jurusan->nama_jurusan }}
                        </h3>
                        <p class="text-gray-600 mb-4">
                            {{ $jurusan->deskripsi }}
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Kapasitas: {{ $jurusan->kapasitas_per_kelas * $jurusan->max_kelas }} siswa</span>
                            <span>{{ $jurusan->max_kelas }} kelas</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-guest-layout>

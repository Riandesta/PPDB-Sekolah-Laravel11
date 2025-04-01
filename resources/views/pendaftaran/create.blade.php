<x-guest-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h2 class="text-2xl font-bold">Form Pendaftaran PPDB</h2>
                    <p class="text-gray-600">Silakan lengkapi data berikut dengan benar</p>
                </div>

                <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Data Pribadi -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Data Pribadi</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">NISN</label>
                                <input type="text" name="NISN" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Tambahkan field lainnya -->
                        </div>

                        <!-- Data Akademik -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Data Akademik</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                                <select name="jurusan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nilai Akademik -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nilai Semester 1</label>
                                    <input type="number" name="nilai_semester_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <!-- Tambahkan semester lainnya -->
                            </div>

                    <!-- Pembayaran -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Informasi Pembayaran</h3>
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-blue-600">
                                Biaya pendaftaran: Rp {{ number_format($tahunAjaran->biaya_pendaftaran, 0, ',', '.') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label>
                            <input type="file" name="bukti_pembayaran" accept="image/*" class="mt-1 block w-full">
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG (max. 2MB)</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

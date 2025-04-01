<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-sm">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold">Struk Pembayaran PPDB</h2>
        <p class="text-gray-600">{{ config('app.name') }}</p>
    </div>

    <div class="border-t border-b py-4 space-y-3">
        <div class="flex justify-between">
            <span class="text-gray-600">No. Pembayaran:</span>
            <span class="font-semibold">{{ $pembayaran->no_pembayaran }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Tanggal:</span>
            <span>{{ $pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Nama Siswa:</span>
            <span>{{ $pembayaran->administrasi->pendaftaran->nama }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Jumlah Bayar:</span>
            <span class="font-semibold">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Status:</span>
            <span class="text-green-600 font-semibold">{{ $pembayaran->status }}</span>
        </div>
    </div>

    <div class="mt-6 text-center text-sm text-gray-500">
        <p>Terima kasih telah melakukan pembayaran</p>
        <p>Simpan struk ini sebagai bukti pembayaran yang sah</p>
    </div>

    <div class="mt-6 flex justify-center">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Cetak Struk
        </button>
    </div>
</div>

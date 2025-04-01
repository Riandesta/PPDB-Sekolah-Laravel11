<?php

return [
    'biaya_pendaftaran' => env('BIAYA_PENDAFTARAN', 100000),
    'biaya_ppdb' => env('BIAYA_PPDB', 5000000),
    'biaya_mpls' => env('BIAYA_MPLS', 250000),
    'biaya_awal_tahun' => env('BIAYA_AWAL_TAHUN', 1500000),

    'minimum_pembayaran' => env('MINIMUM_PEMBAYARAN', 100000),

    'metode_pembayaran' => [
        'tunai' => 'Tunai',
        'transfer' => 'Transfer Bank'
    ],
];

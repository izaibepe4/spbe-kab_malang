<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Strategy Configuration
    |--------------------------------------------------------------------------
    |
    | Smart caching strategy untuk optimize performance
    |
    */

    'berita' => [
        'list' => 10, // 10 menit - balance freshness & performance
        'detail' => 60, // 1 jam - artikel jarang berubah
        'old_articles' => 1440, // 24 jam - artikel lama (>1 tahun)
    ],

    'informasi' => [
        'list' => 15, // 15 menit
        'detail' => 60, // 1 jam
    ],

    'search' => [
        'results' => 5, // 5 menit - user expect fresh results
    ],

    'layanan' => [
        'list' => 10, // 10 menit
        'stats' => 15, // 15 menit untuk dashboard stats
    ],

    'regulasi' => [
        'list' => 30, // 30 menit - jarang berubah
        'categories' => 60, // 1 jam
    ],

    'pengetahuan' => [
        'list' => 30, // 30 menit
        'categories' => 60, // 1 jam
    ],

];

<?php
return [
    'nev' => 'Beállítások',
    'slug' => 'beallitasok',
    'handler' => \Infoinventory\Modules\Core\Beallitasok::class,
    'pages' => [
        'adminisztracio' => ['menupont' => 'Adminisztráció', 'lathatosag' => 'engedelyezettek', 'menuben' => true, 'sorrend' => 60, 'szulo' => true, 'ikon' => 'fogaskerek'],
        'beallitasok' => ['menupont' => 'Beállítások', 'lathatosag' => 'engedelyezettek', 'sorrend' => 61, 'szulo' => 'adminisztracio'],
    ],
];

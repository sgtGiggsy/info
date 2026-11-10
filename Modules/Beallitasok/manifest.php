<?php
return [
    'handler' => \Infoinventory\Modules\Beallitasok::class,
    'pages' => [
        'adminisztracio' => ['menupont' => 'Adminisztráció', 'lathatosag' => 'adminok', 'menuben' => true, 'sorrend' => 60, 'szulo' => true, 'ikon' => 'fogaskerek'],
        'beallitasok' => ['menupont' => 'Beállítások', 'lathatosag' => 'adminok', 'sorrend' => 61, 'szulo' => 'adminisztracio'],
    ],
];

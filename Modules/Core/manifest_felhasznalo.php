<?php
return [
    'nev' => "Felhasználó",
    'slug' => "felhasznalo",
    'handler' => \Infoinventory\Modules\Core\Felhasznalo::class,
    'pages' => [
        'felhasznalo' => ['menupont' => 'Felhasználó', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 70, 'szulo' => true, 'ikon' => 'sefsapka'],
        'profil' => ['menupont' => 'Profil', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 72, 'szulo' => 'felhasznalo'],
        'profil/szerkesztes' => ['menupont' => 'Beállítások', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 74, 'szulo' => 'felhasznalo'],
        'felhasznalok' => ['menupont' => 'Felhasználók', 'lathatosag' => 'engedelyezettek', 'menuben' => true, 'sorrend' => 63, 'szulo' => 'adminisztracio'],
    ],
];

<?php
return [
    'handler' => \Infoinventory\Modules\Autentikacio::class,
    'pages' => [
        'belepes' => ['menupont' => 'Belépés', 'lathatosag' => 'vendeg', 'menuben' => true, 'sorrend' => 80],
        'regisztracio' => ['menupont' => 'Regisztráció', 'lathatosag' => 'vendeg', 'menuben' => true, 'sorrend' => 81],
        'elfelejtettjelszo' => ['menupont' => 'Elfelejtett jelszó', 'lathatosag' => 'vendeg'],
        'jelszocsere' => ['menupont' => 'Jelszócsere', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 75, 'szulo' => 'profil'],
        'kilepes' => ['menupont' => 'Kilépés', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 76, 'szulo' => 'felhasznalo'],
    ],
];

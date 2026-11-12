<?php
return [
    'nev' => "Közlemények",
    'slug' => 'kozlemenyek',
    'handler' => \Infoinventory\Modules\Kozlemenyek\Kozlemeny::class,
    'pages' => [
        'fooldal' => ['menupont' => 'Főoldal', 'menuben' => true, 'sorrend' => 0, 'ikon' => 'oldalminilogo'],
        'kozlemeny' => ['menupont' => 'Közlemény'],
        'kozlemenyek' => ['menupont' => 'Közlemények', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 10]
    ]
];

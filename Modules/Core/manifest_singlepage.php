<?php
return [
    'nev' => "Egyszerű oldal",
    'slug' => 'singlepage',
    'handler' => \Infoinventory\Modules\Core\SinglePage::class,
    'pages' => [
        '403' => [], '404' => [], 'adatkezelesi-tajekoztato' => ['menupont' => 'Adatkezelési tájékoztató'],
        'security' => ['menupont' => 'Biztonsági irányelvek'], 'impresszum' => [],
        'media-ajanlat' => [], 'kapcsolat' => []
    ],
];

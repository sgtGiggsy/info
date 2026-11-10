<?php
return [
    'handler' => \Infoinventory\Modules\Recept::class,
    'pages' => [
        'fooldal' => ['menupont' => 'Főoldal', 'menuben' => true, 'sorrend' => 0, 'ikon' => 'oldalminilogo'],
        'recept' => ['menupont' => 'Recept'],
        'receptek' => ['menupont' => 'Receptek', 'menuben' => true, 'sorrend' => 10],
        'szakacskonyv' => ['menupont' => 'Szakácskönyvem', 'lathatosag' => 'tagok', 'menuben' => true, 'sorrend' => 30],
    ],
];

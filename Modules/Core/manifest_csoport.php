<?php
return [
    'nev' => 'Csoportok',
    'slug' => 'csoportok',
    'handler' => \Infoinventory\Modules\Core\CsoportHandler::class,
    'pages' => [
        'csoport' => ['menupont' => 'Csoport', 'lathatosag' => 'engedelyezettek', 'menuben' => false, 'sorrend' => 71],
        'csoportok' => ['menupont' => 'Csoportok', 'lathatosag' => 'engedelyezettek', 'menuben' => true, 'sorrend' => 64, 'szulo' => 'adminisztracio'],
    ],
];

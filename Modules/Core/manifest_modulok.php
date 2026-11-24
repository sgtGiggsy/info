<?php
return [
    'nev' => "Modulok",
    'slug' => "modulok",
    'handler' => \Infoinventory\Modules\Core\ModuleHandler::class,
    'pages' => [
        'modul' => ['menupont' => 'Modul', 'lathatosag' => 'engedelyezettek', 'menuben' => false, 'sorrend' => 86],
        'modulok' => ['menupont' => 'Modulok', 'lathatosag' => 'engedelyezettek', 'menuben' => true, 'sorrend' => 64, 'szulo' => 'adminisztracio']
    ],
];

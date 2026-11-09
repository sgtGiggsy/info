<?php

namespace Infoinventory\Modules;

class SinglePage extends Page
{
    protected array $views = [
        '401' => '401.php',
        '403' => '403.php',
        '404' => '404.php',
        'fooldal' => 'fooldal.php',
        'adatkezelesi-tajekoztato' => 'adatkezelesi-tajekoztato.php',
        'security' => 'security.php',
        'impresszum' => 'impresszum.php',
        'media-ajanlat' => 'media-ajanlat.php',
        'kapcsolat' => 'kapcsolat.php'
    ];

    public function __construct(string $type) {
        $this->title = $GLOBALS['ablakcim'];
        $file = $this->views[$type] ?? $this->views['404'];
        $this->selectedpage = $type;
        $this->canonical = ROOT_PATH . '/' . $type;
        include(ROOT_DIR . "/" . $file);
    }

    public function Render() : void {
        if(function_exists('Render'))
            Render();
    }
}
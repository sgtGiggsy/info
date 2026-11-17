<?php

namespace Infoinventory\Modules\Core;

class SinglePage extends Page
{
    protected ?string $egyedihiba;
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

    public function __construct(string $type, ?string $egyedihiba = null) {
        $this->title = ABLAK_CIM;
        $this->egyedihiba = $egyedihiba;
        $file = $this->views[$type] ?? $this->views['404'];
        $this->selectedpage = $type;
        $this->canonical = ROOT_PATH . '/' . $type;
        include(ROOT_DIR . "/" . $file);
    }

    public function Router(array $params): Page {
        return $this;
    }

    public function Render() : void {
        if(function_exists('Render'))
            Render($this->egyedihiba);
    }
}
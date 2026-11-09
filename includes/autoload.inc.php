<?php
spl_autoload_register(function ($class) {

    // namespace → mappa
    $prefix = 'Infoinventory\\';
    $baseDir = ROOT_DIR;

    // nem a mi namespace-ünk
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // levágjuk a prefixet
    $relativeClass = substr($class, strlen($prefix));

    // namespace → path
    $file = $baseDir . "/" . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        //echo $file;
        $module = $baseDir . "/" . str_replace('\\', '/', $relativeClass) . '/' . array_last(explode('/', str_replace('\\', '/', $relativeClass))) . '.php';
        //echo $module;
        if (file_exists($module)) {
            require $module;
        }
    }
});
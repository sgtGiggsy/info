<?php

namespace Infoinventory\Classes;

class Settings
{
    public static bool $foadmin = false;
    public static bool $admin = false;
    public static ?string $nonce = null;
    public static ?string $slug = null;
    public static array $jsfiles = array();
    public static array $PHPvarsToJS = array();
}
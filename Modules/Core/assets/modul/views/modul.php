<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

?><div class="normalcontent"><?php
?><h1><?=$this->modulnev ?? 'Ismeretlen modul'?></h1><?php
<?php

use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\ModuleLoader;

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

?><div class="normalcontent"><?php
    ?><h1>Modulok</h1><?php
    if(empty($this->modulok)) {
        ?><p>Nincsenek telepített modulok.</p><?php
    }
    else{
        ?><table>
            <thead>
                <tr>
                    <th>Modul neve</th>
                    <th>Állapot</th>
                </tr>
            </thead>
            <tbody><?php
            foreach($this->modulok as $slug => $modul) {
                ?><tr>
                    <td><?=$modul['nev']?></td>
                    <td><?=(array_key_exists($slug, $this->telepitett)) ? '<form method="post"><button type="submit" class="anchbutton" disabled>Telepítve</button></form>' : '<form action="' . ROOT_PATH . '/modul/install/' . $modul['slug'] . '" method="post"><input type="hidden" name="csrf_token" id="csrf_token" value="' . $_SESSION['csrf_token'] . '"><button type="submit" class="anchbutton">Telepítés</button></form>'?></td>
                </tr><?php
            }
            ?></tbody><?php
        ?></table><?php
    }
?></div><?php
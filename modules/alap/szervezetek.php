<?php

if(!@$mindolvas)
{
	getPermissionError();
}
else
{
    $statarr = array(1 => "Alakulat", 2 => "HM Tulajdon", 3 => "Civil beszállító");
    $szervezetek = mySQLConnect("SELECT szervezetek.id AS id,
            nev, rovid,
            statusz,
            GROUP_CONCAT(needle ORDER BY szervezetldap.id SEPARATOR '; ') AS ldapstring
	    FROM szervezetek
    	    LEFT JOIN szervezetldap ON szervezetek.id = szervezetldap.szervezet
        GROUP BY nev
        ORDER BY id;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/szervezetszerkeszt'">Új szervezet</button><?php
    }

    ?><div class="oldalcim">Szervezetek listája</div>
    <table id="szervezetek">
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, 'i', 'szervezetek')">Azonosító</th>
            <th class="tsorth" onclick="sortTable(1, 's', 'szervezetek')">Teljes megnevezés</th>
            <th class="tsorth" onclick="sortTable(2, 's', 'szervezetek')">Rövid név</th>
            <th class="tsorth" onclick="sortTable(3, 's', 'szervezetek')">Státusz</th>
            <th class="tsorth" onclick="sortTable(4, 's', 'szervezetek')">Azonosításhoz használt névtöredék</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($szervezetek as $szervezet)
    {
        $szervezetid = $szervezet['id'];
        
        ?><tr <?=($mindir) ? "class='kattinthatotr'" . "data-href='$RootPath/szervezetszerkeszt/$szervezetid'" : "" ?>>
            <td><?=$szervezet['id']?></td>
            <td><?=$szervezet['nev']?></td>
            <td><?=$szervezet['rovid']?></td>
            <td><?=$statarr[$szervezet['statusz']]?></td>
            <td><?=$szervezet['ldapstring']?></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}
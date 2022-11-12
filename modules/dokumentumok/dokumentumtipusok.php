<?php

if(!@$mindolvas)
{
	getPermissionError();
}
else
{
    $dokumentumtipusok = mySQLConnect("SELECT * FROM dokumentumtipusok ORDER BY nev;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/dokumentumtipus?action=addnew'">Új dokumentumtípus</button><?php
    }

    ?><div class="oldalcim">Dokumentumtípusok listája</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', '<?=$tipus?>?>')">ID</th>
                <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>?>')">Típus megnevezése</th>
            </tr>
        </thead>
        <tbody><?php
        foreach($dokumentumtipusok as $dokumentumtipus)
        {
            $dokumentumtipusid = $dokumentumtipus['id'];
            ?><tr <?=($mindir) ? "class='kattinthatotr'" . "data-href='$RootPath/dokumentumtipus/$dokumentumtipusid?action=edit'" : "" ?>>
                <td><?=$dokumentumtipus['id']?></td>
                <td><?=$dokumentumtipus['nev']?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}
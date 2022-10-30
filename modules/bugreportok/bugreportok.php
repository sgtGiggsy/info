<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $bugok = mySQLConnect("SELECT bugok.id AS id, cim, leiras, felhasznalok.nev AS felhasznalo, timestamp, oldal, bugtipusok.nev AS tipus, prioritasok.nev AS prioritas, bugok.prioritas AS prioritasid, lezaro, (SELECT nev FROM felhasznalok WHERE id = lezaro) AS lezaronev
        FROM bugok
            LEFT JOIN bugtipusok ON bugok.tipus = bugtipusok.id
            LEFT JOIN prioritasok ON bugok.prioritas = prioritasok.id
            LEFT JOIN felhasznalok ON bugok.felhasznalo = felhasznalok.id
        ORDER BY id ASC;");

    ?><div class="oldalcim">Buglista</div>
    <form action="<?=$RootPath?>/bugreportdb?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    foreach($bugok as $bug)
    {
        ?><div style="margin: 0 0 15px 0; padding: 4px; background-color: <?php if(!$bug['lezaro']) { switch($bug['prioritasid']) { case 5: echo 'red'; break; case 4: echo 'orange'; break; case 3: echo 'yellow'; break; case 2: echo 'blue'; break; case 1: echo 'grey'; } } else { echo 'green'; } ?> ">
            <div style="display: grid; grid-template-columns: 1fr 1fr; width: 100%">
                <div><?=$bug['tipus']?></div>
                <div class="right"><?=$bug['prioritas']?></div>
            </div>
            <h1><?=$bug['cim']?></h1>
            <small><?=$bug['timestamp']?></small>
            <div style="font-size: 1.3em; padding: 5px"><?=$bug['leiras']?></div>
            <div>Hiba helye: <?=$bug['oldal']?></div>
            <div>Jelentette: <?=$bug['felhasznalo']?></div>
            <?=($bug['lezaro']) ? "<div>Lezárta: " . $bug['lezaronev'] . "</div>" : "" ?>
        </div><?php
    }
}
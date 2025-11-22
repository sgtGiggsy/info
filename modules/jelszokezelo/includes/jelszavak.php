<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    
    $jelszavak = new MySQLHandler("SELECT id, uname, leiras
        FROM jelszokezelo_jelszavak
        ORDER BY leiras ASC");

    $oszlopok = array(
        array('nev' => 'Megjegyzés', 'tipus' => 's'),
        array('nev' => 'Felhasználónév', 'tipus' => 's'),
        array('nev' => 'Jelszó', 'tipus' => 's'),
        array('nev' => '', 'tipus' => 's')
    );

    if($csoportir && $_SESSION['unlockedmaster'])
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/jelszokezelo/jelszo?action=addnew'">Új jelszó felvitele</button><?php
    }
    elseif($csoportir && !$_SESSION['unlockedmaster'])
    {
        ?><button type="button" onclick="enterMasterPass()">Feloldás az új jelszó rögzítéséhez</button><?php
    }

    ?><div class="oldalcim">Jelszavak listája</div>
    <table id="jelszolista">
        <thead>
            <tr><?php
                sortTableHeader($oszlopok, 'jelszolista');
            ?></tr>
        </thead>
        <tbody><?php

        foreach($jelszavak->Result() as $jelszo)
        {
            if($_SESSION['unlockedmaster'])
                $kattinthatolink = "href='" . $RootPath . "/jelszokezelo/jelszo/" . $jelszo['id'] . "'";
            else
                $kattinthatolink = "onclick='showPass(" . $jelszo['id'] . ")' style='cursor:pointer'";

            ?><tr class="trlink">
                <td><a <?=$kattinthatolink?>><?=$jelszo['leiras']?></a></td>
                <td><a <?=$kattinthatolink?>><?=$jelszo['uname']?></a></td>
                <td><span onclick="showPass(<?=$jelszo['id']?>)" id="jelszo-<?=$jelszo['id']?>">********</span></td>
                <td><?php
                    if($csoportir && $_SESSION['unlockedmaster'])
                    {
                        ?><a href='<?=$RootPath?>/jelszokezelo/jelszo/<?=$jelszo['id']?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Jelszó szerkesztése' title='Jelszó szerkesztése'/></a><?php
                    }
                ?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}
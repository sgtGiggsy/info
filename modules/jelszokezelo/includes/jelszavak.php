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
        array('nev' => 'Felhasználónév', 'tipus' => 's'),
        array('nev' => 'Megjegyzés', 'tipus' => 's'),
        array('nev' => 'Jelszó', 'tipus' => 's'),
        array('nev' => '', 'tipus' => 's')
    );

    if($csoportir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/jelszokezelo/jelszo?action=addnew'">Új jelszó felvitele</button><?php
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
            $kattinthatolink = $RootPath . "/jelszokezelo/jelszo/" . $jelszo['id'];
            ?><tr class="trlink">
                <td><a href="<?=$kattinthatolink?>"><?=$jelszo['leiras']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$jelszo['uname']?></a></td>
                <td><span onclick="showPass(<?=$jelszo['id']?>)" id="jelszo-<?=$jelszo['id']?>">********</span></td><?php
                if($csoportir)
                {
                    ?><td><a href='<?=$RootPath?>/jelszokezelo/jelszo/<?=$jelszo['id']?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Jelszó szerkesztése' title='Jelszó szerkesztése'/></a></td><?php
                }
            ?></tr><?php
        }
        ?></tbody>
    </table><?php
}
<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $alegysegek = mySQLConnect("SELECT telefonkonyvcsoportok.id AS telcsopid,
            telefonkonyvcsoportok.nev AS nev,
            telefonkonyvcsoportok.sorrend AS sorrend,
            (SELECT COUNT(id) FROM telefonkonyvbeosztasok WHERE csoport = telcsopid) AS beosztasok
        FROM telefonkonyvcsoportok
        WHERE telefonkonyvcsoportok.torolve IS NULL AND telefonkonyvcsoportok.id > 1
        ORDER BY telefonkonyvcsoportok.sorrend;");

    if($globaltelefonkonyvadmin) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyv/alegyseg?action=addnew'">Új alegység</button><?php
    }
    
    ?><div class="oldalcim">Alegységek</div>
    <table id="alegysegek">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(1, 's', 'alegysegek')">Sorrend</th>
                <th class="tsorth" onclick="sortTable(0, 's', 'alegysegek')">Alegység neve</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php
            foreach($alegysegek as $alegyseg)
            {
                $alegysegid = $alegyseg['telcsopid'];
                ?><tr>
                    <td><?=$alegyseg['sorrend']?></td>
                    <td><?=$alegyseg['nev']?></td>
                    <td><?=($csoportir) ? "<a href='$RootPath/telefonkonyv/alegyseg/$alegysegid?action=edit'><img src='$RootPath/images/edit.png' alt='Alegység szerkesztése' title='Alegység szerkesztése'/></a>" : "" ?></td>
                    <td><?php
                        if($globaltelefonkonyvadmin)
                        {
                            ?><form action="<?=$RootPath?>/telefonkonyv/alegyseg?action=delete" method="post" onsubmit="return confirm('FIGYELEM!\n\nA törölni kívánt alegységhez <?=$alegyseg['beosztasok']?> beosztás tartozik!\nBiztosan törölni szeretnéd ezt az alegységet?'); beKuld.disabled = true; return true;">
                                <input type ="hidden" id="id" name="id" value=<?=$alegysegid?>>
                                <input type ="hidden" id="sorrend" name="sorrend" value=<?=$alegyseg['sorrend']?>>
                                <input type="submit" name="beKuld" value="X">
                            </form><?php
                        }
                    ?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}
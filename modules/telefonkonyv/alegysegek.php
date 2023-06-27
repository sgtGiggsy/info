<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE torolve IS NULL ORDER BY sorrend;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyvalegyseg?action=addnew'">Új alegység</button><?php
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
                $alegysegid = $alegyseg['id'];
                ?><tr>
                    <td><?=$alegyseg['sorrend']?></td>
                    <td><?=$alegyseg['nev']?></td>
                    <td><?=($csoportir) ? "<a href='$RootPath/telefonkonyvalegyseg/$alegysegid?action=edit'><img src='$RootPath/images/edit.png' alt='Alegység szerkesztése' title='Alegység szerkesztése'/></a>" : "" ?></td>
                    <td><?php
                        if($mindir)
                        {
                            ?><form action="<?=$RootPath?>/telefonkonyvalegyseg?action=delete" method="post" onsubmit="return confirm('Biztosan törölni szeretnéd ezt az alegységet?'); beKuld.disabled = true; return true;">
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
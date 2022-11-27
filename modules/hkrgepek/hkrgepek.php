<?php
if(!$mindolvas)
{
    getPermissionError();
}
else
{
    $lista = mySQLConnect("SELECT hkrgepek.id AS id, gepnev, felhasznalok.nev AS nev, utolsofrissites, utolsoeredmeny
        FROM hkrgepek
            LEFT JOIN felhasznalok ON hkrgepek.felhasznalo = felhasznalok.id
        ORDER BY nev;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/hkrszerkeszt'">Új gép</button><?php
    }
    ?><div class='oldalcim'>Frissítendő gépek listája</div>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Gépnév</th>
                    <th>Felhasználó</th>
                    <th>Utolsó frissítési kísérlet</th>
                    <th>Utolsó frissítés ideje</th>
                </tr>
            </thead>
            <tbody><?php
            foreach($lista as $x)
            {
                $hkrid = $x['id'];
                ?><tr <?=($mindir) ? "class='kattinthatotr'" . "data-href='$RootPath/hkrszerkeszt/$hkrid'" : "" ?>>
                    <td><?=$x['gepnev']?></td>
                    <td><?=$x['nev']?></td>
                    <td><?=($x['utolsoeredmeny'] == 1) ? "Sikeres" : "Sikertelen" ?></td>
                    <td><?=$x['utolsofrissites']?></td>
                </tr><?php
            }
            ?></tbody>
        </table>
    </div><?php
}
?>
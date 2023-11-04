<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE ipcimek.ipcim LIKE '%$keres%' OR ipcimek.vlan LIKE '%$keres%' OR ipcimek.eszkoz LIKE '%$keres%' OR beepitesek.nev LIKE '%$keres%' OR ipcimek.megjegyzes LIKE '%$keres%'";
    }

    $ipcimek = mySQLConnect("SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim, ipcimek.vlan AS vlan, ipcimek.eszkoz AS eszkoz, vlanok.nev AS vlannev, beepitesek.nev AS beepitesnev, beepitesideje, kiepitesideje, ipcimek.megjegyzes AS megjegyzes, leadva, sorozatszam, eszkozok.id AS eszkid, beepitesek.id AS beepid
        FROM ipcimek
            LEFT JOIN vlanok ON ipcimek.vlan = vlanok.id
            LEFT JOIN beepitesek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
        $where
        ORDER BY beepitesek.beepitesideje DESC;");
    if($mindir)
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/ipszerkeszt'">Új rezervált IP</button><?php
    }

    $ipcimek = mysqliNaturalSort($ipcimek, 'ipcim')

    ?><div class="oldalcim">Rezervált IP címek listája</div><?php
    $zar = false;
    $elozoipk = array();
    $ipcimekszurt = array();
    $elozoip = null;

    foreach($ipcimek as $ipcim)
    {
        $elozoip = end($elozoipk);

        // echo "Előző: " . @$elozoip['ipcim'] . " mostani: " . $ipcim['ipcim'] . "<br>";
        if($elozoip != null && $elozoip['ipcim'] != $ipcim['ipcim'])
        {
            //Összehasonlítás
         //   echo count($elozoipk); echo " - ";
           // echo $elozoipk[0]['ipcim']; echo "<br>";


           usort($elozoipk, function($a, $b) {
                if($a["beepitesideje"] == null)
                {
                    $a["beepitesideje"] = "zzzzz";
                }
        
                if($b["beepitesideje"] == null)
                {
                    $b["beepitesideje"] = "zzzzz";
                }
        
                return strnatcmp($b["beepitesideje"], $a["beepitesideje"]); //Case sensitive
                //return strnatcasecmp($a['manager'],$b['manager']); //Case insensitive
            });

            //$ipcimekszurt[] = $elozoipk[0];

            $elemszam = count($elozoipk);

           for($i = 0; $i < $elemszam; $i++)
           {
                if($i == 0 && $elemszam > 1)
                {
                    $elozoipk[$i]["elozmenyek"] = true;
                    $elozoipk[$i]["szulo"] = true;
                }
                elseif($i == 0 && $elemszam == 1)
                {
                    $elozoipk[$i]["elozmenyek"] = false;
                    $elozoipk[$i]["szulo"] = false;
                }
                else
                {
                    $elozoipk[$i]["elozmenyek"] = true;
                    $elozoipk[$i]["szulo"] = false;
                }


                $ipcimekszurt[] = $elozoipk[$i];
                //echo "ip: " . $elozoipk[$i]['ipcim'] . " - " . $elozoipk[$i]["elozmenyek"] . "<br>";
           }


            // Lezárás
            $elozoipk = array();
            $elozoipk[] = $ipcim;
        }
        else
        {
            // echo "ipadd: " . $ipcim['ipcim'] . "<br>";
            $elozoipk[] = $ipcim;
        }
    }

    $altabla = false;
    $elozmenyid = 1;

    foreach($ipcimekszurt as $ipcim)
    {
        $ipid = $ipcim['id'];
        $hasznalatban = false;
        $volthasznalva = true;

        if($ipcim['eszkoz'] || $ipcim['beepid'])
        {
            if($ipcim['beepitesnev'] && $ipcim['beepitesideje'] && !$ipcim['kiepitesideje'])
            {
                $hasznalatban = true;
                $eszkoz = $ipcim['beepitesnev'];
            }
            elseif($ipcim['beepitesnev'] && !$ipcim['beepitesideje'] || $ipcim['kiepitesideje'])
            {
                $eszkoz = $ipcim['beepitesnev'];
            }

            if($ipcim['eszkoz'] && !$hasznalatban)
            {
                $eszkoz = $ipcim['eszkoz'];
                $hasznalatban = true;
            }
        }
        else
        {
            $volthasznalva = false;
        }

       // echo "ipcim: " . $ipcim['ipcim'] . " altab: " . $altabla . " elozmenyek: " . $ipcim['elozmenyek'] . " szülő: " . $ipcim['szulo'] . "<br>";
        if($altabla && ($ipcim['szulo'] || !$ipcim['elozmenyek']))
        {
                        ?></tbody>
                    </table>
                </td>
            </tr><?php
            $altabla = false;
        }
        
        if(@$tableid != $ipcim['vlan'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $tableid = $ipcim['vlan']
            ?><h1 style="text-transform: capitalize;"><?=$ipcim['vlannev']?> (<?=$ipcim['vlan']?>)</h1>
            <table id="<?=$tableid?>">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', '<?=$tableid?>')">IP cím</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$tableid?>')">VLAN</th>
                    <th class="tsorth" onclick="sortTable(2, 's', '<?=$tableid?>')">Eszköz</th>
                    <th class="tsorth" onclick="sortTable(3, 's', '<?=$tableid?>')">Megjegyzes</th>
                    <th class="tsorth">Előzmények</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }
        

        if(!$ipcim['elozmenyek'] || $ipcim['szulo'])
        {
            ?><tr <?=($hasznalatban) ? "style='font-weight: bold'" : "" ?>>
                <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?>'><?=$ipcim['ipcim']?></td>
                <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?>'><?=$ipcim['vlan']?></td>
                <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?> <?=($ipcim['leadva']) ? "mukodeskeptelen" : "" ?>' ><?=$eszkoz?></td>
                <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?> <?=($ipcim['leadva']) ? "mukodeskeptelen" : "" ?>' ><?=$ipcim['megjegyzes']?></td>
                <td><?=($ipcim['szulo']) ? "<a style='cursor: pointer' onclick=\"rejtMutat('elozmeny-$elozmenyid')\">+</a>" : "" ?></td>
                <td><?=($csoportir) ? "<a href='$RootPath/ipszerkeszt/$ipid'><img src='$RootPath/images/edit.png' alt='IP cím szerkesztése' title='IP cím szerkesztése'/></a>" : "" ?></td>
            </tr><?php
            if($ipcim['szulo'])
            {
                $altabla = true;
                ?><tr id="elozmeny-<?=$elozmenyid?>" style="display:none">
                    <td colspan=6>
                        <table>
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>Eszköz</td>
                                    <td>Sorozatszám</td>
                                    <td>Beépítés ideje</td>
                                    <td>Kiépítés ideje</td>
                                </tr>
                            </thead>
                            <tbody><?php
                $elozmenyid++;
            }
        }
        else
        {
            ?><tr>
                <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
                <td><?=$eszkoz?></td>
                <td><?=$ipcim['sorozatszam']?></td>
                <td><?=$ipcim['beepitesideje']?></td>
                <td><?=$ipcim['kiepitesideje']?></td>
            </tr><?php
        }
        $eszkoz = null;
        
    }
    ?></tbody>
    </table><?php
    $enablekeres = true;
}
<?php
// Egyelőre kész
if(!$contextmenujogok['admin'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    if(isset($_GET['action']) && $_GET['action'] == "addnew")
    {
        $irhat = true;
        $kerdesidlist = mySQLConnect("SELECT id FROM vizsgak_kerdesek WHERE vizsga = $vizsgaid AND kep IS NULL;");
        $valasztlist = mySQLConnect("SELECT kerdes, helyes FROM vizsgak_valaszlehetosegek ORDER BY kerdes ASC, id ASC;");
        $kerdesdb = mysqli_num_rows($kerdesidlist);
        $kerdesidlist = mysqliToArray($kerdesidlist);

        $vizsgalapkerdesei = array();
        $megoldokulcs = "";
        $kerdessorszam = 0;
        $sikertelen = false;

        for($i = 0; $i < $vizsgaadatok['kerdesszam']; $i++)
        {
            $loopcount = 0;
            $kerdessorszam++;

            do
            {
                $kovkerdes = $kerdesidlist[array_rand($kerdesidlist)]['id'];
                $loopcount++;
            } while(in_array($kovkerdes, $vizsgalapkerdesei) && $loopcount < 50);

            if($loopcount == 50)
            {
                $sikertelen = true;
                break;
            }

            $vizsgalapkerdesei[] = $kovkerdes;

            $megoldokulcs .= $kerdessorszam . ") ";
            $valasznev = "a";

            foreach($valasztlist as $valasz)
            {
                $megvan = false;
                if($valasz['kerdes'] == $kovkerdes)
                {
                    if($valasz['helyes'] == 1)
                    {
                        $megoldokulcs .= $valasznev . ", ";
                    }
                    $valasznev++;
                }
            }
            $megoldokulcs = rtrim($megoldokulcs, ", ");
            $megoldokulcs .= "; ";
        }

        $megoldokulcs = rtrim($megoldokulcs, "; ");

        include("./modules/vizsgak/db/vizsgalapdb.php");
        $vizsgaurl = $vizsgaadatok['url'];
        header("Location: $RootPath/vizsga/$vizsgaurl/vizsgalapok");
    }

    else
    {
        $kerdeseklistaja = mySQLConnect("SELECT vizsgak_vizsgalapok.azonosito AS azonosito,
                vizsgak_kerdesek.id AS kerdesid,
                vizsgak_kerdesek.kerdes AS kerdes,
                vizsgak_valaszlehetosegek.valaszszoveg AS valasz
            FROM vizsgak_vizsgalapkerdesek
                INNER JOIN vizsgak_vizsgalapok ON vizsgak_vizsgalapkerdesek.vizsgalapid = vizsgak_vizsgalapok.id
                INNER JOIN vizsgak_kerdesek ON vizsgak_vizsgalapkerdesek.kerdesid = vizsgak_kerdesek.id
                INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_valaszlehetosegek.kerdes = vizsgak_kerdesek.id
            WHERE vizsgak_vizsgalapkerdesek.vizsgalapid = $id
            ORDER BY vizsgak_kerdesek.id, vizsgak_valaszlehetosegek.id;");

        if(!$kerdeseklistaja || mysqli_num_rows($kerdeseklistaja) == 0)
        {
            echo "<h2>Nem létezik ilyen azonosítójú vizsgalap!</h2>";
        }
        else
        {
            $vlapazonosito = mysqli_fetch_assoc($kerdeseklistaja)['azonosito'];
            $sorsz = 0;
            ?><div class="PrintArea">
                <div class="hiddenid"><small><?=$vlapazonosito?></small></div>
                <div class="vizsgalap">
                    <div class="center"><h1><?=$vizsgaadatok['nev']?></h1></div>
                    <div class="nevrffejlec">
                            <div>
                                <p>Név, rf.:&nbsp;<span>.............................................................................................................................................................................................</span></p>
                                <p>Alegység:&nbsp;<span>.............................................................................................................................................................................</span></p>
                            </div>
                            <div>
                                <p>Kitöltés dátuma:&nbsp;<span>...........................................</span></p>
                            </div>
                    </div>
                    <div class='contentcenter'><?php
                        foreach($kerdeseklistaja as $kerdes)
                        {
                            if(!isset($kerdesid) || $kerdes['kerdesid'] != $kerdesid)
                            {
                                if(isset($kerdesid))
                                {
                                    ?></div><?php
                                }

                                $kerdesid = $kerdes['kerdesid'];
                                $valasznev = "a";
                                $sorsz++;

                                ?><div class="vizsgalapkerdesblokk">
                                    <h3 style="text-align: justify;"><?=$sorsz?>. <?=$kerdes['kerdes']?></h3><?php
                            }
                            
                            ?><p class="vizsgalapvalasz">(<?=$valasznev++?>) <?=$kerdes['valasz']?></p><?php
                        }
                        ?></div>
                    </div>
                </div>
            </div><?php
        }
    }

    if(isset($_GET['action']) && $_GET['action'] == "print")
    {
        $javascriptfiles[] = "modules/vizsgak/includes/print.js";
    }
}
<?php
header('Content-Type: Content-Type: text/html; charset=utf-8');
$felhasznaloid = @$_SESSION['id'];
//$felhasznaloid = 1;
if($felhasznaloid)
{
    $feladatok = new MySQLHandler("WITH RECURSIVE feladat_hierarchy AS (SELECT rovid, prioritas, allapot, szulo,
                        ido_tervezett, ido_hatarido,
                        feladat_id,
                        felvitte,
                        epulet,
                        szakid,
                        1 AS szint,
                        CAST(LPAD(feladat_id, 7, 0) AS CHAR(7)) AS path
                    FROM feladatterv_feladatok
                    WHERE feladatterv_feladatok.allapot NOT IN (0, 3) AND feladatterv_feladatok.aktiv = 1 AND feladatterv_feladatok.szulo IS NULL
                UNION ALL
                    SELECT gy.rovid, gy.prioritas, gy.allapot, gy.szulo,
                        gy.ido_tervezett, gy.ido_hatarido,
                        gy.feladat_id,
                        gy.felvitte,
                        gy.epulet,
                        gy.szakid,
                        fh.szint + 1, 
                        CONCAT(fh.path, LPAD(gy.feladat_id, 7, 0)) 
                    FROM feladatterv_feladatok gy
                        INNER JOIN feladat_hierarchy fh ON gy.szulo = fh.feladat_id
                    WHERE gy.allapot NOT IN (0, 3) AND gy.aktiv = 1
                )
                SELECT rovid, prioritas, allapot, szulo, ido_tervezett, ido_hatarido, feladat_hierarchy.feladat_id, felvitte, epulet, szakid, szint, path,
                    prioritasok.nev AS prioritasnev,
                    szakok.nev AS szaknev
                FROM feladat_hierarchy
                    LEFT JOIN feladatterv_felelosok ON feladat_hierarchy.feladat_id = feladatterv_felelosok.feladat_id
                    LEFT JOIN prioritasok ON feladat_hierarchy.prioritas = prioritasok.id
                    LEFT JOIN szakok ON feladat_hierarchy.szakid = szakok.id
                WHERE feladatterv_felelosok.felhasznalo_id = ? OR feladat_hierarchy.felvitte = ?
                GROUP BY feladat_hierarchy.feladat_id
                ORDER BY FIELD(allapot, 3), ido_tervezett IS NULL, ido_tervezett ASC, path", $felhasznaloid, $felhasznaloid);
    if($feladatok->sorokszama > 0)
    {
        ?><div><h1>Feladatok</h1>
        <?php
        foreach($feladatok->Result() as $feladat)
        {
            if($feladat['ido_hatarido'])
            {
                $untildeadline = strtotime($feladat['ido_hatarido']) - time();
                $urgentdeadline = ($untildeadline < 172800) ? true : false;
            }

            switch($feladat['allapot'])
            {
                case 0 : $allapot = "Sikertelen"; break;
                case 1 : $allapot = "Megkezdetlen"; break;
                case 2 : $allapot = "Folyamatban"; break;
                case 3 : $allapot = "Befejezve"; break;
            }
            
            ?><a href="<?=$RootPath?>/feladatterv/<?=($feladat['szulo']) ? $feladat['szulo'] : $feladat['feladat_id'] ?>"><div class="feladatwidgetelem <?=($feladat['szulo']) ? 'feladatwidgetchild' : '' ?>">
                <div <?=($urgentdeadline) ? "class='kritikus-font'" : "" ?>><h2><?=$feladat['rovid']?></h2></div>
                <div><i><?=($feladat['ido_tervezett'] < $feladat['ido_hatarido']) ? $feladat['ido_tervezett'] : $feladat['ido_hatarido'] ?></i></div>
                <div><small><?=$feladat['prioritasnev']?> - <?=$allapot?></small></div>
            </div></a><?php
        }
        ?></div><?php
    }
}
else
{
    http_response_code(403);
    echo "Nincs jogosultsága az adat megjelenítésére!";
}
<?php
$fejlecszoveg = new MySQLHandler("SELECT ertek FROM beallitasok WHERE nev = 'munkalapfejlec'");
$fejlecszoveg = $fejlecszoveg->Fetch()['ertek'];

$munka = new MySQLHandler("SELECT munkalapok.id AS id, hely, telephelyek.telephely AS telephely, epuletek.szam AS epulet, epulettipusok.tipus AS eptipus, helyisegek.helyisegszam AS helyiseg, igenylo, igenylesideje, vegrehajtasideje, munkavegzo1, munkavegzo2, leiras, eszkoz, ugyintezo,
            igenylonev,
            igenylotelefon,
            igenyloszervezet,
            munkavegzo1nev,
            munkavegzo1beosztas,
            munkavegzo1telefon,
            munkavegzo2nev,
            munkavegzo2beosztas,
            munkavegzo2telefon,
            felhasznalok.nev AS ugyintezonev,
            felhasznalok.telefon AS ugyintezotelefon,
            varos
        FROM munkalapok
            LEFT JOIN helyisegek ON munkalapok.hely = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN felhasznalok ON munkalapok.ugyintezo = felhasznalok.id
        WHERE munkalapok.id = ?;", $elemid);
$munka = $munka->Fetch();

?><div class="PrintArea munkalap">
    <div class="nostripes">
        <table class="munkalap-szekcio">
            <tr>
                <td class="munkalapfejlec-bal"><?=nl2br($fejlecszoveg)?></td>
                <td class="munkalapfejlec-jobb">
                    <span>1. számú példány</span>
                </td>
            </tr>
        </table>

        <p>Ikt. szám:</p>
        <p class="munkacimsor">Munkalap az informatika részére</p>

        <table class="munkalap-szekcio">
            <tr>
                <td class="munkaigenybal rejtett"></td>
                <td class="munkaigenyjobb rejtett"></td>
            </tr>
            <tr>
                <td colspan=2 class="boldtext">A munkaigény adatai</td>
            </tr>
            <tr>
                <td class="munkaigenybal">Munkavégzés helye:</td>
                <td class="munkaigenyjobb boldtext">
                    <?= $munka['telephely'] ?> <?= $munka['epulet'] ?>. <?= $munka['eptipus'] ?> <?= $munka['helyiseg'] ?>.
                </td>
            </tr>
            <tr>
                <td class="munkaigenybal">Szervezet:</td>
                <td class="munkaigenyjobb boldtext"><?= $munka['igenyloszervezet'] ?></td>
            </tr>
            <tr>
                <td class="munkaigenybal">Igénylő neve:</td>
                <td class="munkaigenyjobb boldtext"><?= $munka['igenylonev'] ?></td>
            </tr>
            <tr>
                <td class="munkaigenybal">Telefonszám:</td>
                <td class="munkaigenyjobb boldtext"><?= $munka['igenylotelefon'] ?></td>
            </tr>
            <tr>
                <td class="munkaigenybal">Igénylés dátuma:</td>
                <td class="munkaigenyjobb boldtext"><?=str_replace("-", ".", $munka['igenylesideje'])?></td>
            </tr>
            <tr>
                <td class="munkaigenybal">Végrehajtás dátuma:</td>
                <td class="munkaigenyjobb boldtext"><?=str_replace("-", ".", $munka['vegrehajtasideje'])?></td>
            </tr>
        </table>

        <table class="munkalap-szekcio">
            <tr>
                <td class="rejtett" width=249></td>
                <td class="rejtett" width=174></td>
                <td class="rejtett" width=96></td>
            </tr>
            <tr>
                <td colspan=3 class="boldtext">A feladatot végrehajtották:</td>
            </tr>
            <tr>
                <td class="boldtext center">Név, rendf.</td>
                <td class="boldtext center">Beosztás</td>
                <td class="boldtext center">Tel.</td>
            </tr>
            <tr>
                <td><?= $munka['munkavegzo1nev'] ?></td>
                <td class="center"><?= $munka['munkavegzo1beosztas'] ?></td>
                <td class="center"><?= $munka['munkavegzo1telefon'] ?></td>
            </tr><?php
            if($munka['munkavegzo2nev'])
            {
                ?><tr>
                    <td><?= $munka['munkavegzo2nev'] ?></td>
                    <td class="center"><?= $munka['munkavegzo2beosztas'] ?></td>
                    <td class="center"><?= $munka['munkavegzo2telefon'] ?></td>
                </tr><?php
            }
        ?></table>

        <table class="munkalap-szekcio">
            <tr>
                <td class="rejtett" width=158></td>
                <td class="rejtett" width=170></td>
                <td class="rejtett" width=313></td>
            </tr>
            <tr>
                <td colspan=3 class="boldtext">Munkavégzés paraméterei:</td>
            </tr>
            <tr>
                <td class="center">Helyben javított</td>
                <td class="center">Kiszállva javított</td>
                <td class="center">Kiszállás esetén az igénybe vett gépjármű:</td>
            </tr>
            <tr>
                <td class="center"><input type="checkbox"></td>
                <td class="center"><input type="checkbox"></td>
                <td></td>
            </tr>
        </table>

        <table class="munkalap-szekcio">
            <tr>
                <td class="rejtett"></td>
            </tr>
            <tr>
                <td class="boldtext">A végzett munka, hibafelvétel során megállapított hibák, megjegyzések:</td>
            </tr>
            <tr>
                <td><p><?=nl2br($munka['leiras'])?></p></td>
            </tr>
        </table>

        <table class="munkalap-szekcio">
            <tr>
                <td class="rejtett"></td>
            </tr>
            <tr>
                <td class="boldtext">Felhasznált anyagok/Számítógép név</td>
            </tr>
            <tr>
                <td><p><?= nl2br($munka['eszkoz']) ?></p></td>
            </tr>
        </table>

        <table class="munkalap-szekcio">
            <tr>
                <td class="rejtett"></td>
                <td class="rejtett"></td>
            </tr>
            <tr>
                <td colspan=2 class="boldtext" style="border-bottom:0">Átadás-átvétel</td>
            </tr>
            <tr>
                <td class='alairasok' style="border-right:0">
                    <p>
                        <br><br><br>
                        ................................................<br>
                        munkát igazoló személy aláírása<br>
                        A felhasznált eszközöket hiánytalanul,<br>
                        üzemképes állapotban átvettem
                    </p>
                </td>
                <td class='alairasok' style="border-left:0">
                    <p>
                        <br><br><br>
                        ................................................<br>
                        munkavégző aláírása<br>
                        A munkát elvégeztem,<br>
                        az eszközöket átadtam
                    </p>
                </td>
            </tr>
        </table><br>

        <p>Kelt: <?=$munka['varos']?>, <?=str_replace("-", ".", $munka['vegrehajtasideje'])?></p>

        <table class="munkalabjegy">
            <tr>
                <td width=100>Készült:</td>
                <td width=250>1 példányban</td>
                <td width=100></td>
            </tr>
            <tr>
                <td>Egy példány:</td>
                <td>1 lap</td>
                <td></td>
            </tr>
            <tr>
                <td>Ügyintéző, (tel.):</td>
                <td><?=$munka['ugyintezonev']?> (<?=$munka['ugyintezotelefon']?>)</td>
                <td>Tudomásul  vettem</td>
            </tr>
            <tr>
                <td>Kapják:</td>
                <td>1.sz. példány: Irattár</td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
<script>
    window.onload = function() {
        window.print();
        setTimeout(function() {
            close()
        }, 1);
        //close();
    }
</script>
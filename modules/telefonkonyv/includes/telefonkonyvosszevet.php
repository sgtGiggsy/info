<?php

Class TKObject
{
    public string $nev;
    public string $beosztas;
    public string $elotag;
    public string $jogallas;
    public string $rendfokozat;
    public string $telefon1;
    public string $telefon2;
    public string $kozcelu;
    public string $fax;
    public string $kozcelufax;
    public string $mobil;
    public string $alegyseg;
    public bool $megtalalt = false;

    public function __construct($nev, $beosztas, $elotag, $jogallas, $rendfokozat, $telefon, $kozcelu, $fax, $kozcelufax, $mobil, $alegyseg)
    {
        $this->nev = $nev;
        $this->beosztas = $beosztas;
        $this->elotag = $elotag;
        $this->jogallas = $jogallas;
        $this->rendfokozat = $rendfokozat;
        $this->kozcelu = $kozcelu;
        $this->fax = $fax;
        $this->kozcelufax = $kozcelufax;
        $this->mobil = $mobil;
        $this->alegyseg = $alegyseg;
        if(str_contains($telefon, ","))
        {
            $tmp = explode(",", $telefon);
            $this->telefon1 = $tmp[0];
            $this->telefon2 = $tmp[1];
        }
        else
        {
            $this->telefon1 = $telefon;
            $this->telefon2 = "";
        }
    }
}

if(isset($_FILES['bemenet1']) && isset($_FILES['bemenet2']))
{
    $osszevetes = $_POST['osszehasonlitas'];
    $kiemeles = $_POST['kiemeles'];
    $alespoz = $_POST['alespoz'];

    if(isset($_FILES["bemenet1"]) && isset($_FILES["bemenet2"]))
    {        
        $filetypes = array('csv');
        $mediatype = array('text/csv', 'application/vnd.ms-excel');

        $gyokermappa = "./uploads/";
        $egyedimappa = "telefonkonyv/osszevetesek";

        $bemenet1 = "./uploads/" . fajlFeltoltes($_FILES["bemenet1"], $filetypes, $mediatype, $gyokermappa, $egyedimappa, true)[0];
        $bemenet2 = "./uploads/" . fajlFeltoltes($_FILES["bemenet2"], $filetypes, $mediatype, $gyokermappa, $egyedimappa, true)[0];

        if($osszevetes == 0)
        {
            $ujtklist = csvToArray($bemenet1, false);
            $oldtklist = csvToArray($bemenet2, false);
        }
        else
        {
            $ujtklist = csvToArray($bemenet2, false);
            $oldtklist = csvToArray($bemenet1, false);
        }
    }
}
else
{
    $osszevetes = false;
}

if(false === $osszevetes)
{
    ?><form method="post" action="" enctype="multipart/form-data" onsubmit="beKuld.disabled = true; return true;">
        <div class="contentcenter">
            <div>
                <div>
                    <label for="bemenet1">Új telefonkönyv:</label>
                    <input type="file" name="bemenet1" id="bemenet1" accept=".csv">
                </div>

                <div>
                    <label for="bemenet2">Régi telefonkönyv:</label>
                    <input type="file" name="bemenet2" id="bemenet2" accept=".csv">
                </div>
                
                <div>
                    <label for="osszehasonlitas">Összevetés módja</label><br>
                    <select name="osszehasonlitas" id="osszehasonlitas">
                        <option value='0'>Régihez az új</option>
                        <option value='1'>Újhoz a régi</option>
                    </select>
                </div>

                <div>
                    <label for="kiemeles">Kiemelés módja</label><br>
                    <select name="kiemeles" id="kiemeles">
                        <option value='0'>Sárgával kijelöl</option>
                        <option value='1'>Csak a változás mutatása</option>
                    </select>
                </div>

                <div>
                    <label for="alespoz">Alegység mező helye</label><br>
                    <select name="alespoz" id="alespoz">
                        <option value='0'>Utolsó oszlop</option>
                        <option value='1'>Külön sor</option>
                    </select>
                </div>
            </div>

            <input type="submit" id='beKuld' value="Verziók összevetése">
        </div>
    </form><?php
}
else
{
    $alegyseg = "";
    $ujtkarray = array();
    $oldtkarray = array();
    $kimenetarray = array();

    foreach($ujtklist as $sor)
    {
        
        if($sor[0] == "Alegység")
            $alegyseg = $sor[1];
        else
        {
            if($sor[10] && $alespoz == 0)
                $alegyseg = $sor[10];

            $ujtkarray[] = new TKObject($sor[2], $sor[0], $sor[1], $sor[3], $sor[4], $sor[5], $sor[6], $sor[7], $sor[8], $sor[9], $alegyseg);
        }
    }

    foreach($oldtklist as $sor)
    {
        if($sor[0] == "Alegység")
            $alegyseg = $sor[1];
        else
            if($sor[10] && $alespoz == 0)
                $alegyseg = $sor[10];

            $oldtkarray[] = new TKObject($sor[2], $sor[0], $sor[1], $sor[3], $sor[4], $sor[5], $sor[6], $sor[7], $sor[8], $sor[9], $alegyseg);
    }

    foreach($oldtkarray as $oldelem)
    {
        foreach($ujtkarray as $ujelem)
        {
            if($oldelem->nev && ($ujelem->nev == $oldelem->nev || ($ujelem->nev && (str_contains($oldelem->nev, $ujelem->nev) || str_contains($ujelem->nev, $oldelem->nev)))))
            {
                $ujelem->megtalalt = true;
                $oldelem->megtalalt = true;
                
                if($ujelem == $oldelem)
                {
                    break;
                }
                else
                {
                    $elteresek = compareObjects($oldelem, $ujelem);
                    $elteresobj = new TKObject("", "", "", "", "", "", "", "", "", "", "");
                    //echo $oldelem->nev . ": <br>";
                    foreach($elteresek as $key => $value)
                    {
                        //echo "Régi $key :" . $oldelem->$value . " ";
                        //echo "Új $key :" . $ujelem->$value . "<br>";
                        $elteresobj->$value = $ujelem->$value;
                    }

                    if($osszevetes == 0)
                    {
                        $orig = $oldelem;
                        $new = $ujelem;
                    }
                    else
                    {
                        $orig = $ujelem;
                        $new = $oldelem;
                    }

                    if($kiemeles == 1)
                        $new = $elteresobj;

                    $kimenetarray[] = array("orig" => $orig, "new" => $new);
                }
            }
        }
        if(!$oldelem->megtalalt && $oldelem->nev)
        {
            $elteresobj = new TKObject("", "", "", "", "", "", "", "", "", "", "");
            $kimenetarray[] = array("orig" => $oldelem, "new" => $elteresobj);
        }
    }

    if($osszevetes == 0)
    {
        foreach($ujtkarray as $ujelem)
        {
            if(!$ujelem->megtalalt)
            {
                $elteresobj = new TKObject("", "", "", "", "", "", "", "", "", "", "");
                $kimenetarray[] = array("orig" => $elteresobj, "new" => $ujelem);
            }
        }
    }

    ?><table id="osszevetotabla">
        <thead>
            <tr>
                <th><a onclick="copyTableToClipboard('osszevetotabla')" style="cursor: pointer"><?=$icons['clipboard']?></a></th>            
                <th>Beosztás</th>
                <th>Elő</th>
                <th>Név</th>
                <th>Jogáll</th>
                <th>Rendfokozat</th>
                <th>Telefon1</th>
                <th>Telefon2</th>
                <th>Közcélú</th>
                <th>Fax</th>
                <th>Közcélúfax</th>
                <th>Mobil</th>
                <th>Alegység</th>
            </tr>
        </thead>
        <tbody><?php
        foreach($kimenetarray as $sor)
        {
            ?><tr>
                <td>Eredeti: </td>
                <td><?=$sor['orig']->beosztas?></td>
                <td><?=$sor['orig']->elotag?></td>
                <td><?=$sor['orig']->nev?></td>
                <td><?=$sor['orig']->jogallas?></td>
                <td><?=$sor['orig']->rendfokozat?></td>
                <td><?=$sor['orig']->telefon1?></td>
                <td><?=$sor['orig']->telefon2?></td>
                <td><?=$sor['orig']->kozcelu?></td>
                <td><?=$sor['orig']->fax?></td>
                <td><?=$sor['orig']->kozcelufax?></td>
                <td><?=$sor['orig']->mobil?></td>
                <td><?=$sor['orig']->alegyseg?></td>
            </tr>
            <tr>
                <td>Változás: </td>
                <td <?=($sor['orig']->beosztas != $sor['new']->beosztas && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->beosztas?></td>
                <td <?=($sor['orig']->elotag != $sor['new']->elotag && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->elotag?></td>
                <td <?=($sor['orig']->nev != $sor['new']->nev && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->nev?></td>
                <td <?=($sor['orig']->jogallas != $sor['new']->jogallas && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->jogallas?></td>
                <td <?=($sor['orig']->rendfokozat != $sor['new']->rendfokozat && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->rendfokozat?></td>
                <td <?=($sor['orig']->telefon1 != $sor['new']->telefon1 && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->telefon1?></td>
                <td <?=($sor['orig']->telefon2 != $sor['new']->telefon2 && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->telefon2?></td>
                <td <?=($sor['orig']->kozcelu != $sor['new']->kozcelu && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->kozcelu?></td>
                <td <?=($sor['orig']->fax != $sor['new']->fax && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->fax?></td>
                <td <?=($sor['orig']->kozcelufax != $sor['new']->kozcelufax && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->kozcelufax?></td>
                <td <?=($sor['orig']->mobil != $sor['new']->mobil && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->mobil?></td>
                <td <?=($sor['orig']->alegyseg != $sor['new']->alegyseg && $kiemeles == 0) ? "style='background-color: yellow'" : "" ?>><?=$sor['new']->alegyseg?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}
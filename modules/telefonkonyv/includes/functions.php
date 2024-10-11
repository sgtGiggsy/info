<?php

function telSzamSzetvalaszt($telszam, $elotaghossz, $telszamhossz)
{
    $elotag = substr($telszam, 0, $elotaghossz);
    $telszam = substr($telszam, $elotaghossz, $telszamhossz);

    $teljesszam = array(
        'elotag' => $elotag,
        'telszam' => $telszam
    );

    return $teljesszam;
}

function formatTelnum($telszam)
{
	$temp1 = substr($telszam, 0, 3);
	$temp2 = substr($telszam, 3);

	if($telszam)
	{
		return $temp1 . "-" . $temp2;
	}
	else
	{
		return null;
	}
}

function telefonKonyvImport()
{
	$con = mySQLConnect(false);

	$bemenet = "./tkmod.csv";
	
	$oldtklist = csvToArray($bemenet);
	
	$rendfokozatok = mySQLConnect("SELECT * FROM rendfokozatok ORDER BY id");
	
	$nevelotagok = mySQLConnect("SELECT * FROM nevelotagok");
	
	$titulusok = mySQLConnect("SELECT * FROM titulusok");
	
	$felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok ORDER BY nev");
	
	$alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL;");
	
	$alegysegid = 0;
	$sorrend = 0;

	foreach($oldtklist as $sor)
	{
		if($sor['beosztasnev'] == "Szervezeti egység:")
		{
			foreach($alegysegek as $alegyseg)
			{
				if($alegyseg['nev'] == $sor['elotag'])
				{
					$alegysegid = $alegyseg['id'];
					$sorrend = 0;
					break;
				}
			}
		}
		else
		{
			// Telefonkönyv felhasználók
			$felhid = null;
			if($sor['nev'])
			{
				$elotagid = $titulusid = $rendfokozatid = $mobil = $felhasznaloid = null;
				foreach($nevelotagok as $x)
				{
					if($sor['elotag'] == $x['nev'])
					{
						$elotagid = $x['id'];
						break;
					}
				}

				foreach($titulusok as $x)
				{
					if($sor['titulus'] == $x['nev'])
					{
						$titulusid = $x['id'];
						break;
					}
				}

				foreach($rendfokozatok as $x)
				{
					if($sor['rendfokozat'] == $x['nev'])
					{
						$rendfokozatid = $x['id'];
						break;
					}
				}

				if($sor['mobil'])
				{
					if(str_contains($sor['mobil'], "0106-30-"))
					{
						$mobil = $sor['mobil'];
					}
					else
					{
						$mobil = "0106-30-" . $sor['mobil'];
					}
				}

				foreach($felhasznalok as $x)
				{
					if(str_contains(strtolower($x['nev']), strtolower($sor['nev'])) && str_contains($x['telefon'], $sor['belsoszam']))
					{
						$felhasznaloid = $x['id'];
						break;
					}
				}

				$stmt = $con->prepare('INSERT INTO telefonkonyvfelhasznalok (elotag, nev, titulus, rendfokozat, mobil, felhasznalo) VALUES (?, ?, ?, ?, ?, ?)');
				$stmt->bind_param('ssssss', $elotagid, $sor['nev'], $titulusid, $rendfokozatid, $mobil, $felhasznaloid);
				$stmt->execute();

				$felhid = mysqli_insert_id($con);
			}
			
			// Telefonkönyv beosztások
			$beosztasnev = $belsoszam = $belsoszam2 = $kozcelu = $fax = $kozcelufax = null;
			$csoport = $alegysegid;
			$beosztasnev = $sor['beosztasnev'];
			$sorrend++;

			if(!str_contains($sor['belsoszam'], "02-43-"))
			{
				$belsoszam = "02-43-" . $sor['belsoszam'];
			}
			else
			{
				$szamok = explode(" ", $sor['belsoszam']);
				
				if(isset($szamok[0]))
				{
					$tenylegesszam = str_replace(",", "", $szamok[0]);
					$belsoszam = $tenylegesszam;
				}

				if(isset($szamok[1]))
				{
					$belsoszam2 = $szamok[1];
				}
			}
			
			if($sor['fax'])
			{
				if(str_contains($sor['fax'], "02-43-"))
				{
					$fax = $sor['fax'];
				}
				else
				{
					$fax = "02-43-" . $sor['fax'];
				}
			}

			if($sor['kozcelu'])
			{
				if(str_contains($sor['kozcelu'], "0106-76-"))
				{
					$kozcelu = $sor['kozcelu'];
				}
				else
				{
					$kozcelu = "0106-76-" . $sor['kozcelu'];
				}
			}

			if($sor['kozcelufax'])
			{
				if(str_contains($sor['kozcelufax'], "0106-76-"))
				{
					$kozcelufax = $sor['kozcelufax'];
				}
				else
				{
					$kozcelufax = "0106-76-" . $sor['kozcelufax'];
				}
			}

			$stmt = $con->prepare('INSERT INTO telefonkonyvbeosztasok (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, felhid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
			$stmt->bind_param('sssssssss', $alegysegid, $beosztasnev, $sorrend, $belsoszam, $belsoszam2, $fax, $kozcelu, $kozcelufax, $felhid);
			$stmt->execute();

			echo $sor['beosztasnev'] . "<br>";
		}
	}
}

function telefonKonyvAdminCheck($felhid = null)
{
	$globaltelefonkonyvadmin = true;

	$sql = new MySQLHandler();
	$sql->KeepAlive();

	$sql->Query("SELECT * FROM jogosultsagok WHERE felhasznalo = $felhid AND menupont = 88 AND iras = 3;");
	if($sql->sorokszama == 0)
	{
		$sql->Query("SELECT * FROM telefonkonyvadminok WHERE felhasznalo = $felhid AND csoport = 1 ORDER BY csoport ASC LIMIT 1");
		if($sql->sorokszama == 0)
		{
			$globaltelefonkonyvadmin = false;
		}
	}

	$sql->Close();

	return $globaltelefonkonyvadmin;
}

function getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $settings)
{
	$where = $current = null;
	$filtercount = 0;
	if(@$settings['where'])
	{
		$where = "WHERE ";
	}

	if(@$settings['and'])
	{
		$where = "AND ";
	}

	if(@$settings['modkorszur'])
	{
		$filtercount++;
		$modid = "(SELECT MAX(id) FROM telefonkonyvmodositaskorok)";
		if($settings['modid'])
		{
			$modid = $settings['modid'];
		}
		$where .= "telefonkonyvvaltozasok.modid = $modid";
	}

	if(!$globaltelefonkonyvadmin)
	{
		$mezonev = "telefonkonyvbeosztasok.csoport";
		$osszekotes = " ";
		if(@$settings['mezonev'] && $settings['mezonev'] !== "telefonkonyvbeosztasok_mod")
		{
			$mezonev = "telefonkonyvcsoportok.id";
		}
		elseif(@$settings['mezonev'] && $settings['mezonev'] === "telefonkonyvbeosztasok_mod")
		{
			$mezonev = "telefonkonyvbeosztasok_mod.csoport";
		}

		if(@$settings['felhasznalo'])
		{
			$felhasznalo = $settings['felhasznalo'];
		}
		else
		{
			$felhasznalo = $_SESSION['id'];
		}
		if($filtercount > 0)
		{
			$osszekotes = " AND ";
		}
		if(@$settings['currcsopid'])
		{
			$current = "$mezonev = " . $settings['currcsopid'] . " OR ";
		}
		$where .= "$osszekotes $current $mezonev IN (SELECT csoport FROM telefonkonyvadminok WHERE felhasznalo = $felhasznalo)";
	}

	if($where == "WHERE " || $where == "AND ")
	{
		$where = null;
	}
//var_dump($where);
	return $where;
}

function getBeosztasList($where, $beosztas, $modid)
{
	$meglevobeo = $zarozar = null;
	if($beosztas)
	{
		$meglevobeo = "(telefonkonyvbeosztasok.id = $beosztas
				OR telefonkonyvbeosztasok.id IN (SELECT origbeoid
							FROM telefonkonyvvaltozasok
								INNER JOIN telefonkonyvbeosztasok_mod ON telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok_mod.id
							WHERE telefonkonyvbeosztasok_mod.felhid IS NULL
			   				AND telefonkonyvvaltozasok.allapot > 1 AND telefonkonyvvaltozasok.allapot < 4)
							AND telefonkonyvvaltozasok.modid = (SELECT MAX(id) FROM telefonkonyvmodositaskorok)) OR (";
		$zarozar = ")";
	}
	$beowhere = "telefonkonyvbeosztasok.felhid IS NULL";

	if($modid)
	{
		$beowhere = "(" . $beowhere . " OR telefonkonyvvaltozasok.id = $modid" . ")";
	}

	// 

	// Kizárólag az eredeti, nem felülírt beosztások
	$query = "SELECT telefonkonyvbeosztasok.id AS id,
                telefonkonyvbeosztasok.nev AS nev,
                telefonkonyvcsoportok.nev AS csoportid,
                telefonkonyvcsoportok.nev AS csoportnev
            FROM telefonkonyvbeosztasok
                LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
                LEFT JOIN telefonkonyvvaltozasok ON (telefonkonyvvaltozasok.origbeoid = telefonkonyvbeosztasok.id OR telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok.id)
            WHERE $meglevobeo
                telefonkonyvcsoportok.id > 1
                AND telefonkonyvbeosztasok.allapot > 1
                AND $beowhere
                $where
                $zarozar
            ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;";
	
	//var_dump($query);

	$beosztasok = mySQLConnect($query);

	// Kizárólag a módosított és elfogadott beosztások
	/*$beosztasokmod = mySQLConnect("SELECT telefonkonyvbeosztasok_mod.id AS id,
				telefonkonyvbeosztasok_mod.nev AS nev,
				telefonkonyvcsoportok.nev AS csoportid,
				telefonkonyvcsoportok.nev AS csoportnev
			FROM telefonkonyvbeosztasok
				LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
				LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
				LEFT JOIN telefonkonyvvaltozasok ON (telefonkonyvvaltozasok.origbeoid = telefonkonyvbeosztasok.id OR telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok.id)
			WHERE $meglevobeo
				telefonkonyvcsoportok.id > 1
				AND telefonkonyvbeosztasok.allapot > 1
				AND $beowhere
				$where
				$zarozar
				ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");*/

	return $beosztasok;
}

function getBeosztasListAlt($where)
{
	// Kizárólag az eredeti, nem felülírt beosztások
	$query = "SELECT DISTINCT telefonkonyvbeosztasok.id AS id,
                telefonkonyvbeosztasok.nev AS nev,
                telefonkonyvcsoportok.nev AS csoportid,
                telefonkonyvcsoportok.nev AS csoportnev,
				telefonkonyvbeosztasok.felhid AS foglalt
            FROM telefonkonyvbeosztasok
                LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
                LEFT JOIN telefonkonyvvaltozasok ON (telefonkonyvvaltozasok.origbeoid = telefonkonyvbeosztasok.id OR telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok.id)
            WHERE telefonkonyvcsoportok.id > 1
                AND telefonkonyvbeosztasok.allapot > 1
				AND telefonkonyvbeosztasok.torolve IS NULL
                $where
            ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;";
	
	//var_dump($query);

	$beosztasok = mySQLConnect($query);

	// Kizárólag a módosított és elfogadott beosztások
	/*$beosztasokmod = mySQLConnect("SELECT telefonkonyvbeosztasok_mod.id AS id,
				telefonkonyvbeosztasok_mod.nev AS nev,
				telefonkonyvcsoportok.nev AS csoportid,
				telefonkonyvcsoportok.nev AS csoportnev
			FROM telefonkonyvbeosztasok
				LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
				LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
				LEFT JOIN telefonkonyvvaltozasok ON (telefonkonyvvaltozasok.origbeoid = telefonkonyvbeosztasok.id OR telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok.id)
			WHERE $meglevobeo
				telefonkonyvcsoportok.id > 1
				AND telefonkonyvbeosztasok.allapot > 1
				AND $beowhere
				$where
				$zarozar
				ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");*/

	return $beosztasok;
}
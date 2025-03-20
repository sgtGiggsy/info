<?php

if(isset($irhat) && $irhat)
{
    $modelldb = new mySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $modelldb->Query('INSERT INTO modellek (gyarto, modell, tipus) VALUES (?, ?, ?)',
            $_POST['gyarto'], $_POST['modell'], $_POST['tipus']);

        if($_POST['tipus'] == "12")
        {
            $modelldb->Query('INSERT INTO nyomtatomodellek (modell, szines, scanner, fax, defadmin, defpass, maxmeret) VALUES (?, ?, ?, ?, ?, ?, ?)',
                $modelldb->last_insert_id, $_POST['szines'], $_POST['scanner'], $_POST['fax'], $_POST['defadmin'], $_POST['defpass'], $_POST['maxmeret']);
        }
    }

    elseif($_GET["action"] == "update")
    {
        $modelldb->KeepAlive();
        $modelldb->Query('UPDATE modellek SET gyarto=?, modell=?, tipus=? WHERE id=?',
            $_POST['gyarto'], $_POST['modell'], $_POST['tipus'], $_POST['id']);

        if($tipusnev == "nyomtato")
        {
            $modelldb->Query("SELECT id FROM nyomtatomodellek WHERE modell = ?", $_POST['id']);

            if($modelldb->sorokszama == 0)
            {
                $modelldb->Query("INSERT INTO nyomtatomodellek (modell, szines, scanner, fax, defadmin, defpass, maxmeret) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    $_POST['id'], $_POST['szines'], $_POST['scanner'], $_POST['fax'], $_POST['defadmin'], $_POST['defpass'], $_POST['maxmeret']);
            }
            else
            {
                $modelldb->Query("UPDATE nyomtatomodellek SET szines=?, scanner=?, fax=?, defadmin=?, defpass=?, maxmeret=? WHERE modell=?",
                    $_POST['szines'], $_POST['scanner'], $_POST['fax'], $_POST['defadmin'], $_POST['defpass'], $_POST['maxmeret'], $_POST['id']);
            }
        }

        if($tipusnev == "mediakonverter")
        {
            $modelldb->Query("SELECT id FROM mediakonvertermodellek WHERE modell = ?", $_POST['id']);

            if($modelldb->sorokszama == 0)
            {
                $modelldb->Query("INSERT INTO mediakonvertermodellek (modell, fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg, lanszabvany, lancsatlakozo, lansebesseg) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    $_POST['id'], $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg'], $_POST['lanszabvany'], $_POST['lancsatlakozo'], $_POST['lansebesseg']);
            }
            else
            {
                $modelldb->Query("UPDATE mediakonvertermodellek SET fizikaireteg=?, transzpszabvany=?, transzpcsatlakozo=?, transzpsebesseg=?, lanszabvany=?, lancsatlakozo=?, lansebesseg=? WHERE modell=?",
                    $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg'], $_POST['lanszabvany'], $_POST['lancsatlakozo'], $_POST['lansebesseg'], $_POST['id']);
            }
        }

        if($tipusnev == "bovitomodul")
        {
            $modelldb->Query("SELECT id FROM bovitomodellek WHERE modell = ?", $_POST['id']);

            if($modelldb->sorokszama == 0)
            {
                $modelldb->Query("INSERT INTO bovitomodellek (modell, fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg) VALUES (?, ?, ?, ?, ?)",
                    $_POST['id'], $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg']);
            }
            else
            {
                $modelldb->Query("UPDATE bovitomodellek SET fizikaireteg=?, transzpszabvany=?, transzpcsatlakozo=?, transzpsebesseg=? WHERE modell=?",
                    $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg'], $_POST['id']);
            }
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }

    $modelldb->Close();
}
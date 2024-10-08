<?php
if(isset($irhat) && $irhat)
{
    $feltoltottfajlok = array();
    $uploadids = array();

    if(isset($include) && $include)
    {
        $db = count($fajlok["name"]);
        for($i = 0; $i < $db; $i++)
        {
            if (!in_array($fajlok['type'][$i], $mediatype))
            {
                $uzenet = "A fájl típusa nem megengedett: " . $fajlok['name'][$i];
            }
            else
            {
                if(!file_exists($feltoltesimappa))
                {
                    mkdir($feltoltesimappa, 0777, true);
                }

                $fajlnev = strtolower(str_replace(".", time() . ".", $fajlok['name'][$i]));
                $finalfile = $feltoltesimappa . $fajlnev;
                if(file_exists($finalfile))
                {
                    $uzenet = "A feltölteni kívánt fájl már létezik: " . $fajlnev;
                }
                else
                {
                    move_uploaded_file($fajlok['tmp_name'][$i], $finalfile);
                    $uzenet = 'A fájl feltöltése sikeresen megtörtént: ' . $fajlnev;
                    $feltoltottfajlok[] = "$mappagyokernelkul" . "$fajlnev";
                }
            }
        }

        if(count($feltoltottfajlok) > 0)
        {
            $fajlfeltolt = new mySQLHandler();
            $fajlfeltolt->Prepare('INSERT INTO feltoltesek (fajl) VALUES (?)');
            foreach($feltoltottfajlok as $fajl)
            {
                $fajlfeltolt->Run($fajl);
                $uploadids[] = $fajlfeltolt->last_insert_id;
            }
            $fajlfeltolt->Close();
        }
    }
}
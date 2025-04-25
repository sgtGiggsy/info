<?php

class Ertesites
{
    private $sql;
    private $felhasznalok = array();
    private $ertesitesid = null;
    public $tipus = null;
    public $cim = null;
    public $szoveg = null;
    public $url = null;
    public $mailbody;

    public function __construct($cim = null, $szoveg = null, $url = null, $tipus = null, $mailbody = null)
    {
        $this->sql = new MySQLHandler();
        $this->sql->KeepAlive();
        $this->cim = $cim;
        $this->szoveg = $szoveg;
        $this->url = $url;
        $this->SetTipus($tipus);
        $this->mailbody = $mailbody;
    }

    public function Ment()
    {
        $this->sql->Prepare('INSERT INTO ertesitesek (cim, szoveg, url, tipus) VALUES (?, ?, ?, ?)');
        $this->sql->Run($this->cim, $this->szoveg, $this->url, $this->tipus);
        $this->ertesitesid = $this->sql->last_insert_id;

        if(count($this->felhasznalok) > 0)
        {
            $mailbody = $this->szoveg;
            if($this->mailbody)
                $mailbody = $this->mailbody;
            $mail = new MailHandler($mailbody);
            $mail->Subject($this->cim);

            $this->sql->Prepare("INSERT INTO ertesites_megjelenik(felhasznalo, ertesites) VALUES (?, ?)");
            foreach($this->felhasznalok as $felhasznalo)
            {
                $this->sql->Run($felhasznalo['felhasznalo'], $this->ertesitesid);
                if($felhasznalo['email'])
                {
                    $mail->AddAddress($felhasznalo['email']);
                }
            }
            if($mail->cimzettszam > 0)
                $mail->Send();
        }
        else
        {
            $this->sql->Query("INSERT INTO ertesites_megjelenik(felhasznalo, ertesites) VALUES (?, ?)", 0, $this->ertesitesid);
        }
        $this->sql->Close();
    }

    public function SetFelhasznalok(array $felhasznalok)
    {
        $this->felhasznalok = $felhasznalok;
    }

    public function AddFelhasznalo($felhasznalo)
    {
        if(!isset($felhasznalo['felhasznalo']))
        {
            $felhasznalo = array(
                "felhasznalo" => $felhasznalo,
                "email" => false
            );
        }
        $this->felhasznalok[] = $felhasznalo;
    }

    public function SetTipus($tipus)
    {
        $this->tipus = $tipus;
        if($tipus)
        {
            $this->sql->Query("SELECT felhasznalo, IF(ertesitesfeliratkozasok.email, felhasznalok.email, null) AS email
                    FROM ertesitesfeliratkozasok
                        INNER JOIN felhasznalok ON ertesitesfeliratkozasok.felhasznalo = felhasznalok.id
                    WHERE ertesitestipus = ?;", $tipus);
            $this->felhasznalok = $this->sql->AsArray();
        }
    }

    public static function GetFelhasznalok($tipus, $jointtable = null, $where = null, $params = array())
    {
        $sql = new MySQLHandler("SELECT felhasznalok.id AS felhasznalo, IF(ertesitesfeliratkozasok.email, felhasznalok.email, null) AS email
                FROM ertesitesfeliratkozasok
                    INNER JOIN felhasznalok ON ertesitesfeliratkozasok.felhasznalo = felhasznalok.id
                    $jointtable
                WHERE ertesitestipus = ?
                $where;", $tipus, ...$params);
        return $sql->AsArray('felhasznalo');
    }

    public static function GetErtesitesek($napszam = 7)
    {
        $felhasznaloid = $_SESSION['id'];
        $notifications = array();
        $olvasatlanszam = 0;

        $ertesitessql = new MySQLHandler();
        $ertesitessql->KeepAlive();

        if(OLDALAK['Aktív eszközök']['olvasas'] > 0)
        {
            $ertesitessql->Query("SELECT ertek
                FROM `beallitasok`
                WHERE nev = 'last_switch_check'
                    AND ertek < date_sub(now(), INTERVAL 15 MINUTE)
                    AND ertek > date_sub((SELECT lastseennotif FROM felhasznalok WHERE id = ?), INTERVAL 15 MINUTE)", $felhasznaloid);

            if($ertesitessql->sorokszama > 0)
            {
                $switchutolso = $ertesitessql->Fetch()['ertek'];
                $cim = 'Switch ellenőrző leállt';
                $szoveg = 'A switchek állapotát ellenőrző script utolsó futása: ' . $switchutolso;
                $ertesitessql->Query("SELECT id FROM ertesitesek WHERE timestamp = '$switchutolso' AND cim = 'Switch ellenőrző leállt'");
                if($ertesitessql->sorokszama == 0)
                {
                    $ertesites = new Ertesites($cim, $szoveg, 'aktiveszkozok', 1);
                    $ertesites->Ment();
                }
            }
        }

        $ertesitessql->Query("SELECT ertesitesek.id AS id, cim, szoveg, url, timestamp, latta
            FROM ertesitesek
                INNER JOIN ertesites_megjelenik ON ertesitesek.id = ertesites_megjelenik.ertesites
            WHERE felhasznalo = ?
                AND ertesitesek.timestamp > date_sub(now(), INTERVAL ? DAY)

            ORDER BY latta ASC, timestamp DESC", $felhasznaloid, $napszam);

        //GROUP BY ertesitesek.cim

        foreach($ertesitessql->Result() as $ertesites)
        {
            $latta = true;
            if($ertesites["latta"] == 0)
            {
                $latta = false;
                $olvasatlanszam++;
            }

            $notifications[] = array('id' => $ertesites['id'],
                'cim' => $ertesites["cim"],
                'szoveg' => $ertesites["szoveg"],
                'url' => $ertesites["url"],
                'timestamp' => $ertesites["timestamp"],
                'latta' => $latta
            );
        }

        return array('olvasatlanszam' => $olvasatlanszam, 'ertesitesek' => $notifications);
    }
}
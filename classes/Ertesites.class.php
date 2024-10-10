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
        $this->sql->Prepare('INSERT INTO ertesitesek (cim, szoveg, url, tipus) VALUES (?, ?, ?, ?)');
        $this->cim = $cim;
        $this->szoveg = $szoveg;
        $this->url = $url;
        $this->SetTipus($tipus);
        $this->mailbody = $mailbody;
    }

    public function Ment()
    {
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

    public static function GetFelhasznalok($tipus, $jointtable = null, $where = null, $params)
    {
        $sql = new MySQLHandler("SELECT felhasznalo, IF(ertesitesfeliratkozasok.email, felhasznalok.email, null) AS email
                FROM ertesitesfeliratkozasok
                    INNER JOIN felhasznalok ON ertesitesfeliratkozasok.felhasznalo = felhasznalok.id
                    $jointtable
                WHERE ertesitestipus = ?
                $where;", $tipus, ...$params);
        return $sql->AsArray('felhasznalo');
    }
}
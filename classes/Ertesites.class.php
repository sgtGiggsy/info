<?php

class Ertesites
{
    private $con;
    private $prepstate;
    private $felhasznalok = array();
    private $ertesitesid = null;
    public $tipus = null;
    public $cim = null;
    public $szoveg = null;
    public $url = null;

    public function __construct()
    {
        $this->con = mySQLConnect();
        $this->prepstate = $this->con->prepare('INSERT INTO ertesitesek (cim, szoveg, url, tipus) VALUES (?, ?, ?, ?)');
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '__construct'.$numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }
   
    public function __construct1($param1)
    {
        $this->cim = $param1;
    }
   
    public function __construct2($param1, $param2)
    {
        $this->cim = $param1;
        $this->szoveg = $param2;
    }
   
    public function __construct3($param1, $param2, $param3)
    {
        $this->cim = $param1;
        $this->szoveg = $param2;
        $this->url = $param3;
    }

    public function __construct4($param1, $param2, $param3, $param4)
    {
        $this->cim = $param1;
        $this->szoveg = $param2;
        $this->url = $param3;
        $this->SetTipus($param4);
    }

    public function Ment()
    {
        $this->prepstate->bind_param('ssss', $this->cim, $this->szoveg, $this->url, $this->tipus);
        $this->prepstate->execute();
        $this->ertesitesid = $this->prepstate->insert_id;

        if(count($this->felhasznalok) > 0)
        {
            $mail = new MailHandler($this->szoveg);
            $mail->Subject($this->cim);
            foreach($this->felhasznalok as $felhasznalo)
            {
                $felhid = $felhasznalo['felhasznalo'];
                mySQLConnect("INSERT INTO ertesites_megjelenik(felhasznalo, ertesites) VALUES ($felhid, $this->ertesitesid);");
                if($felhasznalo['email'])
                {
                    $mail->addAddress($felhasznalo['email']);
                }
            }
            $mail->Send();
        }
        else
        {
            mySQLConnect("INSERT INTO ertesites_megjelenik(felhasznalo, ertesites) VALUES (0, $this->ertesitesid);");
        }
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
        $felhasznalok = mySQLConnect("SELECT felhasznalo, IF(ertesitesfeliratkozasok.email, felhasznalok.email, null) AS email
                FROM ertesitesfeliratkozasok
                    INNER JOIN felhasznalok ON ertesitesfeliratkozasok.felhasznalo = felhasznalok.id
                WHERE ertesitestipus = $tipus;");
        $this->felhasznalok = mysqliToArray($felhasznalok);
    }

    public static function GetFelhasznalok($tipus)
    {
        $felhasznalok = mySQLConnect("SELECT felhasznalo, IF(ertesitesfeliratkozasok.email, felhasznalok.email, null) AS email
                FROM ertesitesfeliratkozasok
                    INNER JOIN felhasznalok ON ertesitesfeliratkozasok.felhasznalo = felhasznalok.id
                WHERE ertesitestipus = $tipus;");
        return mysqliToArray($felhasznalok);
    }
}
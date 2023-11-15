<?php

class DevInterface
{
    public $id;
    public $name;
    public $shortname;
    public $description;
    public $type;
    public $mtu;
    public $speed;
    public $mac;
    public $adminstate;
    public $opstate;
    public $statesince;
    public $rx;
    public $rxuni;
    public $rxbroad;
    public $rxdroppedhealthy;
    public $rxdroppeddamaged;
    public $rxunknown;
    public $tx;
    public $txuni;
    public $txbroad;
    public $txdroppedhealthy;
    public $txdroppeddamaged;
    public $txwaitlist;
    public $vlan = "Trunk";
    public $portmode;

    public function InMBytes($direction) {
        if($direction == "rx")
        {
            return round(($this->rx / 1048576), 2) . " MByte";
        }
        else
        {
            return round(($this->tx / 1048576), 2) . " MByte";
        }
    }

    public function PortAdminState()
    {
        switch($this->adminstate)
        {
            case "1" : return "Engedélyezve"; break;
            case "2" : return "Letiltva"; break;
            case "3" : return "Teszt"; break;
            default : return null;
        }
    }

    public function PortOperativeState()
    {
        switch($this->opstate)
        {
            case "1" : return "Up"; break;
            case "2" : return "Down"; break;
            case "3" : return "Testing"; break;
            case "4" : return "Ismeretlen"; break;
            case "5" : return "Nincs csatlakozva"; break;
            case "6" : return "Hiányzik"; break;
            case "7" : return "Automatikusan lekapcsolva"; break;
            default : return null;
        }
    }

    public function PortStateTime()
    {
        return date("d \\n\a\p H:i:s", $this->statesince);
    }

    public function PortSebesseg()
    {
        return round($this->speed / 1000000 , 2) . "Mbit/s";
    }

    public function PortTipus() {
        switch($this->type)
        {
            case "6" : return "Ethernet"; break;
            case "7" : return "Ethernet"; break;
            case "11" : return "Ethernet"; break;
            case "69" : return "Ethernet"; break;
            case "71" : return "WiFi"; break;
            case "15" : return "FDDI"; break;
            case "23" : return "PPPoE"; break;
            case "48" : return "Modem"; break;
            case "53" : return "Virtuális"; break;
            case "55" : return "100BaseSV"; break;
            case "56" : return "Fiber"; break;
            case "94" : return "ADSL"; break;
            case "97" : return "VDSL"; break;
            case "209" : return "Bridge"; break;
            default : return null;
        }
    }
}
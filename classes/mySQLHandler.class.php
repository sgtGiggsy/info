<?php

class MySQLHandler
{
    public $con;
    public $last_insert_id = null;
    public $result;
    public $siker = true;
    public $hibakod;
    private $types = "";
    private $vartparam = 0;
    private $stmt;
    private $showdebug = false;

    public function __construct()
	{
        if(isset($GLOBALS['felhasznaloid']) && $GLOBALS['felhasznaloid'] == 1)
        {
            $this->showdebug = true;
        }
        try
        {
            $this->con = mysqli_connect($GLOBALS['DATABASE_HOST'], $GLOBALS['DATABASE_USER'], $GLOBALS['DATABASE_PASS'], $GLOBALS['DATABASE_NAME']);
        }
        catch(Exception $e)
        {
            echo "<h2>Nem sikerült csatlakozni a MySQL kiszolgálóhoz!</h2>";
            echo $e->getMessage();
        }

        if($this->con)
        {
            mysqli_set_charset($this->con, "UTF8");
            $this->stmt = $this->con->stmt_init();
        }
    }

    public function __destruct()
    {
        if($this->con)
            mysqli_close($this->con);
    }

    private function GetType($param)
    {
        if(is_int($param) || is_bool($param))
        {
            $this->types .= "i";
        }
        elseif(is_double($param) || is_float($param))
        {
            $this->types .= "d";
        }
        elseif(is_string($param) || is_null($param))
        {
            $this->types .= "s";
        }
        else
        {
            $this->types .= "b";
        }
    }

    private function SetTypes($params)
    {
        $this->types = "";
        if(is_array($params))
        {   
            foreach($params as $param)
            {
                $this->GetType($param);
            }
        }
        else
        {
            $this->GetType($params);
        }
    }

    public function InitQuery(string $query)
    {
        if($this->con)
        {
            try
            {
                $prep = $this->stmt->prepare($query);
            }
            catch(Exception $e)
            {
                $prep = false;
            }
            if(!$prep)
            {
                if($this->showdebug)
                {
                    echo "<h2>Hibásan megírt SQL query!</h2>";
                    echo $query;
                }
                $this->stmt = null;
                $this->siker = false;
                return false;
            }
            else
            {
                $this->vartparam = $this->stmt->param_count;
                return true;
            }
        }
        elseif($this->showdebug)
        {
            echo "<h2>A meghívni kísérelt MySQL kapcsolat már lezárult!</h2>";
        }
    }

    public function Query(string $query, $params = null, $keepalive = false)
    {
        //$stmt = $this->stmt;
        if($this->InitQuery($query))
        {
            if($params)
            {
                $this->SetTypes($params);
            }
            return $this->Run($params, $keepalive);
        }
    }

    public function Run($params = null, $keepalive = true)
    {
        if($this->stmt)
        {
            @$GLOBALS['dbcallcount']++;
    
            $paramcount = 0;
            $paramszamokay = false;
            if($params)
            {
                $paramcount = 1;
                if(is_array($params))
                    $paramcount = count($params);
                if(!$this->types)
                    $this->SetTypes($params);
            }
    
            if($paramcount == strlen($this->types) && $this->vartparam == $paramcount)
                $paramszamokay = true;
    
    
            if($params != null && $paramszamokay)
            {
                if(is_array($params))
                {
                    $this->stmt->bind_param($this->types, ...$params);
                }
                else
                {
                    $this->stmt->bind_param($this->types, $params);
                }
            }
    
            if($paramszamokay)
            {
                $this->stmt->execute();
                $this->last_insert_id = mysqli_insert_id($this->con);
                $this->result = $this->stmt->get_result();
            }
            else
            {
                if($this->showdebug)
                {
                    echo "<h2>Hibás paraméterszám!</h2>";
                    echo "Várt paraméter: " . $this->vartparam . "<br>";
                    echo "Típusszám: " . strlen($this->types) . "<br>";
                    echo "Paraméterszám: " . $paramcount . "<br>";
                }
    
                $this->result = false;
            }
    
            if($this->con && mysqli_errno($this->con) != 0)
            {
                $this->hibakod = mysqli_errno($this->con);
                $this->siker = false;
            }
    
            if(!$keepalive && $this->con)
            {
                @mysqli_close($this->con);
                $this->con = null;
            }
            
            return $this->result;
        }
        else
        {
            return false;
        }
    }

    public function NaturalSort($column, $casesensitive = false, $result = null)
    {
        if(!$result)
        {
            $result = $this->result;
        }

        $result = $this->MySQLIToArray();
        usort($result, function($a, $b) use ($column, $casesensitive) {
            if($a[$column] == null)
            {
                $a[$column] = "zzzzz";
            }
    
            if($b[$column] == null)
            {
                $b[$column] = "zzzzz";
            }
    
            if($casesensitive)
            {
                return strnatcmp($a[$column], $b[$column]); //Case sensitive
            }
            else
            {
                return strnatcasecmp($a[$column],$b[$column]); //Case insensitive
            }
        });

        //echo json_encode($result);
        return $result;
    }

    public function MySQLIToArray($ondimensional = false, $result = null)
    {
        if(!$result)
        {
            $result = $this->result;
        }
        $returnarr = array();
        foreach($result as $sor)
        {
            $element = array();
            foreach($sor as $key => $value)
            {
                if($ondimensional)
                {
                    $element = $value;
                    break;
                }
                $element[$key] = $value;
            }
            $returnarr[] = $element;
        }

        //echo json_encode($returnarr);
        return $returnarr;
    }
}
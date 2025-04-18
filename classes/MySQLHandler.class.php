<?php

class MySQLHandler
{
    public $last_insert_id = null;
    public $siker = false;
    public $sorokszama = 0;
    private $hibakod;
    private $con;
    private $exception;
    private $keepalive;
    private $result;
    private $querystring = "";
    private $types = "";
    private $vartparam = 0;
    private $stmt;
    private $showdebug = DEBUG_MODE;
    private $params = array();
    private $queryruntime;

    public function __construct(string $query = null, ...$params)
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

        if($query)
        {
            $this->Query($query, ...$params);
        }
    } 

    public function __destruct()
    {
        if($this->con)
            mysqli_close($this->con);
    }

    public function ShowQueryDetails()
    {
        $params = "";
        foreach($this->params as $p)
        {
            $params .= $p . "; ";
        }
        $params = trim($params, ";");
        echo FormatSQL($this->querystring) . "<br><br>Paraméterek: " . $params . "<br><br>Query futásideje: " . round($this->queryruntime, 2) . " mp";
    }

    public function ShowException($paramszamokay = true, $paramcount = 0)
    {
        if($this->showdebug)
        {
            $message = "";
            if(!$this->con)
            {
                $message = "<h2>A meghívni kísérelt MySQL kapcsolat már lezárult!</h2>";
            }
            elseif(!$this->stmt)
            {
                $message = "<h2>Hibásan megírt SQL query!</h2>" .  $this->exception . "<br>" .  $this->querystring;
            }
            elseif(!$paramszamokay)
            {
                $message = "<h2>Hibás paraméterszám!</h2>" . "Várt paraméter: " . $this->vartparam . "<br>Típusszám: " . strlen($this->types)
                    . "<br>Paraméterszám: " . $paramcount . "<br>Lekérdezés:<br>" . FormatSQL($this->querystring) . "<br>";
            }
            elseif(!$this->querystring)
            {
                $message = "<h2>Nem adtál meg lekérdezést!</h2>";
            }
            elseif(!$this->siker)
            {
                $message = "<h2>A MySQL lekérdezésbe valamilyen hiba csúszott!</h2>" . mysqli_error($this->con);
            }
            else
            {   
                $message = "<h2>Ismeretlen hiba a MySQL lekérdezésben!</h2>";
            }
            try
            {
                throw new Exception($message);
            }
            catch(Exception $e)
            {
                echo $e;
            }
        }
        else
        {
            echo "<h2>A MySQL lekérdezésbe valamilyen hiba csúszott!</h2>";
        }
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

    private function SetTypes(...$params)
    {
        $this->types = "";
        if(is_array($params))
        {   
            foreach($params as $param)
            {
                $this->params[] = $param;
                $this->GetType($param);
            }
        }
        else
        {
            $this->GetType($params);
        }
    }

    public function Result()
    {
        return $this->result;
    }

    public function KeepAlive($keepalive = true)
    {
        $this->keepalive = $keepalive;
    }

    public function Fetch()
    {
        if($this->result)
        {
            return mysqli_fetch_assoc($this->result);
        }
        else
        {
            return false;
        }
    }

    public function Prepare(string $query)
    {
        $this->keepalive = true;
        $this->querystring = $query;
        if(isset($GLOBALS["querylist"]) && $GLOBALS["querylist"])
            $GLOBALS["querylist"][] = $query;
        $this->types = "";
        if($this->con)
        {
            try
            {
                $prep = $this->stmt->prepare($query);
            }
            catch(Exception $e)
            {
                $this->exception = $e->getMessage();
                $prep = false;
            }
            if(!$prep)
            {
                $this->stmt = null;
                $this->siker = false;
            }
            else
            {
                $this->vartparam = $this->stmt->param_count;
                $this->siker = true;
            }
        }
        else
        {
            $this->ShowException();
        }
        return $this->siker;
    }

    public function Query(string $query, ...$params) : mysqli_result | false
    {
        if($this->Prepare($query))
        {
            if($params && @$params[0] !== null)
            {
                $this->SetTypes(...$params);
            }
            return $this->Run(...$params);
        }
        else
        {
            $this->ShowException();
            return false;
        }
    }

    public function Run(...$params) : mysqli_result | false
    {
        $start_time = microtime(true);
        $paramszamokay = false;
        $paramcount = 0;
        if($this->stmt && $this->siker)
        {
            if($params && $params[0] !== null)
            {
                $paramcount = 1;
                if(is_array($params))
                    $paramcount = count($params);
                if(!$this->types)
                    $this->SetTypes(...$params);
            }
            
            if($paramcount == strlen($this->types) && $this->vartparam == $paramcount)
                $paramszamokay = true;
            
            
            if($params && $params[0] !== null && $paramszamokay)
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
                @$GLOBALS['dbcallcount']++;
                $this->stmt->execute();
                $this->last_insert_id = mysqli_insert_id($this->con);
                $this->result = $this->stmt->get_result();
                if(!is_bool($this->result))
                    $this->sorokszama = mysqli_num_rows($this->result);
            }
    
            if($this->con && mysqli_errno($this->con) != 0)
            {
                $this->hibakod = mysqli_errno($this->con);
                $this->siker = false;
            }
    
            if(!$this->keepalive && $this->con)
            {
                @mysqli_close($this->con);
                $this->con = null;
            }
        }
        
        if(!$this->stmt || !$this->siker || !$paramszamokay)
        {
            $this->ShowException($paramszamokay, $paramcount);
            $this->result = false;
        }
        else
        {
            $this->siker = true;
        }

        $this->queryruntime = microtime(true) - $start_time;

        return $this->result;
    }

    public function Bind(&...$array)
    {
        $returnarr = array();
        $tobind = mysqli_fetch_assoc($this->result);
        if($tobind)
        {
            $dbelem = count($tobind);
            
            if($dbelem == count($array))
            {
                $i = 0;
                foreach($tobind as $key => $value)
                {
                    $array[$i] = $value;
                    $returnarr[$key] = $value;
                    $i++;
                }
                
                return $returnarr;
            }
            else
            {
                echo "<h2>Az SQL lekérdezés mezőinek, és a kötni kívánt tömb elemeinek száma különbözik!</h2>";
                echo "SQL adatbázisból vett mezők száma: . $dbelem";
                echo "A kötni kívánt elemek száma: " . count($array);
                return false;
            }
        }
        else
        {
            echo "<h2>A lekérdezés sikertelen!</h2>";
            return false;
        }
    }

    public function NaturalSort($column, $casesensitive = false, $result = null)
    {
        if(!$result)
        {
            $result = $this->result;
        }

        $result = $this->AsArray();
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

    public function AsArray($arrkey = null, $ondimensional = false)
    {
        if($arrkey || $ondimensional)
        {
            $returnarr = array();
            foreach($this->result as $sor)
            {
                if($ondimensional)
                {
                    foreach($sor as $value)
                    {
                        $returnarr[] = $value;
                        break;
                    }
                }
                
                if($arrkey)
                    $returnarr[$sor[$arrkey]] = $sor;
                else
                    $returnarr[] = $sor;
            }
        }
        else
        {
            $returnarr = mysqli_fetch_all($this->result, MYSQLI_ASSOC);
        }
        return $returnarr;
    }

    public function Close($backtosender = null)
    {
        if($this->con)
            //mysqli_close($this->con);

        if($backtosender)
        {
            header("Location: $backtosender");
            die;
        }
    }
}
<?php
if(is_readable('./external/PHPMailer/Exception.php'))
{
    require('./external/PHPMailer/Exception.php');
    require('./external/PHPMailer/PHPMailer.php');
    require('./external/PHPMailer/SMTP.php');
}
else
{
    require('../external/PHPMailer/Exception.php');
    require('../external/PHPMailer/PHPMailer.php');
    require('../external/PHPMailer/SMTP.php');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailHandler
{
    private $mail;
    private $messageset = false;
    public $cimzettszam = 0;

    public function __construct()
	{
        $this->mail = new PHPMailer(true);
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        if(isset($_SESSION[getenv('SESSION_NAME') . 'mailserver']))
        {
            $this->mail->Host       = $_SESSION[getenv('SESSION_NAME') . 'mailserver'];
            $this->mail->Port       = $_SESSION[getenv('SESSION_NAME') . 'mailport'];
            $this->mail->Username   = $_SESSION[getenv('SESSION_NAME') . 'mailuser'];
            $this->mail->Password   = $_SESSION[getenv('SESSION_NAME') . 'mailpassword'];
            $this->mail->setFrom($_SESSION[getenv('SESSION_NAME') . 'mailfrom'], 'Mailer');
        }
        else
        {
            $beallitasokdb = new MySQLHandler("SELECT * FROM beallitasok;");

            $beallitasok = array();
            foreach($beallitasokdb->Result() as $beallitas)
            {
                $beallitasok[$beallitas['nev']] = $beallitas['ertek'];
            }
            $this->mail->Host       = $beallitasok['mailserver'];
            $this->mail->Port       = $beallitasok['mailport'];
            $this->mail->Username   = $beallitasok['mailuser'];
            $this->mail->Password   = $beallitasok['mailpassword'];
            $this->mail->setFrom($beallitasok['mailfrom'], 'Mailer');
        }
        $this->mail->isSMTP();
        $this->mail->SMTPAuth   = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->CharSet = "UTF-8";
        $this->mail->isHTML(true);
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );


        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '__construct'.$numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function __construct1($uzenet)
    {
        $this->messageset = true;
        $this->mail->Body = $uzenet;
        $this->mail->Subject = "Értesítés a webes nyilvántartótól";
    }

    public function __construct2($uzenet, $cimzett)
    {
        $this->messageset = true;
        $this->mail->Body = $uzenet;
        $this->AddAddress($cimzett);
        $this->mail->Subject = "Értesítés a webes nyilvántartótól";
    }

    public function __construct3($uzenet, $cimzett, $targy)
    {
        $this->messageset = true;
        $this->mail->Body = $uzenet;
        $this->AddAddress($cimzett);
        $this->mail->Subject = $targy;
    }

    public function AddAddress($cimzett)
    {
        if(is_array($cimzett))
        {
            foreach($cimzett as $c)
            {
                $this->cimzettszam++;
                $this->mail->addAddress($c);
            };
        }
        else
        {
            $this->cimzettszam++;
            $this->mail->addAddress($cimzett);
        }        
    }

    public function Subject($targy)
    {
        $this->mail->Subject = $targy;
    }

    public function Send()
    {
        if($this->messageset && $this->cimzettszam > 0)
        {
            try
            {
                $this->mail->send();
                echo 'Mail sikeresen elküldve';
            } catch (Exception $e) {
                echo "A mail küldése nem sikerült. A hiba oka: {$this->mail->ErrorInfo}";
            }
        }
        else
        {
            if(!$this->messageset)
            {
                echo "<h2>Nem adtad meg a mail szövegét!</h2>";
            }
            if($this->cimzettszam < 1)
            {
                echo "<h2>Nem adtál meg címzetteket!</h2>";
            }
        }
    }
}
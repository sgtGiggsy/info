<?php

namespace Infoinventory\Classes;

require(ROOT_DIR . '/includes/external/PHPMailer/Exception.php');
require(ROOT_DIR . '/includes/external/PHPMailer/PHPMailer.php');
require(ROOT_DIR . '/includes/external/PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailHandler
{
    private PHPMailer $mail;
    private bool $messageset = false;
    public int $cimzettszam = 0;

    public function __construct()
	{
        $this->mail = new PHPMailer(true);
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->Host       = $GLOBALS['MAIL_HOST'];
        $this->mail->Port       = $GLOBALS['MAIL_PORT'];
        $this->mail->Username   = $GLOBALS['MAIL_USERNAME'];
        $this->mail->Password   = $GLOBALS['MAIL_PASSWORD'];
        $this->mail->setFrom($GLOBALS['MAIL_FROM'], $GLOBALS['MAIL_FROMNEV']);
        $this->mail->isSMTP();
        $this->mail->SMTPAuth   = $GLOBALS['MAIL_SMTPAUTH'];
        if($GLOBALS['MAIL_SMTPSECURE'])
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        else
            $this->mail->SMTPSecure = false;
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

    public function AddAddress($cimzett) : void
    {
        if(is_array($cimzett))
        {
            foreach($cimzett as $c)
            {
                $this->cimzettszam++;
                $this->mail->addAddress($c);
            }
        }
        else
        {
            $this->cimzettszam++;
            $this->mail->addAddress($cimzett);
        }        
    }

    public function Subject($targy) : void
    {
        $this->mail->Subject = $targy;
    }

    public function Send() : void
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
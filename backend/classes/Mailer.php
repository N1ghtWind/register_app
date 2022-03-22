<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
$path = getcwd();

$path = preg_replace('/register_app.*/', '', $path);
$path .= "register_app";
require $path . '\vendor\autoload.php';

class Mailer
{

    private $message;
    private $subject;
    private $content;

    public function sendMail($email, $subject, $message, $content)
    {

        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->isHTML(true);
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->addAddress($email);
            $mail->Username = "{usernam}";
            $mail->Password = "{password}";
            $mail->setFrom('{setfrom}', '{setfrom}');
            $mail->addReplyTo("no-reply@gmail.com", "No Reply");
            $mail->Subject = $subject;
            $mail->Body = $content;
            $mail->send();
        } catch (Exception $exception) {
            echo "Message could not be sent. Error: {$mail->ErrorInfo}";
        }
    }
    public function sendActivateMail($email, $link)
    {
        $this->subject = "You need to activate your account!";
        $this->content = file_get_contents("mail_template.php");
        $this->content = str_replace("{url}", $link, $this->content);
        $this->content = str_replace("{content}", "Activate your account!", $this->content);
        $this->content = str_replace("{title}", "Activate your account!", $this->content);
        $this->content = str_replace("{button}", "Activate", $this->content);
        $this->sendMail($email, $this->subject, $this->message, $this->content);
    }
}

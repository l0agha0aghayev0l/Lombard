<?php
header('Content-Type: text/html; charset=utf-8', true);
session_start();
include 'functions.php';
require('class.phpmailer.php');

//........................................................................................................
class MailAgent
{

    public $_SMTPServer = '';
    public $_SMTPLogin = '';
    public $_SMTPPass = '';
    public $_SMTPPort = '';
    public $_SMTPAuth = true;
    public $_SMTPSecure = '';
    public $_mailFrom = '';
    public $_mailCharSet = "";
    private $_mail = null;

    public $parts = array();

    public function __construct()
    {
        if ($this->_mail == null) {
            //  		$this->initMailAgent();
        }
    }

    public function __destruct()
    {
        //      $this->MailAgent_remAttachment();      ///////Отключение удаления файла атачмента.
    }


    public function initMailAgent()
    {
        //	require_once('class.phpmailer.php');

        $this->_mail  = new PHPMailer();
        // Устанавливаем, что наши сообщения будет идти через
        // SMTP сервер
        $this->_mail->IsSMTP();

        // Можно раскомментировать след. строчку для отладки
        // 1 = Ошибки и сообщения
        // 2 = Только сообщения
        //$mail->SMTPDebug  = 2;

        // Включение SMTP аутентификации
        // Большинство серверов ее требуют
        $this->_mail->SMTPAuth   = $this->_SMTPAuth;
        // SMTP Сервер отправки сообщений
        $this->_mail->Host       = $this->_SMTPServer;
        // Порт сервера (чаще всего 25)
        $this->_mail->Port       = $this->_SMTPPort;

        $this->_mail->SMTPSecure = $this->_SMTPSecure;
        // SMTP Логин для авториации
        $this->_mail->Username   = $this->_SMTPLogin;
        // SMTP Пароль для авторизации
        $this->_mail->Password   = $this->_SMTPPass;
        // Кодировка сообщения
        $this->_mail->CharSet    = $this->_mailCharSet;
    }


    public function MailAgent_addFile($filename)
    {
        $cnt = count($this->parts) + 1;
        $this->parts[$cnt - 1] = $filename;
        $this->_mail->AddAttachment($filename);
    }

    public function MailAgent_remAttachment()
    {
        for ($i = 0; $i < count($this->parts); $i++) unlink($this->parts[$i]);
    }


    public function sendMail($address, $subject, $body, $from = '')
    {
        //	if ($this->_mail == null) {
        //		$this->initMailAgent();
        //	}
        $retval = "";

        // Устанавливаем от кого будет уходить почта
        $this->_mail->SetFrom($from == '' ? $this->_mailFrom : $from);
        // Устанавливаем заголовк письма
        $this->_mail->Subject    = $subject;
        // Текст сообщения
        $this->_mail->MsgHTML($body);

        if (is_array($address)) {
            // Отправка сообщений сразу нескольким пользователям
            foreach ($address as $value) {
                $this->_mail->AddAddress($value);
            }
        } else {
            // Адрес получателя. Второй параметр - имя получателя (не обязательно)
            $this->_mail->AddAddress($address);
        }

        // Отправляем сообщение
        $sndcode = $this->_mail->Send();
        if (!$sndcode) {
            //	  echo "Ошибка отправки: " . $this->_mail->ErrorInfo;
            $retval = "Ошибка отправки: " . $this->_mail->ErrorInfo;
        } else {
            //	  echo "Сообщение отправлено на {$address}<br/>";
            $retval = "OK";
        }

        return $retval;
    }
}


$ufile = str_replace("\\", "/", getcwd());
//echo $_POST['name_surname']."\r\n".$_POST['email']."\r\n".$_POST['document'];exit;
//........................................................................................................
if ($_FILES['document']['name'] !== "") {
    $uplfile = $ufile . "/uploads/" . basename($_FILES['document']['name']);
    if (move_uploaded_file($_FILES['document']['tmp_name'], $uplfile)) {
        if ((isset($_POST['name_surname']) && $_POST['name_surname'] != "") && (isset($_POST['email']) && $_POST['email'] != "")) {

            $mail = new MailAgent();

            $mail->_SMTPServer = 'smtp.elasticemail.com';
            $mail->_SMTPLogin = 'vpkvsite@gmail.com';
            $mail->_SMTPPass = '0E9A6DAAB6928DACE05BC65843B79F4788F8';
            $mail->_SMTPPort = 2525;
            $mail->_SMTPAuth = 1;
            //        $mail->_SMTPSecure = 'tls';
            //        $mail->_SMTPSecure = 'ssl';
            $mail->_mailFrom = 'vpkvsite@gmail.com';
            $mail->_mailCharSet = 'utf-8';

            $mail->initMailAgent();

            $mail->MailAgent_addFile($uplfile);

            date_default_timezone_set('Asia/Baku');
            $time2 = time();    // + (4*60*60);  //summer time (11*60*60); winter time (10*60*60)


            $out_email = "cavadka@gmail.com";

            $subject = "iş mesajı";
            $message =
                "<div>
                        <table style='border: 3px solid  #187232;border-collapse: collapse;width: 100%'>
                        <tr>
                        <th style='display: flex;flex-direction: left; margin-top: 10px; color: #187232; background-color: #F57731; border-collapse: collapse; padding: 10px 20px;'>Ad, Soyad</th>
                        <td style='display: flex;flex-direction: column;flex-direction: left;margin-top: 10px;border-collapse: collapse;padding: 10px 20px;'>" . $_POST['name_surname'] . "</td>
                        <th style='display: flex;flex-direction: left; margin-top: 10px; color: #187232; background-color: #F57731; border-collapse: collapse; padding: 10px 20px;'>Email</th>
                        <td style='display: flex;flex-direction: column;flex-direction: left;margin-top: 10px;border-collapse: collapse;padding: 10px 20px;'>" . $_POST['email'] . "</td>
                        <th style='display: flex;flex-direction: left; margin-top: 10px; color: #187232; background-color: #F57731; border-collapse: collapse; padding: 10px 20px;'>CV</th>
                      </tr>
                      <tr>
                    </tr>
                        </table>
                    </div>";
            //        $headers = "Content-type: text/html; charset=utf-8 \r\n";
            //        $headers .= "From: <from@exapmle.com>\r\n";
            //        mail($to, $subject, $message, $headers);

            try {
                $retval = $mail->sendMail($out_email, $subject, $message);
            } catch (Exception $e) {
                //                    echo 'Ошибка при отправке письма: ', $mail->ErrorInfo;
                header("Location: ../index.html?err3");
            }

            unset($mail);
            header("Location: ../index.html?sent");
        }
    } else {
        header("Location: ../index.html?err2");
    }
} else {
    header("Location: ../index.html?err1");
}

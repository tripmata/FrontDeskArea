<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class Mail
    {
        public $Body,
            $Subject,
            $From,
            $To,
            $isHTML,
            $FromName,
            $ToName,
            $ReplyTo,
            $CC,
            $BCC,
            $AltBody,
            $ReplyToName;

        private $Attachments = array();

        public function AddAttachment($file)
        {
            array_push($file);
        }

        public static function Send(Mail $email)
        {
            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try
            {
                //Server settings
                $mail->SMTPDebug = 2;                                       // Enable verbose debug output
                /*
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'user@example.com';                     // SMTP username
                $mail->Password   = 'secret';                               // SMTP password
                $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = 587;                                    // TCP port to connect to
                */

                //Recipients
                if($email->FromName == null)
                {
                    $mail->setFrom($email->From);
                }
                else
                {
                    $mail->setFrom($email->From, $email->FromName);
                }

                if($email->ToName == "")
                {
                    $mail->addAddress($email->To);     // Add a recipient
                }
                else
                {
                    $mail->addAddress($email->To, $email->ToName);     // Add a recipient
                }

                if($email->ReplyTo != null)
                {
                    if($email->ReplyToName != null)
                    {
                        $mail->addReplyTo($email->ReplyTo);
                    }
                    else
                    {
                        $mail->addReplyTo($email->ReplyTo, $email->ReplyToName);
                    }
                }

                if($email->isHTML === true)
                {
                    $mail->isHTML(true);                                  // Set email format to HTML
                }


                $mail->Subject = $email->Subject;
                $mail->Body    = $email->Body;


                if($email->CC != null)
                {
                    $mail->addCC($email->CC);
                }
                if($email->BCC != null)
                {
                    $mail->addBCC($email->BCC);
                }

                for($i = 0; $i < count($email->Attachments); $i++)
                {
                    $mail->addAttachment($email->Attachments[$i]);         // Add attachments
                }

                $mail->Subject = $email->Subject;
                $mail->Body    = $email->Body;
                $mail->AltBody = $email->AltBody;

                $mail->send();

                return true;
            }
            catch (Exception $e)
            {
                return $mail->ErrorInfo;
            }
        }


        public static function MapRequest()
        {
            $mail = null;

            if((isset($_REQUEST['from'])) && (isset($_REQUEST['to'])) && (isset($_REQUEST['subject'])) &&
                (isset($_REQUEST['body'])) && (isset($_REQUEST['altbody'])) && (isset($_REQUEST['fromname'])) &&
                (isset($_REQUEST['toname'])) && (isset($_REQUEST['replyto'])) && (isset($_REQUEST['replytoname'])) &&
                (isset($_REQUEST['ishtml'])) && (isset($_REQUEST['attachment'])))
            {
                $mail = new Mail();
                $mail->AltBody = $_REQUEST['altbody'];
                $mail->Body = $_REQUEST['body'];
                $mail->From = $_REQUEST['from'];
                $mail->FromName = $_REQUEST['fromname'];
                $mail->isHTML = $_REQUEST['ishtml'] == "true" ? true : false;
                $mail->ReplyTo = $_REQUEST['replyto'];
                $mail->ReplyToName = $_REQUEST['replytoname'];
                $mail->Subject = $_REQUEST['subject'];
                $mail->To = $_REQUEST['to'];
                $mail->ToName = $_REQUEST['toname'];

                if($_REQUEST['attachment'] != "")
                {
                    $mail->AddAttachment($_SERVER['domain']."/".$_REQUEST['attachment']);
                }
            }

            return $mail;
        }
    }
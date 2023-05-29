<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require __DIR__.'/../vendor/autoload.php';
/**
 * clase para enviar emails
 */
class Email
{

  public function enviarEmail($from,$to,$body,$sujeto,$altsujeto,$file=false){

					//Create a new PHPMailer instance
					$mail = new PHPMailer;
					//Tell PHPMailer to use SMTP
					if($_ENV['MAIL_MAILER']='smtp'){
						$mail->isSMTP();
					}
					//Enable SMTP debugging
					// SMTP::DEBUG_OFF = off (for production use)
					// SMTP::DEBUG_CLIENT = client messages
					// SMTP::DEBUG_SERVER = client and server messages
					$mail->SMTPDebug = SMTP::DEBUG_OFF;
					//Set the hostname of the mail server
					$mail->Host = $_ENV['MAIL_HOST'];
					// use
					// $mail->Host = gethostbyname('smtp.gmail.com');
					// if your network does not support SMTP over IPv6
					//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
					$mail->Port = $_ENV['MAIL_PORT'];
					//Set the encryption mechanism to use - STARTTLS or SMTPS
					if($_ENV['MAIL_ENCRYPTION']=='tls'){
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					}
					//Whether to use SMTP authentication
					$mail->SMTPAuth = true;
					//Username to use for SMTP authentication - use full email address for gmail
					$mail->Username =$_ENV['MAIL_USERNAME'];
					//Password to use for SMTP authentication
					$mail->Password = $_ENV['MAIL_PASSWORD'];
					//Set who the message is to be sent from
					$mail->setFrom($from,$_ENV['APP_NAME']);
					//Set an alternative reply-to address
					$mail->addReplyTo($from,$_ENV['APP_NAME']);
					//Set who the message is to be sent to
					$mail->addAddress($to, '');
					//Set the subject line
					$mail->Subject = $sujeto;
					//Read an HTML message body from an external file, convert referenced images to embedded,
					//convert HTML into a basic plain-text alternative body
					$mail->msgHTML($body,__DIR__);
					//$mail->Body = $body;
					$mail->AltBody = $altsujeto;
					//Attach an image file
          if($file!=false){
            $mail->addAttachment($file,basename($file));//url path
  					//send the message, check for errors
          }

					if (!$mail->send()) {
					return 'error de envio de mensaje '. $mail->ErrorInfo;
					} else {
					return true;
					}

	}

}


 ?>

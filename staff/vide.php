<?php 

require "../general_fonctions.php";
use PHPMailer\PHPMailer\PHPMailer; //pour le mail
use PHPMailer\PHPMailer\SMTP;
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';


try { 
        $mail = new PHPmailer();
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mail_from, $mail_fromName);
        $mail->ContentType = 'text/plain';
        $mail->Subject='Désistement de staff';
        $msg="Attention :\n\nLe créneau suivant n'a plus de staff en gestion : \n " ;
	$msg.=$_POST['titre'];
        $mail->Body=$msg;
        $mail->AddAddress($mail_from);
        $mail->AddAddress('ca@sandsystem.com');
        if (!$mail->send()) {
            echo json_encode("pas bon"); //'Mailer error: ' . $mail->ErrorInfo);
        } else {
	    echo json_encode("fin");
	}
        $mail->SmtpClose();
        unset($mail); 
    } catch (Exception $e) {
        echo json_encode("Erreur dans l'envoi de mail pour prévenir l'admin");
    }
    
?>
<?php
require_once './class.phpmailer.php';
function MailPHPMail()
{
    $mailer = new PHPMailer();
    $mailer->AddAttachment($_POST['attachment'], $_POST['attachmentName']);
    $encoding = 'base64';
    $mailer->AddStringAttachment($_POST['attachment'], $_POST['attachmentName'], $encoding, 'image/png');
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $messageTxt = $_POST['message'];
    $mailer->AddAddress($to);
    $mailer->SetFrom($from);
    $mailer->Subject = $subject;
    $mailer->MsgHTML($messageTxt);
    $mailer->Send();
}

function NormalMail()
{

 //array of file names
 $filenames = $_POST['attachment'];
 //email set up
 $to = $_POST['to'];
 $subject = $_POST['subject'];
 $messageTxt = $_POST['message'];
 $headers = "From: " . $_POST['from'];
 $attachmentName = $_POST['attachmentName'];
 //setting the boundary
 $rand_seed = md5(time());
 $mime_boundary = "==Multipart_Boundary_x{$rand_seed}x";
 //attachment header
 $headers .= " \r\nMIME-Version: 1.0\r\n"
   ."Content-Type: multipart/mixed;\r\n"
   ." boundary=\"{$mime_boundary}\"\r\n";
 $message .= "This is a multi-part message in MIME format.\n\n"
   ."--{$mime_boundary}\n\n"   
   . $messageTxt . "\n\n";
$message .= "--{$mime_boundary}\n";

$data = $filenames;
   $message .= "Content-Type: {\"application/octet-stream\"};\n"
    ." name=\"$attachmentName\"\n"
    ."Content-Disposition: attachment;\n" . " filename=\"$attachmentName\"\n"
    ."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
   $message .= "--{$mime_boundary}\n";
 
 $mail_sent = @mail( $to, $subject, $message, $headers );
 echo $mail_sent ? "Mail sent" : "Mail failed";
}


MailPHPMail();
?>
 
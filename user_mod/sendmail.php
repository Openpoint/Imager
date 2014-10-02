<?php
/*
 * UserMod - An ajax modal based user management system
 * Copyright (C) 2014  Michael Jonker
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * michael@piquant.ie
 * Piquant Media
 * http://www.piquant.ie
 * */ 
  
 /*
  * Connect to PHPmailer
  * 
  * */
?>
<?php
require 'Mail/PHPMailerAutoload.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/user_mod/settings.php';
$mail = new PHPMailer;

$newmail=new stdClass();
$newmail->addReplyTo=$_POST["replyto"];
$newmail->sender=$_POST["sender"];
$newmail->recipient=$_POST["recipient"];
$newmail->subject=$_POST["subject"];
$newmail->message=$_POST["message"];
$newmail->altmessage=$_POST["altmessage"];


if($settings->isSMTP){
	$mail->isSMTP();
	$mail->Host = $settings->Host;
	$mail->SMTPAuth = $settings->SMTPAuth;
	$mail->Username = $settings->Username;
	$mail->Password = $settings->Password;
	$mail->SMTPSecure = $settings->SMTPSecure;
}

$mail->From = $settings->From;
$mail->FromName = $settings->FromName;


$mail->addReplyTo($newmail->addReplyTo, $newmail->sender);


$mail->addAddress($newmail->recipient);  // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional

//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $newmail->subject;
$mail->Body    = $newmail->message;
$mail->AltBody = $newmail->altmessage;

if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo 'success';

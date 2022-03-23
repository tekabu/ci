<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailci
{
	public $last_error;
	private $ci;

	function __construct()
	{
		$this->ci =& get_instance();
	}
	public function sendSimple($recipients, $subject, $body, $username, $password, $from_name=null)
	{
		if (!is_array($recipients)) $recipients = array($recipients);

		$mail = new PHPMailer(true);

		try 
		{
		    #$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		    $mail->SMTPDebug = SMTP::DEBUG_OFF;
		    $mail->isSMTP();
		    $mail->Host       = 'smtp.gmail.com'; 
		    $mail->SMTPAuth   = true;
		    $mail->Username   = $username;
		    $mail->Password   = $password;
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		    $mail->Port       = 587;

		    if ($from_name) $mail->setFrom($username, $from_name);

		    foreach ($recipients as $recipient) {
		    	$mail->addAddress($recipient);	
			}

		    $mail->isHTML(true);
		    $mail->Subject = $subject;
		    $mail->Body    = $body;

		    $mail->send();
		    
		    return true;
		} 
		catch (Exception $e) 
		{
			$this->last_error = $mail->ErrorInfo; dj('r');
		}

		return false;
	}
}
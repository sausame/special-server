<?php

function sendSmtp($configFile, $email_to, $email_subject, $email_body, $email_from=NULL, $email_address=NULL) {

	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

	set_include_path("." . PATH_SEPARATOR . ($UserDir = dirname($_SERVER['DOCUMENT_ROOT'])) . "/pear/php" . PATH_SEPARATOR . get_include_path());

	require_once "Mail.php";

	$config = parse_ini_file($configFile);

	$host = $config['email-smtp-host'];
	$username = $config['email-smtp-username'];
	$password = $config['email-smtp-password'];
	$port = $config['email-smtp-port'];

	if (! $email_from) {
		$email_from = $username;
	}

	if (! $email_address) {
		$email_address = $email_from;
	}

	$headers = array ('From' => $email_from, 'To' => $email_to, 'Subject' => $email_subject, 'Reply-To' => $email_address);
	$smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
	$mail = $smtp->send($email_to, $headers, $email_body);

	if (PEAR::isError($mail)) {
		return $mail->getMessage();
	}

	return NULL;
}
?>

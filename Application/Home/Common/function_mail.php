<?php
/**
 * 发送邮件函数
 * @param  [type] $toemail [description]
 * @param  [type] $subject [description]
 * @param  [type] $message [description]
 * @param  string $from    [description]
 * @return [type]          [description]
 */
function sendmail($toemail, $subject, $message, $from = '') {
	define('CHARSET','utf-8');
	$smtp = C('THINK_EMAIL');;
	$_G['server'] = $_G['port'] = $_G['auth'] = $_G['from'] = $_G['auth_username'] = $_G['auth_password'] = '';
	$_G['server'] = $smtp['SMTP_HOST'];
	$_G['port'] = $smtp['SMTP_PORT'];
	$_G['auth'] = 1;
	$_G['from'] = $smtp['FROM_EMAIL'];
	$_G['auth_username'] = $smtp['SMTP_USER'];
	$_G['auth_password'] = $smtp['SMTP_PASS'];
		
	$message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\2"', $message);

$message = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=CHARSET">
<title>$subject</title>
</head>
<body>
$message
</body>
</html>
EOT;

	$maildelimiter = "\n";
	$mailusername = 1;
	$_G['port'] = $_G['port'] ? $_G['port'] : 25;
	
	$email_from = $from == '' ? '=?'.CHARSET.'?B?'.base64_encode(C('SITENAME'))."?= <".$_G['from'].">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);

	$email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;

	$email_subject = '=?'.CHARSET.'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', $subject)).'?=';
	$email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
	$host = $_SERVER['HTTP_HOST'];
	$headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: $host {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=".CHARSET."{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";
	

	if(!$fp = fsocketopen($_G['server'], $_G['port'], $errno, $errstr, 30)) {
		//return "({$_G[server]}:{$_G[port]}) CONNECT - Unable to connect to the SMTP server";
		return false;
	}
	stream_set_blocking($fp, true);

	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != '220') {
		//return "{$_G[server]}:{$_G[port]} CONNECT - $lastmessage";
		return false;
	}

	fputs($fp, 'EHLO' ." uchome\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
		//return "({$_G[server]}:{$_G[port]}) HELO/EHLO - $lastmessage";
		return false;
	}

	while(1) {
		if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
			break;
		}
		$lastmessage = fgets($fp, 512);
	}

	
	fputs($fp, "AUTH LOGIN\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 334) {
		//return "({$_G[server]}:{$_G[port]}) AUTH LOGIN - $lastmessage";
		return false;
	}

	fputs($fp, base64_encode($_G['auth_username'])."\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 334) {
		//return "({$_G[server]}:{$_G[port]}) USERNAME - $lastmessage";
		return false;
	}

	fputs($fp, base64_encode($_G['auth_password'])."\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 235) {
		//return "({$_G[server]}:{$_G[port]}) PASSWORD - $lastmessage";
		return false;
	}

	$email_from = $_G['from'];

	fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			//return "({$_G[server]}:{$_G[port]}) MAIL FROM - $lastmessage";
			return false;
		}
	}

	fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
		$lastmessage = fgets($fp, 512);
		//return "({$_G[server]}:{$_G[port]}) RCPT TO - $lastmessage";
		return false;
	}

	fputs($fp, "DATA\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 354) {
		//return "({$_G[server]}:{$_G[port]}) DATA - $lastmessage";
		return false;
	}

	$timeoffset = 8;
	if(function_exists('date_default_timezone_set')) {
		@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	}

	$headers .= 'Message-ID: <'.date('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";
	fputs($fp, "Date: ".date('r')."\r\n");
	fputs($fp, "To: ".$email_to."\r\n");
	fputs($fp, "Subject: ".$email_subject."\r\n");
	fputs($fp, $headers."\r\n");
	fputs($fp, "\r\n\r\n");
	fputs($fp, "$email_message\r\n.\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		return "({$_G[server]}:{$_G[port]}) END - $lastmessage";
	}
	fputs($fp, "QUIT\r\n");

	return true;
}

function fsocketopen($hostname, $port = 80, &$errno, &$errstr, $timeout = 15) {
	$fp = '';
	if(function_exists('fsockopen')) {
		$fp = @fsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('pfsockopen')) {
		$fp = @pfsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('stream_socket_client')) {
		$fp = @stream_socket_client($hostname.':'.$port, $errno, $errstr, $timeout);
	}
	return $fp;
}

?>
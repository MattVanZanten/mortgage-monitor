<?php
#preparation
require_once('./config.php');
$now = date('d/m/Y');
$variable_file = './vrate';
$fixed_file = './frate';

#get the file contents
$html = get_data( $url );
$doc = new DOMDocument();
@$doc->loadHTML( $html );
$xpath = new DOMXpath( $doc );

#get the variable rate
$variable_rate = $xpath->query( "//div[@class='box-rate-1']/div/p/a/span" )->item(0)->nodeValue;
$fixed_rate = $xpath->query( "//div[@class='box-rate-2']/div/p/a/span" )->item(0)->nodeValue;

#if an error occurred alert me
if ( strlen($variable_rate) < 3 || strlen($fixed_rate) < 3 ) {
	error( $to );
}

#get the variable rate from previous execution
if ( !$last_variable_rate = file_get_contents( $variable_file ) ) {
	echo "First run, creating vrate file";
}
#get the fixed rate from previous execution
if ( !$last_fixed_rate = file_get_contents( $fixed_file ) ) {
	echo "First run, creating frate file";
}

#email if the variable rate has changed from previous execution
if ( $last_variable_rate != $variable_rate ) {
	email('Variable', $to, $last_variable_rate, $variable_rate);
	file_put_contents($variable_file, $variable_rate, LOCK_EX);
}
#email if the fixed rate has changed from previous execution
if ( $last_fixed_rate != $fixed_rate ) {
	email('Fixed', $to, $last_fixed_rate, $fixed_rate);
	file_put_contents($fixed_file, $fixed_rate, LOCK_EX);
}

#email alert function
function email( $type, $to, $previous, $current ) {
	$headers = "From: {$to}" . "\r\n" . 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
    	'Reply-To: noreply@example.com' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();

	$message = "<h3><u>{$type} Rate Changed</u></h3>";
	$message .= "<p><b>{$previous} >>> {$current}</b></p>";

	if ( mail($to, "{$type} Rate Changed", $message, $headers) ) {
		echo "Successful";
	} else {
		echo "failed";
	}
}

#error alert function
function error( $to ) {
	$headers = "From: {$to}" . "\r\n" . 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
    	'Reply-To: noreply@example.com' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();

	$message = "<p><b>No data recieved from URL</b></p>";

	if ( mail($to, "Mortgage Monitor Error", $message, $headers) ) {
		echo "Successful";
	} else {
		echo "failed";
	}
	die();
}

function get_data($url) {
	$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

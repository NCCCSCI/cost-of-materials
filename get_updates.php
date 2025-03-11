<?php
#!/usr/bin/php

$phpInput = trim(file_get_contents('php://input'));
$jsonData = json_decode($phpInput,true);

$args              = [
    'store_id' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^\w{1,64}$/']],
    'username' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^\w{1,64}$/']],
    'password' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^\w{1,64}$/']]
];

$data   = filter_var_array($jsonData, $args);
if (!is_array($data)) {
    halt(401);
}

$storeId = $data['store_id'];
$username = $data['username'];
$password = $data['password'];
if (empty($storeId) || empty($username) || empty($password)) {
    halt(401);
}

$ftp_server = 'REDACTED';
$ftp_port = 0xEDAC;

// set up a connection or die
$connection = ssh2_connect($ftp_server,$ftp_port) or die("Couldn't connect");
if (!ssh2_auth_password($connection, $username, $password)) {
    halt(403,'Invalid credentials');
}
$remote = '/FMS'.$storeId.'_HEOAExtract.zip';
$tmp = tempnam(sys_get_temp_dir(),'HEOA_');

$sftp = ssh2_sftp($connection);
$stream = fopen('ssh2.sftp://' . intval($sftp) .'/.'. $remote, 'r');
if ($stream === false) {
    halt(502,'File open failed');
}
$bytesWritten = file_put_contents($tmp,$stream);
fclose($stream);
ssh2_disconnect($connection);


if ($bytesWritten === false){
    halt(500,'File write failed');
}

$temp = parse_ini_file(".env");

$ftp_server = $temp["server"];
$ftp_port = $temp["port"];
$storeId = $temp["store"];
$username = $temp["username"];
$password = $temp["password"];

$tmp = tempnam(sys_get_temp_dir(),'HEOA_');

$zip = new ZipArchive;
$zipOkay = true;
if ($zip->open($tmp) === TRUE) {
    $unzipped = $tmp.'-unzip/';
    mkdir($unzipped,0700);
    $zip->extractTo($unzipped);
    $zip->close();
} else {
    $zipOkay = false;
}

unlink($tmp);

if (!$zipOkay) {
    halt(500, 'Unzip failed');
}

$txt = $unzipped.str_replace('zip','txt',$tmp);

 if (exec('clamscan '.escapeshellarg($txt), $output, $resultCode) === false) {
     halt(500, 'Virus scan failed');
 }

 if($resultCode !== 0) {
     halt(500, 'Virus');
 }

// Thanks to: https://www.php.net/manual/en/function.readfile.php
if (file_exists($txt)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($txt).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($txt));
    readfile($txt);
    unlink($txt);
    rmdir($unzipped);
    exit;
}

function halt($code, $message = '') {
    echo $message;
    exit($code);
}
#!/usr/bin/php
<?php


$temp = parse_ini_file(".env");

$ftp_server = $temp["server"];
$ftp_port = $temp["port"];
$storeId = $temp["store"];
$username = $temp["username"];
$password = $temp["password"];

// set up a connection or die
$connection = ssh2_connect($ftp_server, $ftp_port) or die("Couldn't connect");
if (!ssh2_auth_password($connection, $username, $password)) {
    halt(403, 'Invalid credentials');
}
$remote = '/FMS' . $storeId . '_HEOAExtract.zip';
$tmp = tempnam(sys_get_temp_dir(), 'HEOA_');

$sftp = ssh2_sftp($connection);
$stream = fopen('ssh2.sftp://' . intval($sftp) . $remote, 'r');
if ($stream === false) {
    halt(502, 'File open failed');
}
$bytesWritten = file_put_contents($tmp, $stream);
fclose($stream);
ssh2_disconnect($connection);

if ($bytesWritten === false) {
    halt(500, 'File write failed');
}

$zip = new ZipArchive;
$zipOkay = true;
if ($zip->open($tmp) === TRUE) {
    $unzipped = $tmp . '-unzip';
    mkdir($unzipped, 0700);
    $zip->extractTo($unzipped);
    $zip->close();
} else {
    $zipOkay = false;
}

unlink($tmp);

if (!$zipOkay) {
    halt(500, 'Unzip failed');
}

$txt = $unzipped . str_replace('zip', 'txt', $remote);

if (exec('clamscan ' . escapeshellarg($txt), $output, $resultCode) === false) {
    halt(500, 'Virus scan failed');
}

if ($resultCode !== 0) {
    halt(500, 'Virus');
}

rename($txt,'/var/www/html/cost-of-materials/LATEST.txt');

exit(0);

function halt($code, $message = '') {
    echo $message;
    exit($code);
}

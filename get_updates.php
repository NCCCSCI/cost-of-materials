<?php
#!/usr/bin/php

$temp = parse_ini_file(".env");

$ftp_server = $temp["server"];
$ftp_port = $temp["port"];
$storeId = $temp["store"];
$username = $temp["username"];
$password = $temp["password"];

echo <<<END
    $ftp_server
    $ftp_port
    $storeId
    $username
    $password
END;

$tmp = "FMS0971_HEOAExtract.zip";

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
//var_dump($tmp);
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
    http_response_code($code);
    die($message);
}

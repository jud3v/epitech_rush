<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/plain; charset=utf-8');
$path = $_SERVER['DOCUMENT_ROOT'] . '/backend/' . $_GET['name'] . '.mytar';
if (isset($_GET['name']) && file_exists($path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
} else {
    echo 'not found';
}

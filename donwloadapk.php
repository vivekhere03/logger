<? 
<?php
session_start();
include 'dbconnection.php';
if (empty($_SESSION['user_logado'])) {
    unset($_SESSION['user_logado']);
    header("Location: " . BASE_URL . "login");
} else {


$file = "apk/apk.apk";

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    

} else {
    echo "file dont exist";
}

}
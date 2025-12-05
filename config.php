<?php
// config.php
session_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php'; // AWS SDK via composer

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/* ======= CONFIG - EDIT THESE ======= */
$bucket = 'event-management-assets-aditya';
$region = 'ap-south-1';
$adminPassword = 'admin123';   // change or secure as needed
$maxFileMB = 15;
$allowedExt = ['pdf','png','jpg','jpeg','doc','docx','txt','mp3','wav'];
/* =================================== */

/* ======= RDS CONFIG ======= */
$db_host = "eventdb.ctocm0ys45ax.ap-south-1.rds.amazonaws.com";
$db_user = "admin";
$db_pass = "Aditya_2910";
$db_name = "eventdb";
/* ========================= */

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

/* ===== S3 client (uses env/role credentials) ===== */
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region
]);

/* Helpers */
function sanitize_filename($name){
    return preg_replace('/[^a-zA-Z0-9_.-]/', '_', basename($name));
}
?>

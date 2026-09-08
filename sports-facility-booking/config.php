<?php
mysqli_report(MYSQLI_REPORT_OFF);
date_default_timezone_set('Asia/Kuala_Lumpur');
$host=getenv('DB_HOST')?:'localhost';
$user=getenv('DB_USER')?:'root';
$pass=getenv('DB_PASS')?:'';
$dbname=getenv('DB_NAME')?:'sports_facility_db';
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){http_response_code(503);die('Database connection failed. Please try again later.');}
$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+08:00'");


<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "db_taskflow";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
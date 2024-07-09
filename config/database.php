<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "pakan_ayam";

$conn = mysqli_connect($servername, $username, $password, $database);

//perikasa koneksi
if(!$conn){
    die("koneksi gagal: " . mysqli_connect_error());
}

//echo "koneksi berhasil";
?>
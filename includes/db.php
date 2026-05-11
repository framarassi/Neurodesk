<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "neurodesk";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connessione fallita: " . mysqli_connect_error());
}

?>
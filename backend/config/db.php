<?php

$host = "10.188.34.143";
$dbname = "contador_cfdb";
$username = "root";
$password = "root";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e){
    die("Erro de conexão: " . $e->getMessage());
}
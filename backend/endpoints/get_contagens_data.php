<?php 
header('Content-Type: application/json'); 
require '../config/db.php'; 

$hoje = date("Y-m-d"); 

$sql = $conn->prepare("SELECT * FROM contagens WHERE data_contagem = :data_contagem");
$sql->bindValue(":data_contagem", $hoje);
$sql->execute();
$response = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

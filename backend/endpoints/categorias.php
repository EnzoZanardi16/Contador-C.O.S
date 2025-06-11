<?php 
header('Content-Type: application/json'); 
require '../config/db.php'; 

$sql = "SELECT * FROM categorias"; 
$stmt = $conn->prepare($sql); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

echo json_encode($result); 
?>

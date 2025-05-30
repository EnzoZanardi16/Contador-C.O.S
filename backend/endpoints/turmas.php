<?php
header('Content-Type: application/json');
require '../config/db.php';

$sql = "SELECT id_turma, nome_turma FROM turmas WHERE id_turma > 12";
$stmt = $conn -> prepare($sql);
$stmt -> execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
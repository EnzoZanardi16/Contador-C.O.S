<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_SESSION['id_user368'])) {
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

$titulo = isset($_GET['titulo']) ? '%' . $_GET['titulo'] . '%' : '%';

try {
    $stmt = $conn->prepare("SELECT id, titulo, data, texto FROM anotacoes WHERE titulo LIKE :titulo ORDER BY data DESC");
    $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
    $stmt->execute();
    $anotacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($anotacoes);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao buscar anotações"]);
}

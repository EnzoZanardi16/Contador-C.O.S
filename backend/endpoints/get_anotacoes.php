<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

// Verifica se a nutricionista está logada
if (!isset($_SESSION['id_user368'])) {
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, titulo, data, texto FROM anotacoes ORDER BY data DESC");
    $stmt->execute();
    $anotacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($anotacoes);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao buscar anotações"]);
}

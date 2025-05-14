<?php
require_once "../config/db.php";
date_default_timezone_set('America/Sao_Paulo');

session_start();
header("Content-Type: application/json");

// Recebe o token via JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token']) || empty($data['token'])) {
    echo json_encode(["status" => "erro", "mensagem" => "Token não fornecido"]);
    exit;
}

$token = $data['token'];

// Verifica se o token existe e está ativo
$sql = "SELECT id_sessiom FROM sessions WHERE token = :token AND status = '1'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":token", $token, PDO::PARAM_STR);
$stmt->execute();
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if ($session) {
    // Atualiza o status da sessão para inativa (0)
    $update = $conn->prepare("UPDATE sessions SET status = '0' WHERE id_sessiom = :id");
    $update->bindParam(":id", $session['id_sessiom'], PDO::PARAM_INT);
    $update->execute();

    // Destroi a sessão PHP
    session_unset();
    session_destroy();

    echo json_encode(["status" => "success", "mensagem" => "Logout realizado com sucesso"]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Sessão inválida ou já encerrada"]);
}
?>

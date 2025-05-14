<?php
header("Content-Type: application/json");
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$id_contagem = trim($input["id_contagem"]);
$qtd_contagem = trim($input["qtd_contagem"]);
$turmas_id_turma = intval($input["turmas_id_turma"]);
$users368_id_user368 = intval($input["users368_id_user368"]);
$data = date("Y-m-d");
$time = date("H:i:s");
try {
    $stmt = $conn->prepare("UPDATE contagens SET data_contagem = :data_contagem, hora_contagem = :hora_contagem, qtd_contagem = :qtd_contagem, turmas_id_turma = :turmas_id_turma, users368_id_user368 = :users368_id_user368 WHERE id_contagem = :id_contagem");
    $stmt->bindParam(":id_contagem", $id_contagem, PDO::PARAM_STR);
    $stmt->bindParam(":data_contagem", $data);
    $stmt->bindParam(":hora_contagem", $time);
    $stmt->bindParam(":qtd_contagem", $qtd_contagem, PDO::PARAM_STR);
    $stmt->bindParam(":turmas_id_turma", $turmas_id_turma, PDO::PARAM_INT);
    $stmt->bindParam(":users368_id_user368", $users368_id_user368, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => "Contagem atualizada com sucesso", "id_contagem" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao atualizar contagem", "message" => $e->getMessage()]);
}
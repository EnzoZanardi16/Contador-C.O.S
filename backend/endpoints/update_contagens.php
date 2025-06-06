<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../config/db.php";

// Verifica se é uma requisição PATCH
if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

// Lê o corpo da requisição
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input["id_contagem"])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados incompletos"]);
    exit;
}

// Pega os dados do JSON
$id_contagem = trim($input["id_contagem"]);
$qtd_contagem = trim($input["qtd_contagem"]);
$turmas_id_turma = intval($input["turmas_id_turma"]);
$users368_id_user368 = intval($input["users368_id_user368"]);
$data = date("Y-m-d");
$time = date("H:i:s");

try {
    $stmt = $conn->prepare("
        UPDATE contagens
        SET 
            data_contagem = :data_contagem,
            hora_contagem = :hora_contagem,
            qtd_contagem = :qtd_contagem,
            turmas_id_turma = :turmas_id_turma,
            users368_id_user368 = :users368_id_user368
        WHERE id_contagem = :id_contagem
    ");

    $stmt->bindParam(":data_contagem", $data);
    $stmt->bindParam(":hora_contagem", $time);
    $stmt->bindParam(":qtd_contagem", $qtd_contagem);
    $stmt->bindParam(":turmas_id_turma", $turmas_id_turma, PDO::PARAM_INT);
    $stmt->bindParam(":users368_id_user368", $users368_id_user368, PDO::PARAM_INT);
    $stmt->bindParam(":id_contagem", $id_contagem);

    $stmt->execute();

    echo json_encode(["success" => "Contagem atualizada com sucesso"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao atualizar contagem", "message" => $e->getMessage()]);
}

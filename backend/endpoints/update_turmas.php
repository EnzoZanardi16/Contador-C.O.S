<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // Conexão centralizada

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

// Obtém os dados JSON enviados
$input = json_decode(file_get_contents("php://input"), true);


$id_turma = trim($input["id_turma"]);
$nome_turma = trim($input["nome_turma"]);
$categorias_id_categoria = trim($input["categorias_id_categoria"]); 

try {
    // Insere no banco de dados
    $stmt = $conn->prepare("UPDATE Turmas SET nome_turma = :nome_turma, categorias_id_categoria = :categorias_id_categoria WHERE id_turma = :id_turma");
    $stmt->bindParam(":id_turma", $id_turma, PDO::PARAM_STR);
    $stmt->bindParam(":nome_turma", $nome_turma, PDO::PARAM_STR);
    $stmt->bindParam(":categorias_id_categoria", $categorias_id_categoria, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(["success" => "Turma atualizada com sucesso", "id_turma" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao atualizar categoria", "message" => $e->getMessage()]);
}
?>
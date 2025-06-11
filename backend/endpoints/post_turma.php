<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // Conexão centralizada

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

// Obtém os dados JSON enviados
$input = json_decode(file_get_contents("php://input"), true);

// Validação dos campos obrigatórios
if (!isset($input["nome_turma"]) || empty(trim($input["nome_turma"]))) {
    echo json_encode(["error" => "O campo 'nome_turma' é obrigatório"]);
    exit;
}

if (!isset($input["categorias_id_categoria"]) || empty(trim($input["categorias_id_categoria"]))) {
    echo json_encode(["error" => "O campo 'categorias_id_categoria' é obrigatório"]);
    exit;
}

$nome_turma = trim($input["nome_turma"]);
$categorias_id_categoria = (int) $input["categorias_id_categoria"]; // Converte para inteiro

try {
    // Insere os dados na tabela turmas
    $stmt = $conn->prepare("INSERT INTO turmas (nome_turma, categorias_id_categoria) VALUES (:nome_turma, :categorias_id_categoria)");
    $stmt->bindParam(":nome_turma", $nome_turma, PDO::PARAM_STR);
    $stmt->bindParam(":categorias_id_categoria", $categorias_id_categoria, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => "Turma cadastrada com sucesso", "id_turma" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao cadastrar turma", "message" => $e->getMessage()]);
}
?>

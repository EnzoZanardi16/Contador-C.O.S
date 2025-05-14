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

// Valida o nome da categoria
if (!isset($input["nome_categoria"]) || empty(trim($input["nome_categoria"]))) {
    echo json_encode(["error" => "O campo 'nome_categoria' é obrigatório"]);
    exit;
}

$nome_categoria = trim($input["nome_categoria"]);

try {
    // Insere no banco de dados
    $stmt = $conn->prepare("INSERT INTO categorias (nome_categoria) VALUES (:nome_categoria)");
    $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(["success" => "Categoria cadastrada com sucesso", "id_categoria" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao cadastrar categoria", "message" => $e->getMessage()]);
}
?>

<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // Conexão centralizada

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}
$input = json_decode(file_get_contents("php://input"), true);

$id_categoria = trim($input["id_categoria"]);
$nome_categoria = trim($input["nome_categoria"]);

try {
    // Insere no banco de dados
    $stmt = $conn->prepare("UPDATE categorias SET nome_categoria = :nome_categoria WHERE id_categoria = :id_categoria");
    $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_STR);
    $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Categoria atualizada com sucesso", "id_categoria" => $id_categoria]);
    } else {
        echo json_encode(["warning" => "Nenhum dado foi alterado"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao atualizar categoria", "message" => $e->getMessage()]);
}

?>
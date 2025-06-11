<?php
header("Content-Type: application/json");
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

// Verificação normal
if (!isset($data["tabela"]) || !isset($data["id"])) {
    echo json_encode(["error" => "Parâmetros 'tabela' e 'id' são obrigatórios"]);
    exit;
}


$tabela = $data["tabela"];
$id = $data["id"];

try {
    $stmt = $conn->query("DESCRIBE $tabela");
    $tableFields = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $primaryKey = $tableFields[0];

    $stmt = $conn->prepare("DELETE FROM $tabela WHERE $primaryKey = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Item excluído com sucesso"]);
    } else {
        echo json_encode(["error" => "Item não encontrado"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao excluir item", "message" => $e->getMessage()]);
}

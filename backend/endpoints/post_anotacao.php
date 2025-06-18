<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // ajuste o caminho se necessário

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (
    !isset($input["titulo"]) || empty(trim($input["titulo"])) ||
    !isset($input["nutricionista_id"]) || empty($input["nutricionista_id"]) ||
    !isset($input["data"]) || empty(trim($input["data"])) ||
    !isset($input["texto"]) || empty(trim($input["texto"]))
) {
    echo json_encode(["error" => "Todos os campos são obrigatórios: titulo, nutricionista_id, data, texto"]);
    exit;
}

$titulo = trim($input["titulo"]);
$nutricionista_id = intval($input["nutricionista_id"]);
$data = trim($input["data"]);
$texto = trim($input["texto"]);

try {
    $stmt = $conn->prepare("INSERT INTO anotacoes (titulo, nutricionista_id, data, texto) VALUES (:titulo, :nutricionista_id, :data, :texto)");
    $stmt->bindParam(":titulo", $titulo, PDO::PARAM_STR);
    $stmt->bindParam(":nutricionista_id", $nutricionista_id, PDO::PARAM_INT);
    $stmt->bindParam(":data", $data, PDO::PARAM_STR);
    $stmt->bindParam(":texto", $texto, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        "success" => "Anotação cadastrada com sucesso",
        "id_anotacao" => $conn->lastInsertId()
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao cadastrar anotação", "message" => $e->getMessage()]);
}
?>

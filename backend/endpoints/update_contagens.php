<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input["id_contagem"]) || !isset($input["qtd_contagem"])) {
    http_response_code(400);
    echo json_encode(["error" => "Os campos id_contagem e qtd_contagem são obrigatórios."]);
    exit;
}

$id_contagem = trim($input["id_contagem"]);
$qtd_contagem = trim($input["qtd_contagem"]);

try {
    // Usar CURDATE() e CURTIME() do SQL é mais direto
    $stmt = $conn->prepare("
        UPDATE contagens
        SET 
            qtd_contagem = :qtd_contagem,
            data_contagem = CURDATE(),
            hora_contagem = CURTIME()
        WHERE id_contagem = :id_contagem
    ");

    $stmt->bindParam(":qtd_contagem", $qtd_contagem, PDO::PARAM_INT);
    $stmt->bindParam(":id_contagem", $id_contagem, PDO::PARAM_INT);

    $stmt->execute();

    // Verificar se alguma linha foi de fato atualizada
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Contagem atualizada com sucesso!"]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Contagem não encontrada ou nenhum dado foi alterado."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Erro no servidor ao atualizar contagem.",
        "message" => $e->getMessage()
    ]);
}
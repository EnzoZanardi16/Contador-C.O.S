<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // Conexão centralizada

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}


$input = json_decode(file_get_contents("php://input"), true);

$id_user368 = trim($input["id_user368"]);
$nome_user368 = trim($input["nome_user368"]);
$senha_user368 = trim($input["senha_user368"]); 
$nivel_user368 = trim($input["nivel_user368"]); 

try {

    $stmt = $conn->prepare("UPDATE users368 SET nome_user368 = :nome_user368, senha_user368 = :senha_user368, nivel_user368 = :nivel_user368 WHERE id_user368 = :id_user368");
    $stmt->bindParam(":id_user368", $id_user368, PDO::PARAM_STR);
    $stmt->bindParam(":nome_user368", $nome_user368, PDO::PARAM_STR);
    $stmt->bindParam(":senha_user368", $senha_user368, PDO::PARAM_STR);
    $stmt->bindParam(":nivel_user368", $nivel_user368, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Usuário atualizado com sucesso", "id_user368" => $id_user368]);
    } else {
        echo json_encode(["warning" => "Nenhum dado foi alterado", "id_user368" => $id_user368]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao atualizar usuário", "message" => $e->getMessage()]);
}
?>
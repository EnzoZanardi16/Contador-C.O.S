<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // Conexão com o banco

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

// Validação dos campos obrigatórios
if (
    !isset($input["nome_user368"]) || empty(trim($input["nome_user368"])) ||
    !isset($input["senha_user368"]) || empty($input["senha_user368"]) ||
    !isset($input["nivel_user368"])
) {
    echo json_encode(["error" => "Todos os campos são obrigatórios: nome_user368, senha_user368, nivel_user368"]);
    exit;
}

$nome = trim($input["nome_user368"]);
$senha = password_hash($input["senha_user368"], PASSWORD_DEFAULT);
$nivel = filter_var($input["nivel_user368"], FILTER_VALIDATE_INT); // sanitiza para inteiro

if ($nivel === false) {
    echo json_encode(["error" => "O nível do usuário deve ser um número inteiro"]);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO users368 (nome_user368, senha_user368, nivel_user368) VALUES (:nome, :senha, :nivel)");
    $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
    $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
    $stmt->bindParam(":nivel", $nivel, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => "Usuário cadastrado com sucesso",
        "id_user368" => $conn->lastInsertId()
    ]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["error" => "Nome de usuário já existe"]);
    } else {
        echo json_encode(["error" => "Erro ao cadastrar usuário", "message" => $e->getMessage()]);
    }
}
?>

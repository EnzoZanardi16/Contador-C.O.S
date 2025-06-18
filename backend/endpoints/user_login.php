<?php
require_once "../config/db.php";
date_default_timezone_set('America/Sao_Paulo'); // Fuso horário de Brasília

session_start();
header('Content-Type: application/json');

define('SESSION_TIMEOUT', 3600); // 1 hora

// Verificação de inatividade
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    echo json_encode(["status" => "erro", "mensagem" => "Sessão expirada. Faça login novamente."]);
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

$nome = $_POST['nome_user368'] ?? null;
$senha = $_POST['senha_user368'] ?? null;

if (!$nome || !$senha) {
    echo json_encode(["status" => "erro", "mensagem" => "Campos obrigatórios ausentes"]);
    exit;
}

// Buscar usuário
$sql = "SELECT id_user368, senha_user368, nivel_user368 FROM users368 WHERE nome_user368 = :nome";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && password_verify($senha, $result["senha_user368"])) {
    $_SESSION['nome_user368'] = $nome;
    $_SESSION['LAST_ACTIVITY'] = time();
    $_SESSION['nivel_user368'] = $result['nivel_user368'];
    $_SESSION['id_user368'] = $result['id_user368'];

    $id_user = $result["id_user368"];
    $agora = date("Y-m-d H:i:s");
    $expira_em = date("Y-m-d H:i:s", time() + SESSION_TIMEOUT);

    // Gerar token com sha256
    $token = hash("sha256", $nome . $agora);

    $_SESSION['token'] = $token;

    // Inserir na tabela de sessões
    $insert = $conn->prepare("INSERT INTO sessions (token, expira_em, status, users368_id_user368) VALUES (:token, :expira_em, '1', :user_id)");
    $insert->bindParam(":token", $token, PDO::PARAM_STR);
    $insert->bindParam(":expira_em", $expira_em, PDO::PARAM_STR);
    $insert->bindParam(":user_id", $id_user, PDO::PARAM_INT);
    $insert->execute();

    header("Location: ../../frontend/main.php");
    exit;
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário ou senha incorretos"]);
}
?>
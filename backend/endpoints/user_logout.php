<?php
// user_logout.php
require_once "../config/db.php";
date_default_timezone_set('America/Sao_Paulo');

session_start();

// 1) Tenta ler o token de três lugares:
//    a) JSON (API via php://input)
//    b) Form-POST (browser via $_POST)
//    c) Sessão PHP (fallback)
$input = json_decode(file_get_contents("php://input"), true);
$token = $input['token']
       ?? ($_POST['token'] ?? null)
       ?? ($_SESSION['token'] ?? null);

// 2) Se não veio token, retorna erro ou redireciona para login
if (!$token) {
    // Se o cliente pediu JSON, devolve JSON
    if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            "status"  => "erro",
            "mensagem"=> "Token não fornecido"
        ]);
        exit;
    }
    // Caso contrário, redireciona para a página de login
    header("Location: ../../frontend/index.php?erro=token_nao_fornecido");
    exit;
}

// 3) Verifica se o token está ativo na tabela `sessions`
$sql  = "SELECT id_sessiom 
         FROM sessions 
         WHERE token = :token 
           AND status = '1'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":token", $token, PDO::PARAM_STR);
$stmt->execute();
$sessionRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($sessionRow) {
    // 4) Desativa a sessão no banco
    $upd = $conn->prepare("
        UPDATE sessions 
           SET status = '0' 
         WHERE id_sessiom = :id
    ");
    $upd->bindParam(":id", $sessionRow['id_sessiom'], PDO::PARAM_INT);
    $upd->execute();

    // 5) Destroi a sessão PHP
    session_unset();
    session_destroy();

    // 6) Se for chamada API (JSON), retorna confirmação
    if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            "status"  => "success",
            "mensagem"=> "Logout realizado com sucesso"
        ]);
        exit;
    }

    // 7) Se for browser, redireciona para login
    header("Location: ../../frontend/index.php");
    exit;
}

// Se não encontrou sessão válida, trata como erro
if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
    header('Content-Type: application/json');
    echo json_encode([
        "status"  => "erro",
        "mensagem"=> "Sessão inválida ou já encerrada"
    ]);
    exit;
}

header("Location: ../../frontend/index.php?erro=sessao_invalida");
exit;

<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // Conexão centralizada
date_default_timezone_set('America/Sao_Paulo'); // Fuso horário de Brasília

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

// Obtém os dados JSON enviados
$input = json_decode(file_get_contents("php://input"), true);

// Validação dos campos obrigatórios
if (!isset($input["qtd_contagem"]) || empty(trim($input["qtd_contagem"]))) {
    echo json_encode(["error" => "O campo 'qtd_contagem' é obrigatório"]);
    exit;
}
if (!isset($input["turmas_id_turma"]) || empty(trim($input["turmas_id_turma"]))) {
    echo json_encode(["error" => "O campo 'turmas_id_turma' é obrigatório"]);
    exit;
}
$data = date('Y-m-d');
$hora = date('H:i:s');


$qtd_contagem = (int) $input["qtd_contagem"];
$turmas_id_turma = (int) $input["turmas_id_turma"];
// O usuário que realizou a contagem será fixo (inspetora de id 1)
$users368_id_user368 = 1;

$verify = $conn->prepare("SELECT * FROM contagens WHERE turmas_id_turma = :turma AND data_contagem = :data");
$verify->bindParam(":turma", $turmas_id_turma, PDO::PARAM_INT);
$verify->bindParam(":data", $data, PDO::PARAM_STR);
$verify->execute();
$response = $verify->fetch(PDO::FETCH_ASSOC);

if ($response == false) {
    try {
        // Insere os dados na tabela contagens
        $stmt = $conn->prepare("INSERT INTO contagens (data_contagem, hora_contagem, qtd_contagem, turmas_id_turma, users368_id_user368) VALUES (:data_contagem, :hora_contagem, :qtd_contagem, :turmas_id_turma, :users368_id_user368)");
        $stmt->bindParam(":data_contagem", $data, PDO::PARAM_STR);
        $stmt->bindParam(":hora_contagem", $hora, PDO::PARAM_STR);
        $stmt->bindParam(":qtd_contagem", $qtd_contagem, PDO::PARAM_INT);
        $stmt->bindParam(":turmas_id_turma", $turmas_id_turma, PDO::PARAM_INT);
        $stmt->bindParam(":users368_id_user368", $users368_id_user368, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => "Contagem cadastrada com sucesso", "id_contagem" => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Erro ao cadastrar contagem", "message" => $e->getMessage()]);
    }
}else{
    echo json_encode(['error' => 'Essa turma já possuí uma contagem cadastrada hoje']);
}

?>
<?php
header('Content-Type: application/json');

require '../config/db.php';

try {
    // Verifica se o parâmetro 'categoria' foi enviado via GET
    if (isset($_GET['categoria']) && is_numeric($_GET['categoria'])) {
        $categoria = (int) $_GET['categoria']; // <-- CAST explícito para evitar erro no bind
        $sql = "SELECT id_turma, nome_turma FROM turmas WHERE categorias_id_categoria = :categoria";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
    } else {
        $sql = "SELECT id_turma, nome_turma FROM turmas";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);

} catch (PDOException $e) {
    // Retorna erro com status HTTP 500
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro no banco de dados: " . $e->getMessage()
    ]);
}

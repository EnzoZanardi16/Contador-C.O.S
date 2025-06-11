<?php
// get_details.php - Retorna os detalhes de um item específico
include '../config/db.php';

if (isset($_GET['tabela']) && isset($_GET['id'])) {
    $tabela = $_GET['tabela'];
    $id = (int) $_GET['id'];

    // Mapeamento dos nomes das chaves primárias para cada tabela
    $chavesPrimarias = [
        'categorias' => 'id_categoria',
        'turmas' => 'id_turma',
        'contagens' => 'id_contagem',
        'users368' => 'id_user368'
    ];

    // Verifica se a tabela está no mapeamento
    if (!array_key_exists($tabela, $chavesPrimarias)) {
        echo json_encode(['erro' => 'Tabela inválida']);
        exit;
    }

    // Pega o nome da chave primária para a tabela
    $chavePrimaria = $chavesPrimarias[$tabela];

    // Prepara a consulta SQL usando a chave primária correta
    $stmt = $conn->prepare("SELECT * FROM $tabela WHERE $chavePrimaria = :id");
    $stmt->execute(['id' => $id]);

    // Recupera os dados
    $detalhes = $stmt->fetch();

    // Retorna os detalhes ou um erro se não encontrado
    if (!$detalhes) {
        echo json_encode(['erro' => 'Registro não encontrado']);
    } else {
        echo json_encode($detalhes);
    }
} else {
    echo json_encode(['erro' => 'Parâmetros inválidos']);
}
?>

<?php
header('Content-Type: application/json');
require '../config/db.php';

$hoje = date("Y-m-d");

$sql = $conn->prepare("
    SELECT 
        cat.nome_categoria AS categoria,
        t.nome_turma,
        SUM(c.qtd_contagem) AS qtd_contagem
    FROM contagens c
    JOIN turmas t ON c.turmas_id_turma = t.id_turma
    JOIN categorias cat ON t.categorias_id_categoria = cat.id_categoria
    WHERE c.data_contagem = :data_contagem
    GROUP BY cat.nome_categoria, t.nome_turma
");
$sql->bindValue(":data_contagem", $_GET['data'] ?? date("Y-m-d"));
$sql->execute();
$resultados = $sql->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por categoria para montar o formato esperado
$dadosAgrupados = [];

foreach ($resultados as $row) {
    $categoria = $row['categoria'];
    if (!isset($dadosAgrupados[$categoria])) {
        $dadosAgrupados[$categoria] = [];
    }
    $dadosAgrupados[$categoria][] = [
        'nome_turma' => $row['nome_turma'],
        'qtd_contagem' => (int) $row['qtd_contagem']
    ];
}

echo json_encode($dadosAgrupados);

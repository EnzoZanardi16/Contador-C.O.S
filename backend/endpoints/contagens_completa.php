<?php 
header('Content-Type: application/json'); 
require '../config/db.php'; 

$sql = "SELECT 
    contagens.data_contagem, 
    contagens.qtd_contagem, 
    turmas.nome_turma, 
    categorias.nome_categoria
FROM contagens
INNER JOIN turmas ON contagens.turmas_id_turma = turmas.id_turma
INNER JOIN categorias ON turmas.categorias_id_categoria = categorias.id_categoria;
"; 
$stmt = $conn->prepare($sql); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

echo json_encode($result); 
?>

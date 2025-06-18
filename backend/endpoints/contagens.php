<?php 
header('Content-Type: application/json'); 
require '../config/db.php'; 

$data = date("Y-m-d");

$sql = "
SELECT c.id_contagem, t.nome_turma, c.qtd_contagem, c.data_contagem, c.hora_contagem AS hora_contagem, SUM(c.qtd_contagem) OVER () AS soma
FROM contagens c
INNER JOIN turmas t ON c.turmas_id_turma = t.id_turma
WHERE DATE(c.data_contagem) = :data
ORDER BY c.data_contagem DESC
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(":data", $data);
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

echo json_encode($result); 
?>

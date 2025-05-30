<?php 
require_once "../config/db.php"; 

date_default_timezone_set("America/Sao_Paulo"); 
$hoje = date("Y-m-d"); 

try { 
    // Consulta agrupada por turma
    $stmt = $conn->prepare("
SELECT 
    CASE 
        WHEN turmas.id_turma IN (1,2,3,4,5) THEN 'fundamental1c'
        WHEN turmas.id_turma IN (6,7,8,9) THEN 'fundamental2c'
        WHEN turmas.id_turma IN (10,11,12) THEN 'Medio'
        ELSE 'Outros'
    END AS categoria,
    SUM(contagens.qtd_contagem) AS soma
FROM contagens
INNER JOIN turmas ON contagens.turmas_id_turma = turmas.id_turma
WHERE DATE(contagens.data_contagem) = :hoje
GROUP BY categoria

    "); 
    
    $stmt->bindParam(":hoje", $hoje); 
    $stmt->execute(); 
    $contagens = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    if ($contagens) { 
        echo json_encode($contagens); 
    } else { 
        echo json_encode(["error" => "Nenhuma contagem encontrada"]); 
    } 
} catch (PDOException $e) { 
    echo json_encode(["error" => "Erro ao buscar contagens", "message" => $e->getMessage()]); 
} 
?>

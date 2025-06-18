<?php 
require_once "../config/db.php"; 

date_default_timezone_set("America/Sao_Paulo"); 
$hoje = date("Y-m-d"); 

try { 
    // Consulta agrupada por turma (nome da turma)
    $stmt = $conn->prepare("
        SELECT 
            turmas.nome_turma AS turma,
            SUM(contagens.qtd_contagem) AS total
        FROM contagens
        INNER JOIN turmas ON contagens.turmas_id_turma = turmas.id_turma
        WHERE DATE(contagens.data_contagem) = :hoje
        GROUP BY turmas.nome_turma
        ORDER BY turmas.nome_turma ASC
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

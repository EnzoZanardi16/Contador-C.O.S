<?php 
require_once "../config/db.php"; 

date_default_timezone_set("America/Sao_Paulo"); 
$hoje = date("Y-m-d"); 

try { 
    // Consulta para obter todas as contagens e somar as qtd_contagem 
    $stmt = $conn->prepare("SELECT contagens.*, turmas.nome_turma, 
        (SELECT SUM(qtd_contagem) FROM contagens WHERE DATE(data_contagem) = :hoje) AS soma 
        FROM contagens 
        INNER JOIN turmas ON contagens.turmas_id_turma = turmas.id_turma 
        WHERE DATE(contagens.data_contagem) = :hoje"); 
    
    $stmt->bindParam(":hoje", $hoje); 
    $stmt->execute(); 

    // Recuperar todos os resultados 
    $contagens = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    // Verifique se existem dados 
    if ($contagens) { 
        echo json_encode($contagens); 
    } else { 
        echo json_encode(["error" => "Nenhuma contagem encontrada"]); 
    } 
} catch (PDOException $e) { 
    echo json_encode(["error" => "Erro ao buscar contagens", "message" => $e->getMessage()]); 
} 
?>

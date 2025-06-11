<?php 
require_once "../config/db.php"; 

date_default_timezone_set("America/Sao_Paulo"); 
$hoje = date("Y-m-d"); 

try { 
    $stmt = $conn->prepare("
        SELECT 
            c.id_contagem,
            c.qtd_contagem,
            c.data_contagem,
            t.nome_turma,
            cat.nome_categoria
        FROM contagens c
        INNER JOIN turmas t ON c.turmas_id_turma = t.id_turma
        INNER JOIN categorias cat ON t.categorias_id_categoria = cat.id_categoria
        WHERE DATE(c.data_contagem) = :hoje
        ORDER BY t.nome_turma
    "); 
    
    $stmt->bindParam(":hoje", $hoje); 
    $stmt->execute(); 
    $detalhadas = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    if ($detalhadas) { 
        echo json_encode($detalhadas); 
    } else { 
        echo json_encode(["error" => "Nenhuma contagem encontrada"]); 
    } 
} catch (PDOException $e) { 
    echo json_encode([
        "error" => "Erro ao buscar contagens", 
        "message" => $e->getMessage()
    ]); 
} 
?>

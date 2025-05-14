<?php 
header("Content-Type: application/json"); 

if (!isset($_GET['tabela'])) { 
    echo json_encode(["error" => "Parâmetro 'tabela' não fornecido"]); 
    exit; 
}

$tabela = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['tabela']); // Sanitiza o nome da tabela
require_once "../config/db.php"; // Usa a conexão centralizada 

try { 
    // Verifica se a tabela existe no banco de dados 
    $stmt = $conn->prepare("SHOW TABLES LIKE :tabela"); 
    $stmt->bindParam(':tabela', $tabela, PDO::PARAM_STR); 
    $stmt->execute(); 

    if ($stmt->rowCount() == 0) { 
        echo json_encode(["error" => "Tabela não encontrada"]); 
        exit; 
    }

    // Busca os dados da tabela 
    $stmt = $conn->prepare("SELECT * FROM $tabela"); 
    $stmt->execute(); 
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    echo json_encode($dados ?: ["error" => "Nenhum dado encontrado"]); 

} catch (PDOException $e) { 
    echo json_encode(["error" => "Erro no banco de dados", "message" => $e->getMessage()]); 
} 
?>

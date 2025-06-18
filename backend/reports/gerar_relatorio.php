<?php
define('FPDF_FONTPATH', __DIR__ . '/font/');
require(__DIR__ . '/fpdf.php');
require(__DIR__ . '/../config/db.php'); // Conexão via PDO

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetFillColor('255, 125, 125');
        $this->Cell(0, 10, utf8_decode('Relatório de Contagens por Turma'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Inicia PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Captura data via GET
$dataFiltro = $_GET['data'] ?? null;

try {
    if ($dataFiltro) {
        $stmt = $conn->prepare("
            SELECT 
                c.data_contagem, 
                t.nome_turma, 
                c.qtd_contagem, 
                u.nome_user368 AS responsavel
            FROM contagens c
            INNER JOIN turmas t ON c.turmas_id_turma = t.id_turma
            INNER JOIN users368 u ON c.users368_id_user368 = u.id_user368
            WHERE c.data_contagem = ?
            ORDER BY t.nome_turma ASC
        ");
        $stmt->execute([$dataFiltro]);
    } else {
        $stmt = $conn->query("
            SELECT 
                c.data_contagem, 
                t.nome_turma, 
                c.qtd_contagem, 
                u.nome_user368 AS responsavel
            FROM contagens c
            INNER JOIN turmas t ON c.turmas_id_turma = t.id_turma
            INNER JOIN users368 u ON c.users368_id_user368 = u.id_user368
            ORDER BY c.data_contagem DESC, t.nome_turma ASC
        ");
    }

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        $pdf->SetFillColor(255, 125, 125);
        $pdf->Cell(40, 10, 'Data', 1, 0, 'C', true);
        $pdf->Cell(60, 10, utf8_decode('Turma'), 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Qtd', 1, 0, 'C', true);
        $pdf->Cell(60, 10, utf8_decode('Responsável'), 1, 1, 'C', true);

        $totalQtd = 0; // variável para acumular o total

        foreach ($resultados as $row) {
            $pdf->Cell(40, 10, date('d/m/Y', strtotime($row['data_contagem'])), 1);
            $pdf->Cell(60, 10, utf8_decode($row['nome_turma']), 1);
            $pdf->Cell(30, 10, $row['qtd_contagem'], 1);
            $pdf->Cell(60, 10, utf8_decode($row['responsavel']), 1);
            $pdf->Ln();

            $totalQtd += (int)$row['qtd_contagem']; // soma a quantidade
        }

        // Linha do total no fim da tabela
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(2); // Espaço antes do total
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(255, 125, 125);
        $pdf->Cell(190, 10, utf8_decode("Total Geral de Alunos: ") . $totalQtd, 1, 1, 'R', true);
    } else {
        $pdf->Cell(0, 10, utf8_decode('Nenhuma contagem encontrada.'), 1, 1, 'C');
    }
} catch (PDOException $e) {
    $pdf->Cell(0, 10, utf8_decode('Erro ao gerar relatório: ') . $e->getMessage(), 1, 1, 'C');
}

// Saída do PDF
$pdf->Output('D', 'relatorio_contagens.pdf');

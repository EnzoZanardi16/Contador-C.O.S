<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contagens do Dia</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Meu css -->
    <link rel="stylesheet" href="../style.css">


</head>
<body>

<?php

include '../template/navbar.php';

?>

    <!-- Container -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary"><i class="bi bi-bar-chart-line"></i> Contagens do Dia</h2>
            <a href="javascript:history.back()" class="btn btn-outline-danger">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <!-- Exibição do total -->
        <div class="alert alert-light text-center" id="totalSoma">
            <i class="bi bi-calculator"></i> Soma total de contagens: <span id="somaValor">0</span>
        </div>

        <!-- Tabela de contagens -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Turma</th>
                            <th>QTD</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody id="contagemBody">
                        <tr>
                            <td colspan="4">Carregando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("../../backend/endpoints/contagen_dia.php")
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById("contagemBody");
                    tbody.innerHTML = "";
                    let somaTotal = 0;

                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Nenhuma contagem registrada hoje.</td></tr>`;
                        document.getElementById("somaValor").textContent = "0";
                        return;
                    }

                    data.forEach(contagem => {
                        somaTotal = contagem.soma;
                        let row = `<tr>
                            <td>${contagem.id_contagem}</td>
                            <td>${contagem.nome_turma}</td>
                            <td>${contagem.qtd_contagem}</td>
                            <td>${contagem.data_contagem} - ${contagem.hora_contagem}</td>
                        </tr>`;
                        tbody.innerHTML += row;
                    });

                    // Exibe a soma total
                    document.getElementById("somaValor").textContent = somaTotal;
                })
                .catch(error => {
                    console.error("Erro ao carregar contagens: ", error);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sem contagens!',
                        text: 'Nenhuma contagem foi cadastrada hoje.',
                        background: '#333',
                        color: '#ffffff',
                        customClass: {
                            popup: 'dark-alert'
                        }
                    });
                });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Contagem</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Meu css -->
  <link rel="stylesheet" href="../style.css">

  <script>
    async function carregarTurmas() {
      try {
        const response = await fetch("../../backend/endpoints/turmas.php");
        const turmas = await response.json();
        const select = document.getElementById("turmas_id_turma");
        select.innerHTML = '<option value="">Selecione uma turma</option>';
        turmas.forEach(turma => {
          const option = document.createElement("option");
          option.value = turma.id_turma;
          option.text = turma.nome_turma;
          select.appendChild(option);
        });
      } catch (error) {
        Swal.fire({ icon: 'error', title: 'Erro!', text: 'Não foi possível carregar as turmas.' });
      }
    }

    function setDataAtual() {
      const today = new Date().toISOString().split('T')[0];
      document.getElementById("data_contagem").value = today;
    }

    async function cadastrarContagem(event) {
      event.preventDefault();
      const dataContagem = document.getElementById("data_contagem").value;
      const now = new Date();
      const horaContagem = now.toTimeString().split(' ')[0];
      const qtdContagem = document.getElementById("qtd_contagem").value;
      const turmaId = document.getElementById("turmas_id_turma").value;
      if (!dataContagem || !qtdContagem || !turmaId) {
        Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Preencha todos os campos.' });
        return;
      }
      try {
        const response = await fetch("../../backend/endpoints/post_contagem.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            data_contagem: dataContagem,
            hora_contagem: horaContagem,
            qtd_contagem: parseInt(qtdContagem),
            turmas_id_turma: parseInt(turmaId)
          })
        });

        const text = await response.text();
        try {
          const result = JSON.parse(text);
          if (result.success) {
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Contagem cadastrada!' })
              .then(() => window.location.href = "../admin/dashboard.php");
          } else {
            Swal.fire({ icon: 'error', title: 'Erro!', text: result.error || "Erro ao cadastrar." });
          }
        } catch (error) {
          console.error("Erro na resposta do servidor:", text);
          Swal.fire({ icon: 'error', title: 'Erro!', text: "Erro inesperado no servidor." });
        }

      } catch (error) {
        Swal.fire({ icon: 'error', title: 'Erro!', text: error.toString() });
      }
    }

    window.onload = function () { setDataAtual(); carregarTurmas(); }
  </script>
</head>

<body>
  <?php

  include '../template/navbar.php';

  ?>

  <div class="container mt-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-center mb-4 text-primary"><i class="bi bi-clipboard-check"></i> Cadastrar Contagem</h2>
      <form id="contagemForm" onsubmit="cadastrarContagem(event)">
        <div class="mb-3">
          <label for="data_contagem" class="form-label">Data da Contagem:</label>
          <input type="date" class="form-control" id="data_contagem" required>
        </div>
        <div class="mb-3">
          <label for="qtd_contagem" class="form-label">Quantidade:</label>
          <input type="number" class="form-control" id="qtd_contagem" required>
        </div>
        <div class="mb-3">
          <label for="turmas_id_turma" class="form-label">Turma:</label>
          <select class="form-control" id="turmas_id_turma" required>
            <option value="">Carregando turmas...</option>
          </select>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirmar</button>
          <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Voltar</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
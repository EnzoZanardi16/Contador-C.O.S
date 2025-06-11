<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Turma</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Meu css -->
  <link rel="stylesheet" href="../style.css">

  
  <script>
    async function carregarCategorias() {
      try {
        const response = await fetch("../../backend/endpoints/categorias.php");
        const categorias = await response.json();
        const select = document.getElementById("categorias_id_categoria");
        select.innerHTML = '<option value="">Selecione uma categoria</option>';
        categorias.forEach(cat => {
          const option = document.createElement("option");
          option.value = cat.id_categoria;
          option.text = cat.nome_categoria;
          select.appendChild(option);
        });
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível carregar as categorias.',
          background: '#333',
          color: '#ffffff',
          customClass: {
              popup: 'dark-alert'
          }
        });
      }
    }

    async function cadastrarTurma(event) {
      event.preventDefault();
      const nomeTurma = document.getElementById("nome_turma").value.trim();
      const categoriaId = document.getElementById("categorias_id_categoria").value;
      if (!nomeTurma || !categoriaId) {
        Swal.fire({
          icon: 'warning',
          title: 'Atenção',
          text: 'Por favor, preencha todos os campos.',
          background: '#333',
          color: '#ffffff',
          customClass: {
              popup: 'dark-alert'
          }
        });
        return;
      }
      try {
        const response = await fetch("../../backend/endpoints/post_turma.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            nome_turma: nomeTurma,
            categorias_id_categoria: parseInt(categoriaId)
          })
        });
        const result = await response.json();
        if (result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Turma cadastrada com sucesso!'
          }).then(() => window.location.href = "../admin/dashboard.php");
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: result.error || "Erro ao cadastrar turma."
          });
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Erro na requisição!',
          text: error.toString()
        });
      }
    }
    window.onload = carregarCategorias;
  </script>
</head>
<body>
<?php

include '../template/navbar.php';

?>
  
  <!-- Main Container -->
  <div class="container mt-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-center mb-4 text-primary"><i class="bi bi-building"></i> Cadastrar Turma</h2>
      <form id="turmaForm" onsubmit="cadastrarTurma(event)">
        <div class="mb-3">
          <label for="nome_turma" class="form-label">Nome da Turma:</label>
          <input type="text" class="form-control" id="nome_turma" name="nome_turma" required>
        </div>
        <div class="mb-3">
          <label for="categorias_id_categoria" class="form-label">Categoria:</label>
          <select class="form-control" id="categorias_id_categoria" name="categorias_id_categoria" required>
            <option value="">Carregando categorias...</option>
          </select>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Cadastrar
          </button>
          <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Voltar
          </a>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

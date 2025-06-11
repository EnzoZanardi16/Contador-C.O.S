<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Usuário</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Meu css -->
  <link rel="stylesheet" href="../style.css">

  
  <script>
    async function cadastrarUsuario(event) {
      event.preventDefault();

      const nome = document.getElementById("nome_user368").value.trim();
      const senha = document.getElementById("senha_user368").value.trim();
      const nivel = document.getElementById("nivel_user368").value;

      if (!nome || !senha) {
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
        const response = await fetch("../../backend/endpoints/post_user.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            nome_user368: nome,
            senha_user368: senha,
            nivel_user368: parseInt(nivel)
          })
        });

        const result = await response.json();

        if (result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Usuário cadastrado com sucesso!',
            background: '#333',
            color: '#ffffff',
            customClass: {
                popup: 'dark-alert'
            }
          }).then(() => {
            window.location.href = "../admin/dashboard.php"; // Redireciona para a página principal
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: result.error || "Erro ao cadastrar usuário.",
            background: '#333',
            color: '#ffffff',
            customClass: {
                popup: 'dark-alert'
            }
          });
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Erro na requisição!',
          text: error.toString(),
          background: '#333',
          color: '#ffffff',
          customClass: {
              popup: 'dark-alert'
          }
        });
      }
    }
  </script>
</head>
<body>
<?php

include '../template/navbar.php';

?>
  
  <!-- Main Container -->
  <div class="container mt-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-center mb-4 text-primary">
        <i class="bi bi-person-plus"></i> Cadastrar Novo Usuário
      </h2>
      <form id="usuarioForm" onsubmit="cadastrarUsuario(event)">
        <div class="mb-3">
          <label for="nome_user368" class="form-label">Nome do Usuário:</label>
          <input type="text" class="form-control" id="nome_user368" name="nome_user368" required>
        </div>
        <div class="mb-3">
          <label for="senha_user368" class="form-label">Senha:</label>
          <input type="password" class="form-control" id="senha_user368" name="senha_user368" required>
        </div>
        <div class="mb-3">
          <label for="nivel_user368" class="form-label">Nível:</label>
          <select class="form-control" id="nivel_user368" name="nivel_user368" required>
            <option value="1">Usuário Comum</option>
            <option value="2">Moderador</option>
            <option value="3">Administrador</option>
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

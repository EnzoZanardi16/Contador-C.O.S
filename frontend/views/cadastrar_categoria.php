<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Categoria</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Meu css -->
  <link rel="stylesheet" href="../style.css">

  
  <script>
    async function cadastrarCategoria(event) {
      event.preventDefault();
      const nomeCategoria = document.getElementById("nome_categoria").value.trim();

      if (!nomeCategoria) {
        Swal.fire({
          icon: 'warning',
          title: 'Atenção',
          text: 'Por favor, insira o nome da categoria.'
        });
        return;
      }

      try {
        const response = await fetch("../../backend/endpoints/post_categoria.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ nome_categoria: nomeCategoria })
        });

        const result = await response.json();

        if (result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Categoria cadastrada com sucesso!'
          }).then(() => {
            window.location.href = "../admin/dashboard.php";
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: result.error || "Erro ao cadastrar categoria."
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
  </script>
</head>
<body>
  <?php
  
  include '../template/navbar.php';
    
  ?>
  
  <!-- Main Container -->
  <div class="container mt-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-center mb-4 text-primary"><i class="bi bi-plus-circle"></i> Cadastrar Nova Categoria</h2>
      <form id="categoriaForm" onsubmit="cadastrarCategoria(event)">
        <div class="mb-3">
          <label for="nome_categoria" class="form-label">Nome da Categoria:</label>
          <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" required>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Cadastrar</button>
          <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Voltar</a>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

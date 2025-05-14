<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Sistema de Contagens</title>
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

  <!-- Main Container -->
  <div class="container my-4">
    <h1 class="text-center mb-4 text-primary">Dashboard</h1>
    <div id="cards-container" class="row gy-4"></div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const tabelas = {
        "categorias": "Categorias",
        "contagens": "Contagens",
        "turmas": "Turmas",
        "users368": "Usuários"
      };

      Object.keys(tabelas).forEach(tabela => {
        fetch(`../../backend/endpoints/get_data.php?tabela=${tabela}`)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              console.error("Erro ao carregar " + tabela + ": " + data.error);
              return;
            }

            let container = document.getElementById("cards-container");

            let col = document.createElement("div");
            col.className = "col-md-3";

            let card = document.createElement("div");
            card.className = "card shadow-sm";

            let cardBody = document.createElement("div");
            cardBody.className = "card-body text-center";

            let title = document.createElement("h5");
            title.className = "card-title";
            title.textContent = tabelas[tabela] || tabela;

            let list = document.createElement("ul");
            list.className = "list-group list-group-flush";

            data.forEach(row => {
              let listItem = document.createElement("li");
              listItem.className = "list-group-item d-flex justify-content-between align-items-center";

              let chaves = Object.keys(row);
              let segundoCampo = chaves.length > 1 ? chaves[1] : null;
              let valor = segundoCampo ? row[segundoCampo] : "Sem dados";

              let id = row[chaves[0]];

              let textSpan = document.createElement("span");
              textSpan.textContent = valor;

              let viewButton = document.createElement("button");
              viewButton.className = "btn btn-primary btn-sm";
              viewButton.innerHTML = '<i class="bi bi-pencil-square"></i>';
              viewButton.onclick = function () {
                if (id) {
                  window.location.href = `../views/view_details.php?tabela=${tabela}&id=${id}`;
                }
              };

              let deleteButton = document.createElement("button");
              deleteButton.className = "btn btn-danger btn-sm";
              deleteButton.innerHTML = '<i class="bi bi-trash3"></i>';
              deleteButton.onclick = function () {
                Swal.fire({
                  title: `Excluir item?`,
                  text: `Tem certeza que deseja excluir este item da tabela ${tabela}?`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Sim, excluir!',
                  background: '#1e1e1e',
                  color: '#ffffff'
                }).then((result) => {
                  if (result.isConfirmed) {
                    fetch(`../../backend/endpoints/delete_data.php?tabela=${tabela}&id=${id}`, { method: "GET" })
                      .then(response => response.json())
                      .then(result => {
                        if (result.success) {
                          Swal.fire({ icon: 'success', title: 'Excluído!', text: 'Item excluído com sucesso!' })
                            .then(() => window.location.reload());
                        } else {
                          Swal.fire({ icon: 'error', title: 'Erro!', text: result.error || "Erro ao excluir item." });
                        }
                      })
                      .catch(error => {
                        Swal.fire({ icon: 'error', title: 'Erro!', text: 'Erro ao excluir item.' });
                      });
                  }
                });
              };

              listItem.appendChild(textSpan);
              listItem.appendChild(viewButton);
              listItem.appendChild(deleteButton);
              list.appendChild(listItem);
            });

            cardBody.appendChild(title);
            card.appendChild(cardBody);
            card.appendChild(list);
            col.appendChild(card);
            container.appendChild(col);
          })
          .catch(error => {
            console.error("Erro ao buscar dados de " + tabela, error);
            Swal.fire({
              icon: 'error',
              title: 'Erro!',
              text: `Erro ao buscar dados da tabela ${tabela}.`
            });
          });
      });
    });

  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
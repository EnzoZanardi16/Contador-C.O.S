<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detalhes</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <?php include '../template/navbar.php'; ?>

  <div class="container my-4">
    <a href="javascript:history.back()" class="btn btn-outline-warning">Voltar</a>
    <div id="details" class="mt-4"></div>
    <!-- Botão Editar -->
    <div class="mt-3">
      <button id="editButton" class="btn btn-warning">
        <i class="bi bi-pencil-square"></i> Editar
      </button>
    </div>
  </div>

  <!-- Modal de Edição -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="editForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Editar Registro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <!-- Aqui os campos serão gerados dinamicamente -->
          <div id="formFields"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const urlParams = new URLSearchParams(window.location.search);
      const tabela = urlParams.get("tabela");
      const id = urlParams.get("id");

      if (!tabela || !id) {
        Swal.fire({
          icon: 'error',
          title: 'Parâmetros inválidos',
          text: 'Não foi possível carregar os detalhes!'
        });
        document.getElementById("details").innerHTML = "<p>Erro: Parâmetros inválidos!</p>";
        return;
      }

      // Mapeamento para exibir nomes amigáveis para cada campo
      const fieldLabels = {
        id_contagem: 'ID da Contagem',
        data_contagem: 'Data da Contagem',
        hora_contagem: 'Hora da Contagem',
        qtd_contagem: 'Quantidade da Contagem',
        turmas_id_turma: 'ID da Turma',
        users368_id_user368: 'ID do Usuário',
        id_turma: 'ID da Turma',
        nome_turma: 'Nome da Turma',
        categorias_id_categoria: 'ID da Categoria',
        id_user368: 'ID do Usuário',
        nome_user368: 'Nome do Usuário',
        senha_user368: 'Senha do Usuário',
        nivel_user368: 'Nível do Usuário',
        id_categoria: 'ID da Categoria',
        nome_categoria: 'Nome da Categoria'
      };

      let detailsData = {}; // Para armazenar os dados retornados

      // Buscar os detalhes
      fetch(`../../backend/endpoints/get_details.php?tabela=${tabela}&id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.erro) {
            Swal.fire({ icon: 'error', title: 'Erro!', text: data.erro });
            document.getElementById("details").innerHTML = `<p>Erro: ${data.erro}</p>`;
            return;
          }

          detailsData = data; // Armazena para uso posterior

          const detailsContainer = document.getElementById("details");
          detailsContainer.innerHTML = `<h2 class='text-center'>Detalhes de ${tabela}</h2>`;
          const list = document.createElement("ul");
          list.className = "list-group list-group-flush";

          Object.keys(data).forEach(key => {
            // Ignora chaves numéricas
            if (!isNaN(parseInt(key))) return;
            const label = fieldLabels[key] || key;
            const item = document.createElement("li");
            item.className = "list-group-item border-secondary";
            item.textContent = `${label}: ${data[key]}`;
            list.appendChild(item);
          });
          detailsContainer.appendChild(list);
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Não foi possível buscar os detalhes.'
          });
        });

      // Configuração do modal de edição (usando Bootstrap 5)
      const editModal = new bootstrap.Modal(document.getElementById('editModal'));

      // Ao clicar em Editar, preenche o formulário com os dados atuais
      document.getElementById("editButton").addEventListener("click", () => {
        const formFields = document.getElementById("formFields");
        formFields.innerHTML = ""; // Limpa campos anteriores

        // Para cada campo (exceto o ID, se preferir não editar)
        Object.keys(detailsData).forEach(key => {
          if (!isNaN(parseInt(key))) return; // ignora chaves numéricas
          // Se desejar, pode tornar o campo ID somente leitura
          const isIdField = key.toLowerCase().includes("id");

          const divGroup = document.createElement("div");
          divGroup.className = "mb-3";

          const label = document.createElement("label");
          label.className = "form-label";
          label.textContent = fieldLabels[key] || key;
          label.setAttribute("for", key);

          const input = document.createElement("input");
          input.className = "form-control";
          input.id = key;
          input.name = key;
          input.value = detailsData[key];
          if(isIdField) {
            input.readOnly = true;
          }

          divGroup.appendChild(label);
          divGroup.appendChild(input);
          formFields.appendChild(divGroup);
        });

        editModal.show();
      });

      // Ao enviar o formulário, coleta os dados e envia para o endpoint de update
      document.getElementById("editForm").addEventListener("submit", function(e) {
        e.preventDefault();
        // Coleta os dados do formulário
        const formData = new FormData(this);
        let updateData = {};
        formData.forEach((value, key) => {
          updateData[key] = value;
        });

        // Define o endpoint de update de acordo com a tabela
        let updateEndpoint = "";
        switch (tabela) {
          case "categorias":
            updateEndpoint = "update_categoria.php";
            break;
          case "turmas":
            updateEndpoint = "update_turmas.php";
            break;
          case "users368":
            updateEndpoint = "update_users.php";
            break;
          case "contagens":
            updateEndpoint = "update_contagens.php";
            break;
          default:
            Swal.fire({ icon: 'error', title: 'Erro!', text: 'Tabela desconhecida para atualização.' });
            return;
        }

        // Envia os dados (exemplo com método POST; ajuste se necessário)
        fetch(`../../backend/endpoints/${updateEndpoint}?tabela=${tabela}&id=${id}`, {
          method: "PATCH",
          body: JSON.stringify(updateData),
          headers: {
            "Content-Type": "application/json"
          }
        })
        .then(response => response.json())
        .then(result => {
          if(result.success) {
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Registro atualizado com sucesso!' })
              .then(() => location.reload());
          } else {
            Swal.fire({ icon: 'error', title: 'Erro!', text: result.error || "Erro ao atualizar registro." });
          }
        })
        .catch(error => {
          Swal.fire({ icon: 'error', title: 'Erro!', text: 'Erro ao atualizar registro.' });
        });
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

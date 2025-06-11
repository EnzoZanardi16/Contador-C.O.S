<?php
session_start();
require '../backend/config/db.php';

ini_set("display_errors", 1);

$dataAtualFormatadaParaSQL = date('Y-m-d');
$nomeUsuario = $_SESSION['nome_user368'] ?? 'Usuário';
$nomeUsuarioFormatado = ucwords(strtolower($nomeUsuario));
$tokenSessao = $_SESSION['token'] ?? '';
$nivelSessao = intval($_SESSION['nivel_user368']);

function buscarSomaContagemPorCategoriaPHP($conn, $categoria, $data)
{
    $sql = "SELECT SUM(c.qtd_contagem) AS total 
            FROM contagens c 
            JOIN turmas t ON c.turmas_id_turma = t.id_turma 
            JOIN categorias cat ON t.categorias_id_categoria = cat.id_categoria 
            WHERE cat.id_categoria = :categoria AND c.data_contagem = :data";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
    $stmt->bindValue(':data', $data);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado['total'] ?? 0;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Alunos SESI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/styles.css" rel="stylesheet">
</head>

<body>
    <button class="menu-toggle" aria-label="Abrir menu">☰</button>
    <div class="overlay-menu"></div>

    <div class="sidebar">
        <div class="sidebar-header">
            <img src="./assets/C.O.S-white.png" alt="Logo C.O.S" class="img-fluid">
            <h2>Bem-vindo</h2>
        </div>

        <ul class="nav flex-column w-100">
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="pagina-home"><i class="bi bi-house-door-fill"></i> Home</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="pagina-contagem"><i class="bi bi-bar-chart-fill"></i> Contagem
                    Geral</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownNivelEnsino" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-mortarboard-fill"></i> Nível de Ensino
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownNivelEnsino">
                    <li><a class="dropdown-item" href="#" data-page="pagina-fundamental1"><i
                                class="bi bi-pencil-fill"></i> Fundamental I</a></li>
                    <li><a class="dropdown-item" href="#" data-page="pagina-fundamental2"><i
                                class="bi bi-journal-bookmark-fill"></i> Fundamental II</a></li>
                    <li><a class="dropdown-item" href="#" data-page="pagina-ensinoMedio"><i class="bi bi-book-fill"></i>
                            Ensino Médio</a></li>
                    <li><a class="dropdown-item" href="#" data-page="pagina-outros"><i class="bi bi-three-dots"></i>
                            Outros</a></li>
                </ul>
            </li>
            <?php
                if($nivelSessao == 1 or $nivelSessao == 2){
                    echo '
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="painel-nutricionista">
                                <i class="bi bi-card-checklist"></i>
                                Painel Nutricionista
                            </a>
                        </li>
                    ';
                }
            ?>
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="pagina-usuario">
                    <i class="bi bi-person-fill"></i>
                    <?php echo htmlspecialchars($nomeUsuarioFormatado, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </li>
        </ul>
    </div>

    <div id="pagina-home" class="content">
        <h1 class="home-titulo">Realize a contagem agora</h1>
        <span class="line-titulo"></span>
        <div class="info-container">
            <p id="data-atual-home">Data: Carregando...</p>
            <p>Seja bem-vindo ao Contador de Alunos do SESI, uma ferramenta digital desenvolvida para monitorar, em
                tempo real, o número de alunos. Este sistema foi criado com o objetivo de oferecer uma visão clara e
                acessível sobre a quantidade de estudantes no dia, proporcionando praticidade e facilidade de
                acompanhamento tanto para a administração quanto para os membros da equipe da cozinha.</p>
        </div>
        <div class="contact-container">
            <div class="contact-item">
                <a href="https://www.google.com/maps/search/?api=1&query=SESI+Regente+Feijo" target="_blank"
                    rel="noopener noreferrer">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>SESI - Regente Feijó</span>
                    
                </a>
            </div>
            <div class="contact-item">
                <a href="mailto:codingthefuture.cf@gmail.com">
                    <i class="bi bi-envelope-fill"></i> <span>codingthefuture.cf@gmail.com</span>
                </a>
            </div>
        </div>
    </div>

    <div id="pagina-contagem" class="content">
        <div class="container py-4">
            <h1 class="text-center mb-5 display-5 fw-bold text-black" style="text-decoration;">Contagens</h1>
            <div class="row justify-content-center g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 bg-danger text-white rounded-4 border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-between py-4">
                            <div class="bg-light text-danger rounded-4 d-flex align-items-center justify-content-center mb-3"
                                style="width: 100px; height: 100px;">
                                <h2 id="contagem-fundamental-i" class="display-6 fw-bold mb-0">-</h2>
                            </div>
                            <hr class="border-white opacity-50 w-75 my-2">
                            <div class="bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
                                data-escopo="fundamental-i" onclick="abrirTabelaEscopo(this)">
                                <i class="bi bi-mortarboard-fill me-2"></i>Fundamental I
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 bg-danger text-white rounded-4 border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-between py-4">
                            <div class="bg-light text-danger rounded-4 d-flex align-items-center justify-content-center mb-3"
                                style="width: 100px; height: 100px;">
                                <h2 id="contagem-fundamental-ii" class="display-6 fw-bold mb-0">-</h2>
                            </div>
                            <hr class="border-white opacity-50 w-75 my-2">
                            <div class="bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
                                data-escopo="fundamental-ii" onclick="abrirTabelaEscopo(this)">
                                <i class="bi bi-mortarboard-fill me-2"></i>Fundamental II
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 bg-danger text-white rounded-4 border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-between py-4">
                            <div class="bg-light text-danger rounded-4 d-flex align-items-center justify-content-center mb-3"
                                style="width: 100px; height: 100px;">
                                <h2 id="contagem-ensino-medio" class="display-6 fw-bold mb-0">-</h2>
                            </div>
                            <hr class="border-white opacity-50 w-75 my-2">
                            <div class="bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
                                data-escopo="ensino-medio" onclick="abrirTabelaEscopo(this)">
                                <i class="bi bi-journal-bookmark-fill me-2"></i>Ensino Médio
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 bg-danger text-white rounded-4 border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-between py-4">
                            <div class="bg-light text-danger rounded-4 d-flex align-items-center justify-content-center mb-3"
                                style="width: 100px; height: 100px;">
                                <h2 id="contagem-outros-geral" class="display-6 fw-bold mb-0">-</h2>
                            </div>
                            <hr class="border-white opacity-50 w-75 my-2">
                            <div class="bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
                                data-escopo="outros" onclick="abrirTabelaEscopo(this)">
                                <i class="bi bi-three-dots me-2"></i>Outros
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="pagina-usuario" class="content">
        <div class="card-user-container">
            <div class="card p-4 shadow rounded-4">
                <div class="d-flex align-items-center mb-4">
                    <img src="./assets/images-removebg-preview.png" alt="Ícone do Usuário" class="rounded-circle">
                    <h1 class="h4 mb-0">
                        <?php echo htmlspecialchars($nomeUsuarioFormatado, ENT_QUOTES, 'UTF-8'); ?>
                    </h1>
                </div>
                <form id="form-logout" action="../backend/endpoints/user_logout.php" method="POST">
                    <input type="hidden" name="token"
                        value="<?php echo htmlspecialchars($tokenSessao, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-outline-danger w-100">Sair da Conta</button>
                </form>
            </div>
        </div>
    </div>

    <div id="painel-nutricionista" class="content">
        <h1 class="home-titulo">Calendário das Contagens</h1>
        <div id="calendar-container"></div>

        <button id="ver-contagens" class="btn btn-danger mt-3" disabled>Ver Contagens</button>

        <div class="modal fade" id="contagemModal" tabindex="-1" aria-labelledby="contagemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contagemModalLabel">Contagens do Dia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body" id="resultado-contagens">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pagina-fundamental1" class="content cards-turmas-container">
        <h1 class="text-center my-4">Fundamental I</h1>
        <div class="container">
            <div class="row g-4 justify-content-center" id="cardsContainerFund1">
            </div>
        </div>
    </div>

    <div id="pagina-fundamental2" class="content cards-turmas-container">
        <h1 class="text-center my-4">Fundamental II</h1>
        <div class="container">
            <div class="row g-4 justify-content-center" id="cardsContainerFund2">
            </div>
        </div>
    </div>

    <div id="pagina-ensinoMedio" class="content cards-turmas-container">
        <h1 class="text-center my-4">Ensino Médio</h1>
        <div class="container">
            <div class="row g-4 justify-content-center" id="cardsContainerEM">
            </div>
        </div>
    </div>

    <div id="pagina-outros" class="content cards-turmas-container">
        <h1 class="text-center my-4">Outros</h1>
        <div class="container">
            <div class="row g-4 justify-content-center" id="cardsContainerOutros">
            </div>
            <div class="text-center mt-4">
                <button id="btnCadastrarNovaCategoriaOutros" class="btn btn-danger">Cadastrar Nova Turma em
                    Outros</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        // Bloco de Script Principal
        const urlsBackend = {
            postContagens: "../backend/endpoints/post_contagens.php",
            getContagensDia: "../backend/endpoints/contagens_dia.php",
            getTurmasOutros: "../backend/endpoints/turmas.php?categoria=5" // Categoria 5 para "Outros"
        };

        /**
         * Exibe alertas usando SweetAlert2.
         * @param {string} icon - Ícone do alerta ('success', 'error', 'warning', 'info', 'question').
         * @param {string} title - Título do alerta.
         * @param {string} text - Texto do alerta.
         */
        function exibirAlerta(icon, title, text) {
            Swal.fire({ icon, title, text });
        }

        /**
         * Altera o valor do contador no card.
         * @param {HTMLElement} button - O botão clicado (+ ou -).
         * @param {number} delta - O valor a ser adicionado ou subtraído.
         */
        function alterarContadorCard(button, delta) {
            const counterElement = button.parentElement.querySelector(".counter");
            if (!counterElement) return;
            let currentValue = parseInt(counterElement.textContent, 10);
            currentValue = Math.max(0, currentValue + delta); // Não permite valores negativos
            counterElement.textContent = currentValue;
        }
        window.alterarContadorCard = alterarContadorCard; // Expor globalmente se chamado por HTML inline

        /**
         * Envia a contagem para o backend.
         * @param {HTMLElement} button - O botão de confirmação clicado.
         * @param {string|number} turmaId - O ID da turma.
         */
        async function confirmarContagem(button, turmaId) {
            const cardBody = button.closest('.card-body');
            if (!cardBody) return;
            const counterValue = parseInt(cardBody.querySelector(".counter").textContent, 10);

            const dadosContagem = {
                qtd_contagem: counterValue,
                turmas_id_turma: turmaId
            };

            try {
                const response = await fetch(urlsBackend.postContagens, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(dadosContagem)
                });
                const data = await response.json();
                if (data.success) {
                    exibirAlerta('success', 'Contagem salva!', 'Os dados foram registrados com sucesso.');
                } else {
                    exibirAlerta('error', 'Erro ao salvar', data.error || "Erro desconhecido ao salvar contagem.");
                }
            } catch (error) {
                console.error("Erro na requisição de confirmação de contagem:", error);
                exibirAlerta('error', 'Erro de comunicação', 'Não foi possível enviar a contagem. Tente novamente.');
            }
        }
        window.confirmarContagem = confirmarContagem; // Expor globalmente

        /**
         * Cria e adiciona os cards de turmas a um container.
         * @param {Array<Object>} listaTurmas - Array de objetos, cada um com 'nome' e 'id' da turma.
         * @param {string} containerId - ID do elemento container onde os cards serão inseridos.
         * @param {string} [sufixoNome=""] - Sufixo a ser adicionado ao nome da turma no card.
         * @param {boolean} [estiloPersonalizadoOutros=false] - Aplica estilo especial para a categoria "Outros".
         */
        function criarCardsTurma(listaTurmas, containerId, sufixoNome = "", estiloPersonalizadoOutros = false) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container com id "${containerId}" não encontrado.`);
                return;
            }
            container.innerHTML = ""; // Limpa o container antes de adicionar novos cards

            listaTurmas.forEach(turma => {
                const col = document.createElement("div");
                col.className = "col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-4"; // Ajustado para melhor responsividade

                const cardClasses = estiloPersonalizadoOutros ?
                    "card text-center h-100 shadow-sm rounded-4 card-outros-personalizado" :
                    "card text-center border-danger h-100 shadow-sm rounded-4";

                const nomeExibicao = estiloPersonalizadoOutros ? turma.nome : `${turma.nome}${sufixoNome}`;
                const tituloClasses = estiloPersonalizadoOutros ? "card-title" : "card-title text-danger";
                const hrClasses = estiloPersonalizadoOutros ? "border-white" : "border-danger";
                const btnCounterClasses = estiloPersonalizadoOutros ? "btn btn-light text-danger rounded-circle me-2 btn-counter" : "btn btn-outline-danger rounded-circle me-2 btn-counter";
                const btnConfirmarClasses = estiloPersonalizadoOutros ? "btn btn-light text-danger fw-bold w-100 mt-auto btn-confirmar" : "btn btn-outline-danger w-100 mt-auto btn-confirmar";

                col.innerHTML = `
<div class="${cardClasses} bg-danger rounded-4 text-center">
    <div class="card-body d-flex flex-column justify-content-between p-3 text-white">
        <h5 class="${tituloClasses} fw-bold text-white">${nomeExibicao}</h5>
        <hr class="${hrClasses} border-white opacity-50 my-2" />
        <div class="d-flex align-items-center justify-content-center my-3">
            <button class="${btnCounterClasses} btn btn-light text-danger rounded-circle fw-bold fs-4 hover-darken"
                style="width: 45px; height: 45px;"
                onclick="alterarContadorCard(this, -1)">−</button>
            <span class="fs-4 fw-bold counter mx-3 text-white">0</span>
            <button class="${btnCounterClasses.replace('me-2', 'ms-2')} btn btn-light text-danger rounded-circle fw-bold fs-4 hover-darken"
                style="width: 45px; height: 45px;"
                onclick="alterarContadorCard(this, 1)">+</button>
        </div>
        <button class="${btnConfirmarClasses} btn btn-light text-danger fw-bold rounded-3 mt-2 hover-darken"
            onclick="confirmarContagem(this, '${turma.id}')">Confirmar</button>
    </div>
</div>


`;
                container.appendChild(col);
            });
        }

        /**
         * Carrega e renderiza as turmas da categoria "Outros".
         */
        async function carregarTurmasOutros() {
            try {
                const response = await fetch(urlsBackend.getTurmasOutros);
                if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                const data = await response.json();
                const turmasFormatadas = data.map(t => ({ nome: t.nome_turma, id: t.id_turma }));
                criarCardsTurma(turmasFormatadas, "cardsContainerOutros", "", true);
            } catch (error) {
                console.error("Erro ao buscar dados da categoria 'Outros':", error);
                exibirAlerta('error', 'Erro ao carregar "Outros"', 'Não foi possível carregar as turmas da categoria "Outros".');
                const container = document.getElementById("cardsContainerOutros");
                if (container) container.innerHTML = '<p class="text-danger text-center">Falha ao carregar turmas.</p>';
            }
        }

        /**
         * Carrega os dados de contagem do dia e atualiza o dashboard.
         */
        async function carregarDadosContagemDashboard() {
            try {
                const response = await fetch(urlsBackend.getContagensDia);
                if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                const data = await response.json();

                if (data.error) {
                    console.error("Erro retornado do servidor (contagens_dia):", data.error, data.message || "");
                    exibirAlerta('error', 'Erro de Dados', 'Não foi possível carregar os totais das contagens.');
                    return;
                }

                if (Array.isArray(data)) {
                    data.forEach(item => {
                        // Mapeia o nome da categoria para o ID do elemento no HTML
                        let elementId = '';
                        const categoriaLower = item.categoria.toLowerCase();
                        if (categoriaLower.includes('fundamental1c')) elementId = 'contagem-fundamental-i';
                        else if (categoriaLower.includes('fundamental2c')) elementId = 'contagem-fundamental-ii';
                        else if (categoriaLower.includes('Medio') || categoriaLower.includes('medio')) elementId = 'contagem-ensino-medio';
                        else if (categoriaLower.includes('outros')) elementId = 'contagem-outros-geral';
                        // Adicione mais mapeamentos se necessário

                        const el = document.getElementById(elementId);
                        if (el) {
                            el.textContent = item.soma || 0;
                        } else {
                            console.warn(`Elemento com ID "${elementId}" (mapeado de "${item.categoria}") não encontrado no dashboard de contagem.`);
                        }
                    });
                } else {
                    console.error("Formato inesperado dos dados de contagem:", data);
                }
            } catch (error) {
                console.error("Erro ao buscar dados de contagem do dia:", error);
                exibirAlerta('error', 'Erro de Comunicação', 'Falha ao buscar os totais das contagens.');
            }
        }

        /**
         * Exibe a página solicitada e oculta as demais.
         * @param {string} pageIdToShow - ID da página a ser exibida.
         */
        function exibirPagina(pageIdToShow) {
            document.querySelectorAll('.content').forEach(page => {
                page.classList.remove('active');
            });
            const targetPage = document.getElementById(pageIdToShow);
            if (targetPage) {
                targetPage.classList.add('active');
            }

            // Atualiza links ativos na sidebar
            document.querySelectorAll('.sidebar .nav-link, .sidebar .dropdown-item').forEach(link => {
                link.classList.remove('active');
                if (link.dataset.page === pageIdToShow) {
                    link.classList.add('active');
                    // Se for um item de dropdown, ativa também o dropdown-toggle pai
                    if (link.classList.contains('dropdown-item')) {
                        const dropdownToggle = link.closest('.dropdown').querySelector('.dropdown-toggle');
                        if (dropdownToggle) dropdownToggle.classList.add('active');
                    }
                }
            });
            // Fecha o menu mobile (se estiver aberto) ao trocar de página
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay-menu');
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        }

        /**
         * Configura a navegação da página (links da sidebar).
         */
        function configurarNavegacao() {
            document.querySelectorAll('.sidebar .nav-link[data-page], .sidebar .dropdown-item[data-page]').forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const pageId = this.dataset.page;
                    if (pageId) {
                        exibirPagina(pageId);
                    }
                });
            });
        }

        /**
         * Atualiza a data na página Home.
         */
        function atualizarDataHome() {
            const dataAtualElement = document.getElementById('data-atual-home');
            if (dataAtualElement) {
                const hoje = new Date();
                dataAtualElement.textContent = `Data: ${hoje.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })}`;
            }
        }

        /**
         * Configura o menu hamburguer para dispositivos móveis.
         */
        function configurarMenuMobile() {
            const menuToggleButton = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay-menu');

            if (menuToggleButton && sidebar && overlay) {
                menuToggleButton.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        }

        /**
         * Função de inicialização principal da aplicação.
         */
        // Ajuste do script completo para funcionar com os dados do endpoint e exibir tabela corretamente

        // Ajuste do script completo para funcionar com os dados do endpoint e exibir tabela corretamente

        // Ajuste do script completo para funcionar com os dados do endpoint contagens.php

        function inicializarAplicacao() {
            configurarNavegacao();
            configurarMenuMobile();
            atualizarDataHome();

            criarCardsTurma([
                { nome: "1º", id: 1 }, { nome: "2º", id: 2 }, { nome: "3º", id: 3 },
                { nome: "4º", id: 4 }, { nome: "5º", id: 5 }
            ], "cardsContainerFund1", " ANO EF");
            criarCardsTurma([
                { nome: "6º", id: 6 }, { nome: "7º", id: 7 },
                { nome: "8º", id: 8 }, { nome: "9º", id: 9 }
            ], "cardsContainerFund2", " ANO EF");
            criarCardsTurma([
                { nome: "1º", id: 10 }, { nome: "2º", id: 11 }, { nome: "3º", id: 12 }
            ], "cardsContainerEM", " ANO EM");

            carregarTurmasOutros();
            carregarDadosContagemDashboard();
            exibirPagina('pagina-contagem');

            const btnCadastrarOutros = document.getElementById('btnCadastrarNovaCategoriaOutros');
            if (btnCadastrarOutros) {
                btnCadastrarOutros.addEventListener('click', () => {
                    Swal.fire({
                        title: 'Cadastrar Nova Turma',
                        html: `
                    <input id="swal-input-nome-turma" class="swal2-input" placeholder="Nome da Turma">
                `,
                        confirmButtonText: 'Cadastrar',
                        focusConfirm: false,
                        preConfirm: () => {
                            const nome = document.getElementById('swal-input-nome-turma').value;
                            const categoriaId = 5;

                            if (!nome) {
                                Swal.showValidationMessage(`Por favor, preencha o nome da turma.`);
                                return false;
                            }

                            return {
                                nome_turma: nome,
                                categorias_id_categoria: categoriaId
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            fetch('../backend/endpoints/post_turma.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(result.value)
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        exibirAlerta('success', 'Turma cadastrada com sucesso!', `Nome: ${result.value.nome_turma}`);
                                        carregarTurmasOutros();
                                        inicializarAplicacao();

                                    } else {
                                        exibirAlerta('error', 'Erro ao cadastrar turma', data.message || data.error);
                                    }
                                })
                                .catch(error => {
                                    exibirAlerta('error', 'Erro inesperado', 'Não foi possível cadastrar a turma.');
                                });
                        }
                    });
                });
            }


        }
        
        // Script corrigido e funcional para editar contagens via modal SweetAlert2

// Função que abre a tabela, recebendo o elemento clicado
function abrirTabelaEscopo(elemento) {
    const escopo = elemento.getAttribute('data-escopo');

    const categoriasMapeadas = {
        "fundamental-i": ["Fundamental 1 A", "Fundamental 1 B"],
        "fundamental-ii": ["Fundamental 2"],
        "ensino-medio": ["Ensino Médio"],
        "outros": ["Outros"]
    };

    const categoriasAlvo = categoriasMapeadas[escopo];
    if (!categoriasAlvo) {
        Swal.fire("Erro", "Categoria inválida", "error");
        return;
    }

    fetch('../backend/endpoints/contagens_completa.php')
        .then(res => {
            if (!res.ok) throw new Error(`Erro na resposta: ${res.status}`);
            return res.json();
        })
        .then(dados => {
            const hoje = new Date().toISOString().split('T')[0];

            const filtrados = dados.filter(item => {
                const dataItem = item.data_contagem.split('T')[0];
                return dataItem === hoje && categoriasAlvo.includes(item.nome_categoria);
            });

            if (filtrados.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sem dados',
                    text: `Nenhum dado encontrado para a categoria "${escopo}" na data de hoje.`,
                });
                return;
            }

            let tabelaHTML = `
                <table class="table table-bordered">
                    <thead><tr><th>Turma</th><th>Contagem</th><th>Ações</th></tr></thead>
                    <tbody>
                        ${filtrados.map(item => `
                            <tr>
                                <td>${item.nome_turma}</td>
                                <td>${item.qtd_contagem}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick='editarContagem(${JSON.stringify(item)}, "${escopo}")'>
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;

            Swal.fire({
                title: `Contagem de ${escopo.replace('-', ' ').toUpperCase()}`,
                html: tabelaHTML,
                width: 700
            });
        })
        .catch(err => {
            console.error('Erro no fetch:', err);
            Swal.fire('Erro', 'Não foi possível carregar os dados', 'error');
        });
}

// Evento ao carregar a página para registrar os botões de escopo
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-escopo]').forEach(botao => {
        botao.addEventListener('click', () => abrirTabelaEscopo(botao));
    });
});

// Função de edição de contagem
async function editarContagem(item, escopo) {
    console.log("Dados recebidos pela função editarContagem:", item);

    const { value: formValues } = await Swal.fire({
        title: `Editar contagem da Turma: ${item.nome_turma}`,
        html: `<input type="number" id="qtdContagem" class="swal2-input" value="${item.qtd_contagem}" placeholder="Nova contagem">`,
        confirmButtonText: 'Salvar',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const qtd = document.getElementById('qtdContagem').value;
            if (!qtd || isNaN(qtd) || qtd < 0) {
                Swal.showValidationMessage('Digite um número válido para a contagem');
                return false;
            }
            if (!item.id_contagem) {
                Swal.showValidationMessage('ID da contagem inválido');
                return false;
            }
            return {
                id_contagem: item.id_contagem,
                qtd_contagem: qtd
            };
        }
    });

    if (formValues) {
        console.log("Dados que serão enviados para o backend:", formValues);

        try {
            const response = await fetch('../backend/endpoints/update_contagens.php', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify(formValues)
            });

            const resp = await response.json();

            if (!response.ok || resp.error) {
                throw new Error(resp.error || 'Erro na resposta do servidor');
            }

            await Swal.fire('Sucesso', resp.success, 'success');
            inicializarAplicacao();

            const botao = document.querySelector(`[data-escopo="${escopo}"]`);
            if (botao) abrirTabelaEscopo(botao);
        } catch (err) {
            console.error('Falha ao atualizar:', err);
            Swal.fire('Erro', 'Não foi possível atualizar a contagem.', 'error');
        }
    }
}
       
        document.addEventListener('DOMContentLoaded', inicializarAplicacao);

    </script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/pt-br.js"></script>
    <script>
    dayjs.locale('pt-br');
    const hoje = dayjs();
    const inicio = dayjs('2025-01-01');
    let dataSelecionada = null;

    function gerarCalendario(mesRef) {
      const container = document.getElementById('calendar-container');
      container.innerHTML = '';

      const data = dayjs(mesRef).startOf('month');
      const fimDoMes = data.endOf('month').date();
      const primeiroDiaSemana = data.day();

      const header = document.createElement('div');
      header.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'mb-2');

      const btnAnt = document.createElement('button');
      btnAnt.textContent = '←';
      btnAnt.classList.add('btn', 'btn-outline-secondary');
      btnAnt.onclick = () => {
        const anterior = data.subtract(1, 'month');
        if (anterior.isAfter(inicio.subtract(1, 'day'))) gerarCalendario(anterior);
      };

      const btnProx = document.createElement('button');
      btnProx.textContent = '→';
      btnProx.classList.add('btn', 'btn-outline-secondary');
      btnProx.onclick = () => {
        const proximo = data.add(1, 'month');
        if (proximo.isBefore(hoje.startOf('month').add(1, 'day'))) gerarCalendario(proximo);
      };

      const titulo = document.createElement('h4');
      titulo.classList.add('mb-0');
      titulo.textContent = data.format('MMMM [de] YYYY');

      header.appendChild(btnAnt);
      header.appendChild(titulo);
      header.appendChild(btnProx);
      container.appendChild(header);

      const tabela = document.createElement('table');
      tabela.classList.add('table', 'text-center');
      const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
      const thead = document.createElement('thead');
      const trHead = document.createElement('tr');
      diasSemana.forEach(d => {
        const th = document.createElement('th');
        th.textContent = d;
        trHead.appendChild(th);
      });
      thead.appendChild(trHead);
      tabela.appendChild(thead);

        const tbody = document.createElement('tbody');
        let tr = document.createElement('tr');
        for (let i = 0; i < primeiroDiaSemana; i++) {
          tr.appendChild(document.createElement('td'));
        }

        for (let dia = 1; dia <= fimDoMes; dia++) {
          const td = document.createElement('td');
          td.textContent = dia;
          const dataCompleta = data.date(dia);

          if (dataCompleta.isAfter(hoje, 'day')) {
            td.classList.add('text-muted');
          } else {
            td.style.cursor = 'pointer';
            td.onclick = () => {
              document.querySelectorAll('#calendar-container td').forEach(t => t.classList.remove('bg-danger', 'text-white'));
              td.classList.add('bg-danger', 'text-white');
              dataSelecionada = dataCompleta.format('YYYY-MM-DD');
              document.getElementById('ver-contagens').disabled = false;
            };
          }

          tr.appendChild(td);
          if (tr.children.length === 7) {
            tbody.appendChild(tr);
            tr = document.createElement('tr');
          }
        }

        if (tr.children.length) tbody.appendChild(tr);
        tabela.appendChild(tbody);
        container.appendChild(tabela);
      }

      gerarCalendario(hoje);

      document.getElementById('ver-contagens').addEventListener('click', () => {
        if (!dataSelecionada) return;
        fetch(`../backend/endpoints/get_contagens_data.php?data=${dataSelecionada}`)
          .then(resp => resp.json())
          .then(dados => {
            const container = document.getElementById('resultado-contagens');
            container.innerHTML = '';

            for (const categoria in dados) {
              if (dados[categoria].length === 0) continue;

              const card = document.createElement('div');
              card.classList.add('border', 'border-danger', 'p-3', 'mb-3');

              const titulo = document.createElement('h5');
              titulo.classList.add('text-danger', 'fw-bold');
              titulo.textContent = `${categoria}: ${dados[categoria].reduce((soma, turma) => soma + turma.qtd_contagem, 0)}`;
              card.appendChild(titulo);

              const lista = document.createElement('ul');
              lista.classList.add('list-unstyled');
              dados[categoria].forEach(turma => {
                const item = document.createElement('li');
                item.textContent = `${turma.nome_turma}: ${turma.qtd_contagem}`;
                lista.appendChild(item);
              });

              card.appendChild(lista);
              container.appendChild(card);
            }

            const modal = new bootstrap.Modal(document.getElementById('contagemModal'));
            modal.show();
          });
      });
</script>

</body>

</html>
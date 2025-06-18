<?php
session_start();

if ($_SESSION['nome_user368'] == null) {
    header("Location: index.php");
}

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body style="background-color: #FFFDF7;">
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
            if ($nivelSessao == 1 or $nivelSessao == 2) {
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
            <h1 class="text-center mb-5 display-5 fw-bold text-black">Contagens</h1>
            <div class="row justify-content-center g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 bg-danger text-white rounded-4 border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-between py-4">
                            <div class="bg-light text-danger rounded-4 d-flex align-items-center justify-content-center mb-3"
                                style="width: 100px; height: 100px;">
                                <h2 id="contagem-fundamental-i" class="display-6 fw-bold mb-0">-</h2>
                            </div>
                            <hr class="border-white opacity-50 w-75 my-2">
                            <div class="abrir-contagem bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
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
                            <div class="abrir-contagem bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
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
                            <div class="abrir-contagem bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
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
                            <div class="abrir-contagem bg-light text-danger rounded-pill px-3 py-1 d-inline-flex align-items-center cursor-pointer"
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
                <?php
                if ($_SESSION['nivel_user368'] == 2){
                    echo '<a href="admin/dashboard.php" class="btn btn-outline-danger mb-3"><i class="bi bi-gear"></i> Painel do Desenvolvedor</a>';
                }
                ?>
                <form id="form-logout" action="../backend/endpoints/user_logout.php" method="POST">
                    <input type="hidden" name="token"
                        value="<?php echo htmlspecialchars($tokenSessao, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-door-open"></i> Sair da Conta</button>
                </form>
            </div>
        </div>
    </div>

    <div id="painel-nutricionista" class="content">
        <h1>Painel da Nutricionista</h1>
        <canvas id="graficoTurmas" width="800" height="240"></canvas>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                fetch('../backend/endpoints/contagens_turma.php')
                    .then(response => response.json())
                    .then(data => {


                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        const ordemFundamental = [
                            "1º Ano", "2º Ano", "3º Ano", "4º Ano", "5º Ano",
                            "6º Ano", "7º Ano", "8º Ano", "9º Ano"
                        ];

                        const ordemMedio = [
                            "1º Ano M", "2º Ano M", "3º Ano M"
                        ];

                        function prioridade(turma) {
                            if (ordemFundamental.includes(turma)) return 1;
                            if (ordemMedio.includes(turma)) return 2;
                            return 3;
                        }

                        function comparar(a, b) {
                            const turmaA = a.turma.trim();
                            const turmaB = b.turma.trim();
                            const pA = prioridade(turmaA);
                            const pB = prioridade(turmaB);

                            if (pA !== pB) return pA - pB;

                            if (pA === 1) {
                                return ordemFundamental.indexOf(turmaA) - ordemFundamental.indexOf(turmaB);
                            } else if (pA === 2) {
                                return ordemMedio.indexOf(turmaA) - ordemMedio.indexOf(turmaB);
                            } else {
                                return turmaA.localeCompare(turmaB);
                            }
                        }

                        data.sort(comparar);

                        const xValues = data.map(item => item.turma.trim());
                        const yValues = data.map(item => item.total);

                        new Chart(document.getElementById("graficoTurmas"), {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    label: "Contagens",
                                    backgroundColor: "#ba3636",
                                    data: yValues
                                }]
                            },
                            options: {
                                responsive: true,
                                animation: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: "Contagens por Turma"
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        console.log(data);
                    })
                    .catch(error => {
                        console.error("Erro ao carregar gráfico:", error);
                    });
            });
        </script>

        <div class="d-flex gap-2 mt-1">

            <?php
            $dataAtualFormatadaParaSQL = date('Y-m-d');
            echo '<a href="../backend/reports/gerar_relatorio.php?data=' . $dataAtualFormatadaParaSQL . '" target="_blank">
    <button class="btn btn-danger"><i class="bi bi-filetype-pdf"></i> Gerar Relatório de Hoje</button>
</a>';
            ?>
            <button class="btn btn-danger" onclick="exibirPagina('painel-nutricionista-calendario')">
                <i class="bi bi-calendar4-week"></i> Calendário das Contagens
            </button>
            <button class="btn btn-danger" id="ver-anotacoes" onclick="exibirPagina('painel-nutricionista-anotacoes')">
                <i class="bi bi-journal-text"></i> Diário de Anotações
            </button>
        </div>
    </div>


    <div id="painel-nutricionista-calendario" class="content">
        <h1 class="home-titulo">Calendário das Contagens</h1>
        <div id="calendar-container"></div>

        <div class="d-flex gap-2">
            <button id="ver-contagens" class="btn btn-danger mt-3" disabled>Ver Contagens</button>
            <button class="voltar-btn btn btn-danger mb-3" onclick="exibirPagina('painel-nutricionista')">
                <i class="bi bi-arrow-left"></i> Voltar
            </button>
        </div>

        <div class="modal fade" id="contagemModal" tabindex="-1" aria-labelledby="contagemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contagemModalLabel"><i class="bi bi-card-checklist"></i> Contagens do Dia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body" id="resultado-contagens">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="painel-nutricionista-anotacoes" class="content">
        <h1 class="home-titulo">Diário de Anotações</h1>
        <div class="input-group mb-3">
            <span class="input-group-text" id="icon-busca">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="busca-anotacao" class="form-control" placeholder="Buscar por título" aria-label="Buscar por título" aria-describedby="icon-busca">
        </div>


        <div id="lista-anotacoes" class="mt-3"></div>

        <div class="d-flex gap-2">
            <button class="voltar-btn btn btn-danger mb-3" id="btnRealizarAnotacao">
                <i class="bi bi-stickies"></i> Anotar
            </button>
            <button class="voltar-btn btn btn-danger mb-3" onclick="exibirPagina('painel-nutricionista')">
                <i class="bi bi-arrow-left"></i> Voltar
            </button>
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
            <?php

            if ($_SESSION['nivel_user368'] == 2) {

                echo '
                <div class="text-center mt-4">
                    <button id="btnCadastrarNovaCategoriaOutros" class="btn btn-danger">
                        <i class="bi bi-bookmark-plus"></i> Cadastrar Nova Turma em Outros</button>
                </div>
                
                ';
            }

            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const urlsBackend = {
            postContagens: "../backend/endpoints/post_contagens.php",
            getContagensDia: "../backend/endpoints/contagens_dia.php",
            getTurmasOutros: "../backend/endpoints/turmas.php?categoria=5"
        };

        function exibirAlerta(icon, title, text) {
            Swal.fire({
                icon,
                title,
                text
            });
        }

        function alterarContadorCard(button, delta) {
            const counterElement = button.parentElement.querySelector(".counter");
            if (!counterElement) return;

            let currentValue = parseInt(counterElement.textContent, 10);
            let newValue = currentValue + delta;

            // Garante que o valor fique entre 0 e 32
            newValue = Math.max(0, Math.min(32, newValue));

            counterElement.textContent = newValue;
        }


        window.alterarContadorCard = alterarContadorCard;

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
                    headers: {
                        "Content-Type": "application/json"
                    },
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
        window.confirmarContagem = confirmarContagem;

        function criarCardsTurma(listaTurmas, containerId, sufixoNome = "", estiloPersonalizadoOutros = false) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container com id "${containerId}" não encontrado.`);
                return;
            }
            container.innerHTML = "";

            listaTurmas.forEach(turma => {
                const col = document.createElement("div");
                col.className = "col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-4";

                const cardClasses = estiloPersonalizadoOutros ?
                    "card text-center h-100 shadow-sm rounded-4 card-outros-personalizado" :
                    "card text-center border-danger h-100 shadow-sm rounded-4";

                const nomeExibicao = estiloPersonalizadoOutros ? turma.nome : `${turma.nome}${sufixoNome}`;
                const tituloClasses = estiloPersonalizadoOutros ? "card-title" : "card-title text-danger";
                const hrClasses = estiloPersonalizadoOutros ? "border-white" : "border-danger";
                const btnCounterClasses = estiloPersonalizadoOutros ? "btn btn-light text-danger rounded-circle me-2 btn-counter" : "btn btn-outline-danger rounded-circle me-2 btn-counter";
                const btnConfirmarClasses = estiloPersonalizadoOutros ? "btn btn-light text-danger fw-bold w-100 mt-auto btn-confirmar" : "btn btn-outline-danger w-100 mt-auto btn-confirmar";

                col.innerHTML =
                    `
                    <div class="${cardClasses} bg-danger rounded-4 text-center">
                        <div class="card-body d-flex flex-column justify-content-between p-3 text-white">
                            <h5 class="${tituloClasses} fw-bold text-white">${nomeExibicao}</h5>
                            <hr class="${hrClasses} border-white opacity-50 my-2" />
                            <div class="d-flex align-items-center justify-content-center my-3">
                                <button class="${btnCounterClasses} btn btn-light text-danger rounded-circle fw-bold fs-4 hover-darken"
                                    style="width: 45px; height: 45px;"
                                    onclick="alterarContadorCard(this, -1)">−</button>
                                <span class="fs-4 fw-bold counter mx-3 text-white">32</span>
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

        async function carregarTurmasOutros() {
            try {
                const response = await fetch(urlsBackend.getTurmasOutros);
                if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                const data = await response.json();
                const turmasFormatadas = data.map(t => ({
                    nome: t.nome_turma,
                    id: t.id_turma
                }));
                criarCardsTurma(turmasFormatadas, "cardsContainerOutros", "", true);
            } catch (error) {
                console.error("Erro ao buscar dados da categoria 'Outros':", error);
                exibirAlerta('error', 'Erro ao carregar "Outros"', 'Não foi possível carregar as turmas da categoria "Outros".');
                const container = document.getElementById("cardsContainerOutros");
                if (container) container.innerHTML = '<p class="text-danger text-center">Falha ao carregar turmas.</p>';
            }
        }

        async function carregarDadosContagemDashboard() {
            try {
                const response = await fetch(urlsBackend.getContagensDia);
                if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
                const data = await response.json();



                if (Array.isArray(data)) {
                    data.forEach(item => {
                        let elementId = '';
                        const categoriaLower = item.categoria.toLowerCase();
                        if (categoriaLower.includes('fundamental1c')) elementId = 'contagem-fundamental-i';
                        else if (categoriaLower.includes('fundamental2c')) elementId = 'contagem-fundamental-ii';
                        else if (categoriaLower.includes('Medio') || categoriaLower.includes('medio')) elementId = 'contagem-ensino-medio';
                        else if (categoriaLower.includes('outros')) elementId = 'contagem-outros-geral';

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

        function exibirPagina(pageIdToShow) {
            document.querySelectorAll('.content').forEach(page => {
                page.classList.remove('active');
            });
            const targetPage = document.getElementById(pageIdToShow);
            if (targetPage) {
                targetPage.classList.add('active');
            }

            document.querySelectorAll('.sidebar .nav-link, .sidebar .dropdown-item').forEach(link => {
                link.classList.remove('active');
                if (link.dataset.page === pageIdToShow) {
                    link.classList.add('active');
                    if (link.classList.contains('dropdown-item')) {
                        const dropdownToggle = link.closest('.dropdown').querySelector('.dropdown-toggle');
                        if (dropdownToggle) dropdownToggle.classList.add('active');
                    }
                }
            });
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay-menu');
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        }

        function configurarNavegacao() {
            document.querySelectorAll('.sidebar .nav-link[data-page], .sidebar .dropdown-item[data-page]').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const pageId = this.dataset.page;
                    if (pageId) {
                        exibirPagina(pageId);
                    }
                });
            });
        }

        function atualizarDataHome() {
            const dataAtualElement = document.getElementById('data-atual-home');
            if (dataAtualElement) {
                const hoje = new Date();
                dataAtualElement.textContent = `Data: ${hoje.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })}`;
            }
        }

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

        function inicializarAplicacao() {
            configurarNavegacao();
            configurarMenuMobile();
            atualizarDataHome();

            criarCardsTurma([{
                    nome: "1º",
                    id: 1
                }, {
                    nome: "2º",
                    id: 2
                }, {
                    nome: "3º",
                    id: 3
                },
                {
                    nome: "4º",
                    id: 4
                }, {
                    nome: "5º",
                    id: 5
                }
            ], "cardsContainerFund1", " ANO EF");
            criarCardsTurma([{
                    nome: "6º",
                    id: 6
                }, {
                    nome: "7º",
                    id: 7
                },
                {
                    nome: "8º",
                    id: 8
                }, {
                    nome: "9º",
                    id: 9
                }
            ], "cardsContainerFund2", " ANO EF");
            criarCardsTurma([{
                nome: "1º",
                id: 10
            }, {
                nome: "2º",
                id: 11
            }, {
                nome: "3º",
                id: 12
            }], "cardsContainerEM", " ANO EM");

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

            const btnAnotacao = document.getElementById('btnRealizarAnotacao');

            if (btnAnotacao) {
                btnAnotacao.addEventListener('click', () => {
                    const hoje = new Date().toISOString().split('T')[0];
                    Swal.fire({
                        title: 'Nova Anotação',
                        html: `
                <input id="swal-input-titulo" class="swal2-input" placeholder="Título">
                <input id="swal-input-data" type="date" class="swal2-input" placeholder="Data" value="${hoje}">
                <textarea id="swal-input-texto" class="swal2-textarea" placeholder="Digite a anotação..."></textarea>
            `,
                        confirmButtonText: 'Salvar',
                        focusConfirm: false,
                        preConfirm: () => {
                            const titulo = document.getElementById('swal-input-titulo').value;
                            const data = document.getElementById('swal-input-data').value;
                            const texto = document.getElementById('swal-input-texto').value;
                            const nutricionista_id = 1;

                            if (!titulo || !data || !texto) {
                                Swal.showValidationMessage(`Preencha todos os campos.`);
                                return false;
                            }

                            return {
                                titulo: titulo,
                                data: data,
                                texto: texto,
                                nutricionista_id: nutricionista_id
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            fetch('../backend/endpoints/post_anotacao.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(result.value)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        exibirAlerta('success', 'Anotação salva com sucesso!', `Título: ${result.value.titulo}`);
                                        carregarAnotacoes(); // se tiver função para recarregar anotações
                                    } else {
                                        exibirAlerta('error', 'Erro ao salvar anotação', data.message || data.error);
                                    }
                                })
                                .catch(error => {
                                    exibirAlerta('error', 'Erro inesperado', 'Não foi possível salvar a anotação.');
                                });
                        }
                    });
                });
            }


        }

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
                            title: 'Sem contagens',
                            text: `Nenhuma contagem foi cadastrada ainda hoje.`,
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

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-escopo]').forEach(botao => {
                botao.addEventListener('click', () => abrirTabelaEscopo(botao));
            });
        });

        async function editarContagem(item, escopo) {
            console.log("Dados recebidos pela função editarContagem:", item);

            const {
                value: formValues
            } = await Swal.fire({
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

        function carregarAnotacoes() {
            fetch('../backend/endpoints/get_anotacoes.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById("lista-anotacoes");
                    container.innerHTML = "";

                    if (data.error) {
                        container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    if (data.length === 0) {
                        container.innerHTML = `<div class="alert alert-info">Nenhuma anotação encontrada.</div>`;
                        return;
                    }

                    data.forEach(anotacao => {
                        const card = document.createElement("div");
                        card.className = "card mb-2";
                        card.innerHTML = `
                    <div class="card-body">
                        <i class="bi bi-pin-angle-fill pin"></i>                        
                        <h5 class="card-title">${formatarData(anotacao.data)} - ${anotacao.titulo}</h5>
                        <p class="card-text">${anotacao.texto}</p>
                    </div>
                `;
                        container.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error("Erro ao carregar anotações:", error);
                });
        }

        document.getElementById("ver-anotacoes").addEventListener("click", carregarAnotacoes);

        function formatarData(dataBruta) {
            const data = new Date(dataBruta);
            return data.toLocaleDateString('pt-BR');
        }

        document.getElementById("busca-anotacao").addEventListener("input", () => {
            const termo = document.getElementById("busca-anotacao").value.trim();

            fetch(`../backend/endpoints/get_anotacoes_filtro.php?titulo=${encodeURIComponent(termo)}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById("lista-anotacoes");
                    container.innerHTML = "";

                    if (data.error) {
                        container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    if (data.length === 0) {
                        container.innerHTML = `<div class="alert alert-info">Nenhuma anotação encontrada.</div>`;
                        return;
                    }

                    data.forEach(anotacao => {
                        const card = document.createElement("div");
                        card.className = "card mb-2";
                        card.innerHTML = `
                    <div class="card-body anotacao-scroll">
                        <i class="bi bi-pin-angle-fill pin"></i>
                        <h5 class="card-title">${formatarData(anotacao.data)} - ${anotacao.titulo}</h5>
                        <p class="card-text">${anotacao.texto}</p>
                    </div>
                `;
                        container.appendChild(card);
                    });
                });
        });

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

                    let temContagens = false;

                    for (const categoria in dados) {
                        if (dados[categoria].length === 0) continue;
                        temContagens = true;

                        const card = document.createElement('div');
                        card.classList.add('border', 'border-danger', 'p-3', 'mb-3');

                        const titulo = document.createElement('h5');
                        titulo.classList.add('text-danger', 'fw-bold');
                        titulo.textContent = `${categoria} - Total: ${dados[categoria].reduce((soma, turma) => soma + turma.qtd_contagem, 0)}`;
                        card.appendChild(titulo);

                        const lista = document.createElement('ul');
                        lista.classList.add('list-unstyled');
                        dados[categoria].forEach(turma => {
                            const item = document.createElement('li');
                            item.textContent = `${turma.nome_turma}: ${turma.qtd_contagem} aluno(s)`;
                            lista.appendChild(item);
                        });

                        card.appendChild(lista);
                        container.appendChild(card);
                    }

                    if (!temContagens) {
                        const mensagem = document.createElement('p');
                        mensagem.classList.add('text-muted', 'text-center', 'mt-3');
                        mensagem.textContent = 'Nenhuma contagem registrada para esta data.';
                        container.appendChild(mensagem);
                    }

                    const modal = new bootstrap.Modal(document.getElementById('contagemModal'));
                    modal.show();
                })
                .catch(err => {
                    console.error('Erro ao buscar contagens:', err);
                });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>
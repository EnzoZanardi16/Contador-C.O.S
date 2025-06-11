<?php
session_start();
// O require do db.php deve instanciar a variável $conn
require '../backend/config/db.php'; // Garanta que $conn seja inicializada aqui.

// Configurações de exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Variáveis de sessão e data
$dataAtualFormatadaParaSQL = date('Y-m-d');
$nomeUsuario = $_SESSION['nome_user368'] ?? 'Usuário'; // Define um nome padrão
$nomeUsuarioFormatado = mb_convert_case($nomeUsuario, MB_CASE_TITLE, "UTF-8");
$tokenSessao = $_SESSION['token'] ?? '';

// Função para buscar a soma da contagem por categoria (exemplo, se fosse usada no PHP)
// Atualmente, a lógica principal de contagem é carregada via JS por contagens_dia.php
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

// Exemplo de como poderia ser usado se os dados fossem pré-carregados pelo PHP:
// $contagemFund1PHP = buscarSomaContagemPorCategoriaPHP($conn, 1, $dataAtualFormatadaParaSQL) +
//                     buscarSomaContagemPorCategoriaPHP($conn, 2, $dataAtualFormatadaParaSQL) +
//                     buscarSomaContagemPorCategoriaPHP($conn, 3, $dataAtualFormatadaParaSQL);
// $contagemFund2PHP = buscarSomaContagemPorCategoriaPHP($conn, 4, $dataAtualFormatadaParaSQL);
// $contagemMedioPHP = buscarSomaContagemPorCategoriaPHP($conn, 5, $dataAtualFormatadaParaSQL);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Alunos SESI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: sans-serif;
        }

        .sidebar {
            width: 280px;
            background-color: #BB2A2A;
            color: white;
            padding: 20px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transition: transform 0.3s ease;
            /* Para menu responsivo */
        }

        .sidebar h2 {
            color: white;
            font-size: 1.8em;
            /* Ajustado para caber melhor com a imagem */
            margin-bottom: 15px;
            /* Reduzido */
            text-align: left;
            font-weight: bold;
        }

        .sidebar .sidebar-header {
            /* Novo container para logo e título */
            text-align: center;
            margin-bottom: 20px;
            /* Espaço abaixo do header */
            width: 100%;
        }

        .sidebar .sidebar-header img {
            max-width: 120px;
            /* Ajuste o tamanho do logo conforme necessário */
            margin-bottom: 10px;
        }


        .sidebar .nav-item {
            margin-bottom: 15px;
            width: 100%;
        }

        .sidebar .nav-link {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: background-color 0.2s ease-in-out;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            /* Estilo para link ativo */
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .sidebar .dropdown-toggle::after {
            border: none;
            content: '';
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid white;
            margin-left: 5px;
            vertical-align: middle;
        }

        .sidebar .dropdown-menu {
            background-color: #BB2A2A;
            /* Tom mais claro do vermelho da sidebar */
            border: none;
            padding: 0;
            margin-top: 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .sidebar .dropdown-item {
            color: white;
            /* Texto branco para melhor contraste */
            padding: 10px 20px;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s ease-in-out;
        }

        .sidebar .dropdown-item:hover,
        .sidebar .dropdown-item.active {
            /* Estilo para item de dropdown ativo */
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar .dropdown-item i {
            margin-right: 8px;
        }

        .content {
            flex: 1;
            padding: 30px;
            display: none;
            /* Todas as páginas começam ocultas */
            flex-direction: column;
            align-items: center;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .content.active {
            /* Página ativa é exibida */
            display: flex;
            opacity: 1;
        }


        .content h1 {
            color: #333;
            font-size: 2.5em;
            /* Ajustado */
            margin-bottom: 30px;
            text-align: center;
        }

        #home h1.home-titulo {
            /* Título específico da home */
            font-size: 2.8em;
        }

        .info-container {
            background-color: white;
            padding: 25px;
            /* Aumentado */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            /* Sombra mais suave */
            text-align: left;
            margin-bottom: 25px;
            /* Aumentado */
            width: 100%;
            /* Ajustado */
            max-width: 850px;
            /* Ajustado */
        }

        .info-container p {
            color: #555;
            line-height: 1.7;
            /* Melhorado */
            margin-bottom: 12px;
            /* Ajustado */
            font-size: 1.1em;
            /* Ajustado */
        }

        .info-container p:first-child {
            font-weight: bold;
            color: #333;
        }

        .contact-container {
            text-align: left;
            width: 100%;
            /* Ajustado */
            max-width: 850px;
            /* Ajustado */
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            /* Aumentado */
        }

        .contact-item i {
            color: #BB2A2A;
            margin-right: 12px;
            /* Aumentado */
            font-size: 1.5em;
            /* Ajustado */
        }

        .contact-item span {
            font-size: 1.1em;
            /* Ajustado */
            color: #333;
        }

        .contact-item a {
            /* Estilo para links nos contatos */
            text-decoration: none;
            color: inherit;
            /* Herda a cor do span */
            display: contents;
            /* Para que o <a> não quebre o flex */
        }

        .contact-item a:hover span {
            color: #BB2A2A;
            /* Muda cor do texto no hover */
        }


        .line-titulo {
            width: 150px;
            /* Ajustado */
            height: 3px;
            /* Ajustado */
            background-color: #BB2A2A;
            /* Cor principal */
            margin-bottom: 30px;
            border-radius: 2px;
        }

        /* Estilos para página de Contagem (cards de resumo) */
        #pagina-contagem .card {
            min-height: 220px;
            /* Ajustado */
        }

        #pagina-contagem .card h2 {
            font-size: 2.8em;
            /* Tamanho do número da contagem */
        }

        #pagina-contagem .card p {
            font-size: 1em;
            /* Tamanho do texto da categoria */
        }

        #pagina-contagem .card i {
            font-size: 1.2em;
        }


        /* Estilos para cards de turmas (Fundamental I, II, Médio, Outros) */
        .cards-turmas-container .card {
            background-color: #fff;
            border: 1px solid #ddd;
            /* Borda sutil */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .cards-turmas-container .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .cards-turmas-container .card-title {
            font-size: 1.1em;
            font-weight: bold;
            color: #BB2A2A;
        }

        .cards-turmas-container .counter {
            font-size: 2em;
            /* Tamanho do contador */
            font-weight: bold;
            color: #333;
        }

        .cards-turmas-container .btn-counter {
            font-size: 1.2em;
            padding: 0.3em 0.6em;
        }

        .cards-turmas-container .btn-confirmar {
            font-weight: bold;
        }

        /* Estilo para cards "Outros" personalizados */
        .cards-turmas-container .card.card-outros-personalizado {
            background-color: #BB2A2A;
            /* Cor principal */
            color: white;
            border: none;
        }

        .cards-turmas-container .card.card-outros-personalizado .card-title {
            color: white;
        }

        .cards-turmas-container .card.card-outros-personalizado hr {
            border-top: 1px solid rgba(255, 255, 255, 0.5);
        }

        .cards-turmas-container .card.card-outros-personalizado .counter {
            color: white;
        }

        .cards-turmas-container .card.card-outros-personalizado .btn-counter,
        .cards-turmas-container .card.card-outros-personalizado .btn-confirmar {
            background-color: white;
            color: #BB2A2A;
            border-color: white;
        }

        .cards-turmas-container .card.card-outros-personalizado .btn-counter:hover,
        .cards-turmas-container .card.card-outros-personalizado .btn-confirmar:hover {
            background-color: #f0f0f0;
            color: #BB2A2A;
        }


        /* Card do Usuário */
        #pagina-usuario .card-user-container {
            /* Container para centralizar */
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 70vh;
            /* Para ocupar mais espaço vertical */
        }

        #pagina-usuario .card {
            max-width: 450px;
            /* Ajustado */
            width: 100%;
        }

        #pagina-usuario .card img {
            max-width: 80px;
            /* Imagem menor */
            margin-right: 15px;
        }

        #pagina-usuario .card h1.h4 {
            /* Nome do usuário */
            font-size: 1.5em;
        }


        .menu-toggle {
            display: none;
            /* Oculto por padrão em telas maiores */
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1101;
            /* Acima da sidebar */
            background: #BB2A2A;
            /* Cor de fundo */
            color: white;
            /* Cor do ícone */
            border: none;
            cursor: pointer;
            font-size: 1.8rem;
            /* Tamanho do ícone */
            padding: 0.5rem 0.8rem;
            /* Padding */
            border-radius: 5px;
            /* Bordas arredondadas */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .overlay-menu {
            /* Para escurecer o fundo quando o menu mobile estiver aberto */
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1099;
            /* Abaixo da sidebar, mas acima do conteúdo */
        }

        .overlay-menu.active {
            display: block;
        }

        .hover-darken:hover {
            background-color: #e0e0e0 !important;
            /* cinza claro escuro */
        }


        @media (max-width: 768px) {
            body {
                flex-direction: column;
                /* Empilha sidebar e content */
            }

            .menu-toggle {
                display: block;
                /* Mostra o botão hamburguer */
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1100;
                transform: translateX(-100%);
                /* Escondido por padrão */
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
            }

            .sidebar.active {
                transform: translateX(0);
                /* Mostra a sidebar */
            }

            .content {
                padding: 20px;
                /* Padding menor em mobile */
                margin-left: 0;
                /* Conteúdo ocupa toda a largura */
            }

            .content h1 {
                font-size: 2em;
            }

            #home h1.home-titulo {
                font-size: 2.2em;
            }

            .info-container,
            .contact-container {
                max-width: 100%;
                padding: 20px;
            }

            .info-container p,
            .contact-item span {
                font-size: 1em;
            }

            #pagina-contagem .card {
                min-height: 180px;
            }

            #pagina-contagem .card h2 {
                font-size: 2.2em;
            }

            .cards-turmas-container .col-12 {
                /* Garante que os cards ocupem mais espaço em mobile */
                flex: 0 0 auto;
                width: 80%;
                /* Ocupa 80% para centralizar melhor */
            }

            .cards-turmas-container .row>* {
                /* Centraliza os cards */
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>
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
</body>

</html>
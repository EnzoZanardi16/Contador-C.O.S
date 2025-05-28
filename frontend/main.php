<?php
session_start();
require '../backend/config/db.php';

$date = date('Y-m-d');
$username = $_SESSION['nome_user368'];
// Função para buscar a soma da contagem por categoria
function buscarContagem($conn, $categoria, $data)
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

$contagem1 = buscarContagem($conn, 1, $date) + buscarContagem($conn, 2, $date) + buscarContagem($conn, 3, $date);
$contagem2 = buscarContagem($conn, 4, $date);
$contagem3 = buscarContagem($conn, 5, $date);
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Alunos SESI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: sans-serif;

        }

        .sidebar {
            width: 280px;
            background-color: #b32424;
            /* Vermelho mais escuro */
            color: white;
            padding: 20px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar h2 {
            color: white;
            font-size: 2.8em;
            /* Maior */
            margin-bottom: 30px;
            text-align: left;
            font-weight: bold;
        }

        .sidebar .nav-item {
            margin-bottom: 15px;
            /* Maior espaçamento */
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

        .sidebar .nav-link:hover {
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
            /* Triângulo branco */
            margin-left: 5px;
            vertical-align: middle;
        }

        .sidebar .dropdown-menu {
            background-color: #f8d7da;
            /* Fundo mais claro para o dropdown */
            border: none;
            padding: 0;
            margin-top: 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
            /* Ocupar a largura total */
        }

        .sidebar .dropdown-item {
            color: #333;
            padding: 10px 20px;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s ease-in-out;
        }

        .sidebar .dropdown-item:hover {
            background-color: #e9ecef;
        }

        .content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .content h1 {
            color: #333;
            font-size: 3em;
            /* Maior */
            margin-bottom: 30px;
            text-align: center;
        }

        .info-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            text-align: left;
            margin-bottom: 20px;
            width: 80%;
            max-width: 900px;
        }

        .info-container p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
            font-size: 1.4em;
            /* Fonte um pouco menor */
        }

        .contact-container {
            text-align: left;
            width: 80%;
            max-width: 900px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .contact-item i {
            color: #b32424;
            /* Vermelho mais escuro para os ícones */
            margin-right: 10px;
            font-size: 1.8em;
        }

        .contact-item span {
            font-size: 1.4em;
            color: #333;
            /* Texto preto */
        }

        /* Estilos para as páginas ocultas inicialmente */
        .content[id="contagem"],
        .content[id="usuario"],
        .content[id="configuracoes"] {
            display: none;
            opacity: 0;
            animation: none;
        }

        .content[id="home"] {
            display: flex;
            flex-direction: column;
            align-items: center;
            opacity: 1;
        }

        #home .info-container p:first-child {
            margin-bottom: 20px;
            /* Espaçamento para a data */
        }

        .line-titulo {
            width: 200px;
            height: 2px;
            background-color: red;
            margin-bottom: 30px;
        }

        .contagens {
            width: 900px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .contagens .card {
            width: 290px;
            height: 400px;
            text-align: center;
            font-size: 1.8em;
        }

        .contagens .card .categorias {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2em;
        }
        .menu-toggle {
  display: none;
}

        /* Estilos gerais para desktop já definidos no seu CSS */

/* Mobile - telas até 768px */
@media (max-width: 768px) {

  /* Mostrar o botão hamburguer */
  .menu-toggle {
    display: block;
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1050; /* acima do conteúdo */
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 2rem;
  }

  /* Sidebar fica escondida (fora da tela à esquerda) */
  .sidebar {
    transform: translateX(-100%);
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #BB2A2A; /* fundo com leve transparência */
    box-shadow: 2px 0 12px rgba(0,0,0,0.5);
    z-index: 1100;
    overflow-y: auto;
    transition: transform 0.3s ease;
  }
  .sidebar h2{
    font-size:2;
  }

  /* Quando a sidebar estiver ativa, trazemos ela para dentro da tela */
  .sidebar.active {
    transform: translateX(0);
  }

  /* Conteúdo principal ocupa 100% e sem margem lateral */
  .content {
    margin-left: 0;
    padding: 1rem;
    transition: margin-left 0.3s ease;
  }

  /* Fundo escurecido atrás do menu (overlay) */
  .overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.4);
    z-index: 1050;
    display: none;
  }

  .overlay.active {
    display: block;
  }

  /* >>> NOVO BLOCO: Card do usuário empilhado (layout vertical) <<< */
  .card-usuario {
    display: flex;
    flex-direction: column; /* empilha os itens verticalmente */
    align-items: center;
    text-align: center;
    gap: 10px;
  }

  .card-usuario img,
  .card-usuario svg {
    max-width: 100px;
    height: auto;
  }

  .card-usuario h2 {
    font-size: 1.2em;
    word-break: break-word;
  }
    body {
    display: block;
  }

  .card-usuario {
    width: 100%;
    padding: 1rem;
  }

}


        
    </style>
</head>
<header>
                <button class="menu-toggle" aria-label="Abrir menu">
  &#9776; <!-- Ícone do hambúrguer (três barras) -->
</button>
</header>
<body>


    <div class="sidebar">
<div class="text-center my-4">
    <img src="./assets/C.O.S-white.png"
        alt="C.O.S"
        class="img-fluid rounded-circle mx-auto d-block"
        style="max-width: 150px;">
    <h2 class="mt-3">Bem-vindo</h2>
</div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#" class="nav-link active" onclick="showPage('home')"><i class="bi bi-house-door-fill"></i>
                    Home</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="showPage('contagem')"><i class="bi bi-bar-chart-fill"></i>
                    Contagem</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-mortarboard-fill"></i> Nível de Ensino
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#" onclick="showPage('fundamental1')"><i
                                class="bi bi-mortarboard-fill"></i> Fundamental I</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showPage('fundamental2')"><i
                                class="bi bi-mortarboard-fill"></i> Fundamental II</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showPage('ensinoMedio')"><i
                                class="bi bi-book-fill"></i> Ensino Médio</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showPage('outros')"><i class="bi bi-three-dots"></i>
                            Outros</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="showPage('usuario')">
                    <i class="bi bi-person-fill"></i>
                    <?php
                    $nome = $_SESSION['nome_user368'];
                    $nomeFormatado = mb_convert_case($nome, MB_CASE_TITLE, "UTF-8");
                    echo htmlspecialchars($nomeFormatado, ENT_QUOTES, 'UTF-8');
                    ?>
                </a>
            </li>
     
        </ul>
    </div>

    <div id="home" class="content">

        <h1 class="home-titulo">Realize a contagem agora</h1>
        <span class="line-titulo"></span>
        <div class="info-container">
            <p>Data: 17/10/2024</p>
            <p>Seja bem-vindo ao Contador de Alunos do SESI, uma ferramenta digital desenvolvida para monitorar, em
                tempo real, o número de alunos. Este sistema foi criado com o objetivo de oferecer uma visão clara e
                acessível sobre a quantidade de estudantes no dia, proporcionando praticidade e facilidade de
                acompanhamento tanto para a administração quanto para os membros da equipe da cozinha.</p>
        </div>
        <div class="contact-container">
            <div class="contact-item">
                <a href="https://maps.app.goo.gl/TqAfypWhLpFdygLa9"><i class="bi bi-geo-alt-fill"></i></a>
                <span>SESI - Regente Feijó</span>
            </div>
            <div class="contact-item">
                <i class="bi bi-whatsapp"></i>
                <span>Contato: codingthefuture.cf@gmail.com</span>
            </div>
        </div>
    </div>

<div id="contagem" class="content">
  <div class="container py-5">
    <h1 class="text-center mb-5 display-4 fw-light">Contagens</h1>
    <div class="row justify-content-center g-4">

      <!-- Card 1 (85 - Fundamental I e II) -->
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 h-100 shadow" style="min-height: 250px;">
          <div class="card-body d-flex flex-column justify-content-center py-4">
            <h2 id="fundamental" class="text-center display-3 fw-bold text-dark mb-4">-</h2>
            <hr class="w-50 mx-auto">
            <div class="text-center mt-3">
              <p class="mb-2">
                <i class="bi bi-mortarboard-fill me-2 text-muted"></i> Fundamental I
              </p>
              <p class="mb-0">
                <i class="bi bi-mortarboard-fill me-2 text-muted"></i> Fundamental II
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2 (75 - Ensino Médio) -->
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 h-100 shadow" style="min-height: 250px;">
          <div class="card-body d-flex flex-column justify-content-center py-4">
            <h2 id="medio" class="text-center display-3 fw-bold text-dark mb-4">-</h2>
            <hr class="w-50 mx-auto">
            <div class="text-center mt-3">
              <p class="mb-0">
                <i class="bi bi-book-fill me-2 text-muted"></i> Ensino Médio
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 3 (49 - Outros) -->
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 h-100 shadow" style="min-height: 250px;">
          <div class="card-body d-flex flex-column justify-content-center py-4">
            <h2 id="outros" class="text-center display-3 fw-bold text-dark mb-4">-</h2>
            <hr class="w-50 mx-auto">
            <div class="text-center mt-3">
              <p class="mb-0">
                <i class="bi bi-three-dots me-2 text-muted"></i> Outros
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>



<div id="usuario" class="content">
    <div class="card-user">
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="card p-4 shadow rounded-4" style="max-width: 500px; width: 100%;">
                <div class="d-flex align-items-center mb-4">
                    <img src="./assets/images-removebg-preview.png">
                    <h1 class="h4 mb-0">
                        <?php
                            $nome = $_SESSION['nome_user368'];
                            $nomeFormatado = mb_convert_case($nome, MB_CASE_TITLE, "UTF-8");
                            echo htmlspecialchars($nomeFormatado, ENT_QUOTES, 'UTF-8');
                        ?>
                    </h1>
                </div>
                <form action="../backend/endpoints/user_logout.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
                    <button type="submit" class="btn btn-outline-danger w-100">Sair da Conta</button>
                </form>
            </div>
        </div>
    </div>
</div> <!-- FECHAMENTO CORRETO da div #usuario -->

<!-- A PARTIR DAQUI ESTÁ FORA DA DIV #usuario -->

<div id="fundamental1" class="content">
    <h1>Fundamental I</h1>

    <div class="container">
        <div class="row g-4 justify-content-center" id="cardsContainer1">
            <!-- Cartões serão inseridos aqui via JavaScript -->
        </div>
    </div>
</div>

<script>
  const anos = ["1°", "2°", "3°", "4°", "5°"];
  const container = document.getElementById("cardsContainer1");

  anos.forEach(ano => {
    const card = document.createElement("div");
    card.className = "col-12 col-sm-6 col-md-4 col-lg-2";
    card.innerHTML = `
      <div class="card text-center border-danger">
        <div class="card-body">
          <h5 class="card-title text-danger">${ano} ANO EF</h5>
          <hr class="border-danger" />
          <div class="d-flex align-items-center justify-content-center mb-3">
            <button class="btn btn-outline-danger rounded-circle me-2" onclick="changeCount(this, -1)">−</button>
            <span class="fs-4 fw-bold counter">32</span>
            <button class="btn btn-outline-danger rounded-circle ms-2" onclick="changeCount(this, 1)">+</button>
          </div>
          <button class="btn btn-outline-danger w-100">Confirmar</button>
        </div>
      </div>
    `;
    container.appendChild(card);
  });

  function changeCount(button, delta) {
    const counter = button.parentElement.querySelector(".counter");
    let value = parseInt(counter.textContent, 10);
    value = Math.max(0, value + delta); // impede valor negativo
    counter.textContent = value;
  }
</script>
<!-- ____________________________________________ -->
    <div id="fundamental2" class="content">
  <h1 class="text-center my-4">Fundamental II</h1>
  
  <div class="container">
    <div class="row g-4 justify-content-center" id="cardsContainer2">
      <!-- Cartões serão inseridos aqui via JavaScript -->
    </div>
  </div>
</div>

<script>
  const anosFundamental2 = ["6°", "7°", "8°", "9°"];
  const container2 = document.getElementById("cardsContainer2");

  anosFundamental2.forEach(ano => {
    const card = document.createElement("div");
    card.className = "col-12 col-sm-6 col-md-4 col-lg-2";
    card.innerHTML = `
      <div class="card text-center border border-danger h-100 shadow-sm rounded-4">
        <div class="card-body d-flex flex-column justify-content-between">
          <h5 class="card-title text-danger">${ano} ANO EF</h5>
          <hr class="border-danger" />
          <div class="d-flex align-items-center justify-content-center mb-3">
            <button class="btn btn-outline-danger rounded-circle me-2" onclick="changeCount(this, -1)">−</button>
            <span class="fs-4 fw-bold counter">32</span>
            <button class="btn btn-outline-danger rounded-circle ms-2" onclick="changeCount(this, 1)">+</button>
          </div>
          <button class="btn btn-outline-danger w-100 mt-auto">Confirmar</button>
        </div>
      </div>
    `;
    container2.appendChild(card);
  });

  function changeCount(button, delta) {
    const counter = button.parentElement.querySelector(".counter");
    let value = parseInt(counter.textContent, 10);
    value = Math.max(0, value + delta); // impede valor negativo
    counter.textContent = value;
  }
</script>

    </div>
<div id="ensinoMedio" class="content">
  <h1 class="text-center my-4">Ensino Médio</h1>
  
  <div class="container">
    <div class="row g-4 justify-content-center" id="cardsContainerEM">
      <!-- Cartões serão inseridos aqui via JavaScript -->
    </div>
  </div>
</div>

<script>
  const anosEM = ["1° EM", "2° EM", "3° EM"];
  const containerEM = document.getElementById("cardsContainerEM");

  anosEM.forEach(ano => {
    const card = document.createElement("div");
    card.className = "col-12 col-sm-6 col-md-4 col-lg-2";
    card.innerHTML = `
      <div class="card text-center border border-danger h-100 shadow-sm rounded-4">
        <div class="card-body d-flex flex-column justify-content-between">
          <h5 class="card-title text-danger">${ano}</h5>
          <hr class="border-danger" />
          <div class="d-flex align-items-center justify-content-center mb-3">
            <button class="btn btn-outline-danger rounded-circle me-2" onclick="changeCount(this, -1)">−</button>
            <span class="fs-4 fw-bold counter">32</span>
            <button class="btn btn-outline-danger rounded-circle ms-2" onclick="changeCount(this, 1)">+</button>
          </div>
          <button class="btn btn-outline-danger w-100 mt-auto">Confirmar</button>
        </div>
      </div>
    `;
    containerEM.appendChild(card);
  });

  function changeCount(button, delta) {
    const counter = button.parentElement.querySelector(".counter");
    let value = parseInt(counter.textContent, 10);
    value = Math.max(0, value + delta); // impede valor negativo
    counter.textContent = value;
  }
</script>

<div id="outros" class="content py-4">
  <div class="container text-center">
    <!-- Título -->
    <h1 class="mb-1 text-danger">Outros</h1>
    <hr class="mx-auto mb-4 border-danger" style="width: 200px; border-width: 2px;">

    <!-- Grade de botões em 3 colunas -->
    <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
      <div class="col">
        <a href="senai.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> SENAI
        </a>
      </div>
      <div class="col">
        <a href="personaliza.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> Personaliza
        </a>
      </div>
      <div class="col">
        <a href="topico-avancado.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> Tópico Avançado
        </a>
      </div>
      <div class="col">
        <a href="autorizados.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> Autorizados
        </a>
      </div>
      <div class="col">
        <a href="para-gabaritar.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> Para Gabaritar
        </a>
      </div>
      <div class="col">
        <a href="robotica.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> Robótica
        </a>
      </div>
      <!-- Botão Cadastrar ajustado -->
      <div class="col">
        <a href="Cadastrar.html" class="btn btn-danger w-100 py-3 fs-5 rounded-4 d-flex justify-content-center align-items-center">
          <i class="bi bi-mortarboard-fill me-2"></i> Cadastrar
        </a>
      </div>
    </div>
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
  // Função para trocar de página
  function showPage(pageId) {
    document.querySelectorAll('.content').forEach(page => {
      page.style.display = 'none';
      page.style.opacity = 0;
    });
    const targetPage = document.getElementById(pageId);
    targetPage.style.display = 'flex';
    setTimeout(() => {
      targetPage.style.opacity = 1;
    }, 50);

    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
      link.classList.remove('active');
    });
    const activeLink = document.querySelector(`.sidebar .nav-link[onclick="showPage('${pageId}')"]`);
    if (activeLink && !activeLink.classList.contains('dropdown-toggle')) {
      activeLink.classList.add('active');
    } else if (activeLink && activeLink.classList.contains('dropdown-toggle')) {
      activeLink.classList.add('active');
    }
    document.querySelectorAll('.sidebar .dropdown-item').forEach(item => {
      item.classList.remove('active');
      if (item.getAttribute('onclick') === `showPage('${pageId}')`) {
        item.classList.add('active');
      }
    });
  }

  // Atualiza a data
  const dataHoje = new Date().toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
  const dataAtualElement = document.querySelector('#home .info-container p:first-child');
  if (dataAtualElement) {
    dataAtualElement.textContent = `Data: ${dataHoje}`;
  }

  // Contador (+/-)
  function changeCount(button, delta) {
    const span = button.parentElement.querySelector('.counter');
    let value = parseInt(span.textContent);
    value = Math.max(0, value + delta);
    span.textContent = value;
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Exibe a página padrão
    showPage('contagem');

    // Menu toggle e overlay
    const toggleButton = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);

    toggleButton.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    });

    // Busca dados do backend
    fetch("../backend/endpoints/contagens_dia.php")
      .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        console.log("Dados recebidos:", data);

        if (data.error) {
          console.error("Erro retornado do servidor:", data.error);
          if (data.message) console.error("Detalhes:", data.message);
          return;
        }

        if (Array.isArray(data)) {
          data.forEach(item => {
            const categoria = item.categoria;
            const soma = item.soma;

            const id = categoria.toLowerCase();
            const el = document.getElementById(id);
            if (el) {
              el.textContent = soma;
            } else {
              console.warn(`Elemento com id "${id}" não encontrado.`);
            }
          });
        } else {
          console.error("Formato inesperado dos dados:", data);
        }
      })
      .catch(error => {
        console.error("Erro ao buscar dados:", error);
      });
  });
</script>



</body>

</html>

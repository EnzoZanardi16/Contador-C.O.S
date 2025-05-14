<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Alunos SESI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: sans-serif;
            
        }

        .sidebar {
            width: 280px;
            background-color: #b32424; /* Vermelho mais escuro */
            color: white;
            padding: 20px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar h2 {
            color: white;
            font-size: 2.8em; /* Maior */
            margin-bottom: 30px;
            text-align: left;
            font-weight: bold;
        }

        .sidebar .nav-item {
            margin-bottom: 15px; /* Maior espaçamento */
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
            border-top: 8px solid white; /* Triângulo branco */
            margin-left: 5px;
            vertical-align: middle;
        }

        .sidebar .dropdown-menu {
            background-color: #f8d7da; /* Fundo mais claro para o dropdown */
            border: none;
            padding: 0;
            margin-top: 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%; /* Ocupar a largura total */
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
            font-size: 3em; /* Maior */
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
            font-size: 1.4em; /* Fonte um pouco menor */
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
            color: #b32424; /* Vermelho mais escuro para os ícones */
            margin-right: 10px;
            font-size: 1.8em;
        }

        .contact-item span {
            font-size: 1.4em;
            color: #333; /* Texto preto */
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
            margin-bottom: 20px; /* Espaçamento para a data */
        }
        .line-titulo{
            width: 200px;
            height: 2px;
            background-color: red;
            margin-bottom: 30px;
        }
        .contagens{
            width: 900px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .contagens .card{
            width: 290px;
            height: 400px;
            text-align: center;
            font-size: 1.8em;
        }
        .contagens .card .categorias{
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2em;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Bem vindo</h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#" class="nav-link active" onclick="showPage('home')"><i class="bi bi-house-door-fill"></i> Home</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="showPage('contagem')"><i class="bi bi-bar-chart-fill"></i> Contagem</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-mortarboard-fill"></i> Nível de Ensino
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#" onclick="showPage('fundamental1')"><i class="bi bi-mortarboard-fill"></i> Fundamental I</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showPage('fundamental2')"><i class="bi bi-mortarboard-fill"></i> Fundamental II</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showPage('ensinoMedio')"><i class="bi bi-book-fill"></i> Ensino Médio</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showPage('outros')"><i class="bi bi-three-dots"></i> Outros</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="showPage('usuario')"><i class="bi bi-person-fill"></i> {Usuário}</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="showPage('configuracoes')"><i class="bi bi-gear-fill"></i> Configurações</a>
            </li>
        </ul>
    </div>

    <div id="home" class="content">
        <h1 class="home-titulo">Realize a contagem agora</h1>
        <span class="line-titulo"></span>
        <div class="info-container">
            <p>Data: 17/10/2024</p>
            <p>Seja bem-vindo ao Contador de Alunos do SESI, uma ferramenta digital desenvolvida para monitorar, em tempo real, o número de alunos. Este sistema foi criado com o objetivo de oferecer uma visão clara e acessível sobre a quantidade de estudantes no dia, proporcionando praticidade e facilidade de acompanhamento tanto para a administração quanto para os membros da equipe da cozinha.</p>
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
        <h1>Contagens</h1>
        <div class="contagens">
            <div class="card">
                <p>Contagem:</p>
                <div class="numero">85</div>
                <hr>
                <div class="categorias">
                    <span><i class="bi bi-mortarboard-fill"></i> Fundamental I</span>
                    <span><i class="bi bi-mortarboard-fill"></i> Fundamental II</span>
                </div>
            </div>
            <div class="card">
                <p>Contagem:</p>
                <div class="numero">75</div>
                <hr>
                <div class="categorias">
                    <span><i class="bi bi-book-fill"></i> Ensino Médio</span>
                </div>
            </div>
            <div class="card">
                <p>Contagem:</p>
                <div class="numero">49</div>
                <hr>
                <div class="categorias">
                    <span><i class="bi bi-three-dots"></i> Outros</span>
                </div>
            </div>
        </div>
    </div>

    <div id="usuario" class="content">
        <h1>Usuário</h1>
        <p>Dados do usuário aqui.</p>
    </div>

    <div id="configuracoes" class="content">
        <h1>Configurações</h1>
        <p>Configurações do sistema.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
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

        const dataHoje = new Date().toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
        const dataAtualElement = document.querySelector('#home .info-container p:first-child');
        if (dataAtualElement) {
            dataAtualElement.textContent = `Data: ${dataHoje}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            showPage('home');
        });
    </script>

</body>
</html>
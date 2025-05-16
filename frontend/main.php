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

        
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Bem vindo</h2>
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
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="showPage('configuracoes')"><i class="bi bi-gear-fill"></i>
                    Configurações</a>
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
        <h1>Contagens</h1>
        <div class="contagens">
            <div class="card">
                <p>Contagem:</p>
                <div class="numero">
                    <?php echo $contagem1; ?>
                </div>
                <hr>
                <div class="categorias">
                    <span><i class="bi bi-mortarboard-fill"></i> Fundamental I</span>
                    <span><i class="bi bi-mortarboard-fill"></i> Fundamental II</span>
                </div>
            </div>
            <div class="card">
                <p>Contagem:</p>
                <div class="numero">
                    <?php echo $contagem2; ?>
                </div>
                <hr>
                <div class="categorias">
                    <span><i class="bi bi-book-fill"></i> Ensino Médio</span>
                </div>
            </div>
            <div class="card">
                <p>Contagem:</p>
                <div class="numero">
                    <?php echo $contagem3; ?>
                </div>
                <hr>
                <div class="categorias">
                    <span><i class="bi bi-three-dots"></i> Outros</span>
                </div>
            </div>
        </div>
    </div>

    <div id="usuario" class="content">
        <div class="card-user">
            
 <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow rounded-4" style="max-width: 500px; width: 100%;">
        <div class="d-flex align-items-center mb-4">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAclBMVEX///8AAAD7+/vg4OD19fXp6enBwcHc3NyGhoby8vK1tbUoKCi+vr4fHx+mpqbt7e2Ojo5qampQUFB/f3+enp7T09MrKysVFRVxcXEwMDA4ODitra1KSkqWlpZjY2M/Pz/Ly8sYGBhcXFx2dnZOTk4NDQ233REeAAAJ20lEQVR4nO2diXqyPBOGK5sIAqJQrYhGred/iv/fvdWQ7ZkkvN/FfQCGQTL7JE9PExMTExMTExMTExP/GYJFHLbJMuu6suuyZdKG8SLw/VA0RPOkTOv1vrjN/nIr9us6LZN55PsRzYnabnMoZjKKw6Zr/z0xF6w/5lLhfsiPPVv4fmh1wrLWke5byroMfT+6CmFzMpDui1MzciGr7AiI98Exq3yLMUibPsPyvfGctr5F4RF0axLxPlh3Y7OWcWOiW0TkTexbqF/Eqdzs6VOkY5GxWtmQ713G1RiUzqLfWpLvjW3v2w+ISur9d09eevXoloh1V+W09CZfVTuQ743a03bM7gMie9wyD/LFO2fyvbFzbjmSF6cCzmYviVP5gpVj+d5YOXTkwoMHAWezg7PIamnbBg6RO7IbjSf53mgcyLdwq0Pv2Vn34mI/W/CHg2WzMXdtJB55mdsUkNmMI1TZMnsCJr6F+8Sa8V/6luwbS1ZjPAJaEnFMAloRcSx78Avyvch8S/QAoxUwPPsW6IEzqR9e7X3Lw2FPmNuILr6l4XKhy8L5dbaH2VEJ6DNcEkMUTI3LEP6FxCyGviJ6FXIChRr4DgjFHPD0lI+smg4rVMCxOWuPgO5b7D+ml/GCpTXGagl/A1nFzPfTKwGUbSp31SWEm7mD6qo+iFKbCmjFmbGSrTN0bSLSEnZx2TRsHsZVHM5Zs7mQ9m+czKKMku4JXnt2v1cq1r/SLVCaCLig8kfztOW7VkGbkq1hUs7oadbeC7tFopIoe9DrC1iRqISXTOYZBxmJ27TVtxgkHneqsm6VUiyl7YHHBLouZ4qLMYLtWOi6pwTv9aL+4VQEqa5UT0CCv1BvRfyNav6JePJJd1/g+14rLRXAG0PfBMMORq6T0OjQ1UzSfPB302kshjalmyVP0A91rb5UCy51MUuABahGVR9iABXbs2lEWoHzGsrqG12IGQoIVymVXy2YndkYC/j0tMGWVs3YYLNLxt/oG+Dnc1RbJYQW0dLZj4B2Sq2Mgdklw4TCF2DqRM0OY2sY5RN+gbk2J5UlsI/0jFaeozO0vspnir1ERJF+gKlTlU8I06T4bCTmUClo0wqKDE94wTKA9EAht1WYWwHXK59QB5xJfx9LIlJ0m2FFWXlaEdqGZ4om7PiMPIJ0I0ZQdL+mGGsJoOg0l5krTJNdCQR8erpCzyDT5phfaJBb54CpAplfjNlbmjFBLHqT+RxYfxDNWQjYTjmIfzzCMsE0wx5z6BkKsarBflwxPJMBBqji14xZ2xvNTFKMtYCIvQ4w8TyK/1AcXoB5xDHsQ0lOEeygYSQSgjlFcXcNmM4fgz0UJ/cDsG9gDD7NbC9yjhdgYXQMfumsEHWegHp69koSW4CdREKbBerp2ZYkPkT7XEQ2Cy2rkUwHwA2DIu8Y7urWbIjgAjctiJwa+PUJ9ZgaqD4Xf0h41zOeisKnA0RWGe5QMG/X/QZvTBZF+biEM1SbxvgjiCQkaJpF3RqCpk9RcEHwHz5jR3MsCA6XtPyVggVEitZrkYQUEyTabZC/oWj6FOpSkvkDZEqHZApJZA9pJtXMo0SaKSSRTYb90ne2pumakGbcROSXorHFJ2uzYn5EdIqt6AWj8eEXRxP3NMBPWX5HGB+iMf43Jg0LYMfXN8IYH/frv9B3bYgmWGTxDd15zrqRIsnQxTviRlrCicNaR91ElAs7epOz2au60QgJx9gkXw/hRN5sdlY1/dmZclmxY0w8fn9U+RtDIivxhTjNABZFHtg2sh6lqqGemxWXh8AaMIfnlcgAxyuaywZ+IakBg3V8LttdMjBDmuwszD1L6vhkjsVf8msW/pUyCLOrnWNhZO4URZTP51SvuoTNwzlLulVt76hzWT8NTfzkE1nHC9bXNgKkfW1gi7B/5E3CZC6+J+RBzfgOSNSDSSUkO0rBDyqHKxDEMadrk7Eka3Zqhwk87/osYVlzJTAhKpUhOLy4/hyDESU7Webntku+X3vQgk0Kahl3MN+2u3N840b0R77c33k0B3PCSjEp8q3UHHsbZQMXzeV1xjFeLbJNlOaegNm1NRv4yWqZ3t2EWBzS5VBgxcyzRWqza8afaSNMckXzpGv69Jr2TSe5uTIwfsmKiRMzt0YpnlfFMO5XnCE1qo/csNHRRzqT9LtqYshgGPdEf59fq6/x1EeQtXOKOxsXMlXahkM9C60bJNI0XT6iGwVofEha6npr77qpTCuPo3EuhlYu48zsSPcOO2s8iY6y0zifZm/3iqJQvRqmdT6Nul9zsH2zzUI5v6l3Jo5q18fJ/nVosaLV0O1yUTMYuYv73mK1LaNbsFT6EylO01ZA6URx/UYlhYnqs6uLiduz/GH0p8gVzk10d1+fvOhncG6i3KFAz/jQQZpaMXGrZEk3isMF1JFsGqPzSyXv7UItgwTxOWBm35PwMBxHavQHoUI1PfZH1Ivp/o5eK08znPWimBzRZdgJMR8QGDyTHTwMyozBXQOcyT6YsXF7c+0XQ1YRik/5iQSyO3pG8DTc+y1ob8vSgHtzGHi/BffLcOnM/IVnouEdw3EmbCWe5HA8Sdy14t0V5OtP5PyFBHcFcZ0JH5e5czU7jWvFcyYYxQ9rwuswIHKteGmpcXhtZJdY8+yQ6w+V53zQ2WVuRs+tuuHZCcpMJtfwuzQavIQDaur/Mudlbezfc/4J9z73LfH93NxWqYOjbCI37c2ol+E69mcXKnV55i1tIbzhx9grirM+RAT8HJTDa6vXlmtP/FKm06vHC5tf6pJfXLC25ECQfbVRxX+jGmhzs5hiYGfuirfSxm4MSn6a6MwsLPbNUEl2sN/LnKHeL8tF5+FbfWpaAzwfymRq3EBkSDTU5XJL6byoOB3KY+5c5DGHa/zCESd14uEyDFm4JGY5WEIoVvi3Ol8Nlp9zZ1Ep31H8oB4Y41IjSAT9s47c4I8HEZXzXnrjMxV6Uce0dQfxL4mwDf/ShbqPE4SdsET44rySEIs7B2/rFVMPHxdstRb3k+5c9LXck8l6XPNL08qVe9Q2F1k7yc1P+vKpUuiqv502WRvy5YzCNtucFHqBa+tWfpClWkfWrTgdN32ZJYy1bctYkpX95ngq1BqdT+4zl7+ISttjUrnwplYXLHortzR/su1dZbtEVMNOCEix8rcB/xKnNmQsCF15nLih3o/5/cyXd4KO7myb/0fUnVsXTZE2pTkC4jl11dapT5Xho+DHbCzqZYCwQeYXT42vPg8twvJooluLY/lPiPdBxfqjjnbNjz0b+cfJIWq7zUH+ZxaHTacQgoyWaJ6Uab3eP3jZt2K/rtNSMk367xAs4rBNllnXlV2XLZM2jBejtHgTExMTExMTExMTE2b8D1JWpcJHIHUeAAAAAElFTkSuQmCC" class="rounded-circle me-3 border" alt="Foto de Perfil">
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

    <div id="fundamental1" class="content">
        <h1>Fundamental I</h1>
        <p>
            <div class="container">
    <div class="row g-4 justify-content-center">

      <!-- Cartões -->
      <script>
        const anos = ["1°", "2°", "3°", "4°", "5°"];
        document.write(anos.map(ano => `
          <div class="col-12 col-sm-6 col-md-4 col-lg-2">
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
          </div>
        `).join(''));
      </script>

    </div>
  </div>

        </p>
    </div>
    <div id="fundamental2" class="content">
        <h1>Fundamental II</h1>
        <p></p>
    </div>
    <div id="ensinoMedio" class="content">
        <h1>Ensino Médio</h1>
        <p></p>
    </div>
    <div id="outros" class="content">
        <h1>Outros</h1>
        <p></p>
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
    <script>
    function changeCount(button, delta) {
      const span = button.parentElement.querySelector('.counter');
      let value = parseInt(span.textContent);
      value = Math.max(0, value + delta);
      span.textContent = value;
    }
  </script>
</body>

</html>
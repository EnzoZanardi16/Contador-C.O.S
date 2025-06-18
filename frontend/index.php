<?php

session_start();
if (isset($_SESSION['nome_user368'])) {
  header("Location: main.php");
}



?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Culinária Otimizada</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      background-image: url('../public/images/Culinária Otimizada.png');
      background-size: cover;
      background-position: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    @media (max-width: 768px) {
      body {
        background-image: none;
        /* remove a imagem */
        background-color: #B91C1C;
        /* aplica a cor */
      }
    }


    .titulo-topo {
      font-size: 36px;
      color: #FFF9EB;
      margin-bottom: 10px;
      z-index: 1;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .card {
      z-index: 1;
      background-color: #FFF9EB;
      padding: 1px 30px 40px;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      width: 400px;
      max-width: 90%;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: -1%;
    }

    .logo {
      width: 100px;
      margin-bottom: 15px;
    }

    .titulo-card {
      text-align: center;
      font-size: 32px;
      color: #B91C1C;
      margin-bottom: 30px;
    }

    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    fieldset {
      border: 3px solid #B91C1C;
      border-radius: 20px;
      padding: 10px 20px;
    }

    legend {
      padding: 0 10px;
      font-size: 18px;
      color: #B91C1C;
    }

    input[type="text"],
    input[type="password"] {
      border: none;
      background: transparent;
      width: 100%;
      height: 40px;
      font-size: 18px;
      color: #333;
    }

    input:focus {
      outline: none;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }

    input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: #B91C1C;
      cursor: pointer;
    }

    label {
      font-size: 14px;
      color: #B91C1C;
    }

    button {
      margin-top: 20px;
      padding: 15px 30px;
      font-size: 18px;
      background-color: #B91C1C;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #a31414;
    }
  </style>
</head>

<body class="aturmaa">

  <!-- Título fora do card -->
  <div class="titulo-topo">
    <img src="../public/images/C.O.S-white.png" alt="Logo" class="logo" />
    Culinária
  </div>

  <!-- Card com "Otimizada" e o formulário -->
  <div class="card">
    <div class="titulo-card">Otimizada</div>

    <form action="../backend/endpoints/user_login.php" method="POST">
      <fieldset>
        <legend>Usuário</legend>
        <input type="text" name="nome_user368" required />
      </fieldset>

      <fieldset>
        <legend>Senha</legend>
        <input type="password" name="senha_user368" required />
      </fieldset>

      <div class="checkbox-container">
        <input type="checkbox" id="lembrar" name="lembrar" />
        <label for="lembrar">Lembrar minha senha</label>
      </div>

      <button type="submit">Entrar</button>
    </form>
  </div>

  <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: 'error',
          title: 'Erro ao entrar',
          text: 'Usuário ou senha incorretos.',
          confirmButtonColor: '#B91C1C',
          background: '#fff8f0',
          color: '#000'
        });
      });
    </script>
  <?php endif; ?>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
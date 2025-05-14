<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            background-color: #f9f9f9;
        }

        div {
            height: 80%;
            width: 39%;
        }

        div:nth-of-type(1) img {
            height: 100px;
            width: auto;
        }

        div:nth-of-type(1) {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        div:nth-of-type(2) {
            background-color: #FFEBEB;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        form{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            flex-direction: column;
            width: 80%;
        }
        input{
            background: transparent;
            border:none;
            width: 90%;
            height: 50px;
            font-size: 24px;

        }
        input:focus{
            outline: none;
        }
        fieldset{
            width: 100%;
            height: 84px;
            border: #FF3636 3px solid;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        input[type="checkbox"] {
            width: 30px;
            height: 30px;
            appearance: none; 
            border: 2px solid #FF3636; 
            position: relative;
            background-color: white; 
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            background-color: #FF3636; 
            border: 2px solid #FF3636; 
        }

        input[type="checkbox"]:checked::before {
            content: '✔'; 
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: white; 
        }
        legend{
            font-size: 24px;
            margin-left: 30px;
            padding-inline: 12px;
        }
    </style>
</head>

<body>
    <div>
    <img src="../public/images/logo.png" alt="Culinaria Otimizada Logo" class="fade-in">
        <h1 style="font-size: 70px; color: #FF3636; text-align: center;">Culinária<br>Otimizada</h1>
    </div>
    <hr style="border: none; height: 70%;width: 4px;border-radius: 50px; background-color: #FF3636;">
    <div>
        <form action="../backend/endpoints/user_login.php" method="POST">
            <fieldset>
                <legend>Nome</legend>
                <input type="text" name="nome_user368" id="nome_user368">
            </fieldset>
            <fieldset>
                <legend>Senha</legend>
                <input type="password" name="senha_user368" id="senha_user368">
            </fieldset>

            <button style="cursor: pointer; font-size: 30px; border-radius: 10px; color: white; padding-inline: 45px; height: 60px; border: none; background-color: #FF3636;" type="submit">Entrar</button>

        </form>
    </div>
</body>
</html>
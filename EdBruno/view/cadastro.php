<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro - Ed Ensino</title>

    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/cadastro.css?v=2">

</head>

<body>

<?php include 'header.php'; ?>

<?php

//verifica se existe um erro na sessão e exibe o erro
if(isset($_SESSION['Error']))
{
    echo '
    <section style="
        background-color: rgb(220, 53, 69);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
        width: 100%;
        max-width: 400px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    ">
        ' . $_SESSION['Error'] . '
    </section>
    ';

    unset($_SESSION['Error']);
}

?>

<main class="cadastro-pagina">

    <div class="cadastro-card">

        <div class="cadastro-titulo">

            <h1>Criar Conta</h1>

            <p>Preencha os dados para se cadastrar</p>

        </div>

        <hr class="cadastro-divisor">

        <form action="../processamento/processamento.php" method="POST">

            <input type="hidden" name="acao" value="cadastro">

            <div class="campo-grupo">

                <label for="nome">Nome Completo</label>

                <input 
                    type="text"
                    id="nome"
                    name="nome"
                    placeholder="Ex: Joao da Silva"
                    required
                    maxlength="100"
                >

            </div>

            <div class="campo-grupo">

                <label for="email">E-mail</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Ex: joao@email.com"
                    required
                    maxlength="150"
                >

            </div>

            <div class="campo-grupo">

                <label for="cpf">CPF</label>

                <input
                    type="text"
                    id="cpf"
                    name="cpf"
                    placeholder="Ex: 111.111.111-11"
                    required
                    maxlength="14"
                >

            </div>

            <div class="campo-grupo">

                <label for="senha">Senha</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Mínimo 6 caracteres"
                    required
                    minlength="6"
                    maxlength="50"
                >

            </div>

            <div class="campo-grupo">

                <label for="confirmar_senha">Confirmar Senha</label>

                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    placeholder="Repita a senha"
                    required
                    minlength="6"
                    maxlength="50"
                >

            </div>

            <button type="submit" class="btn-cadastrar">

                Cadastrar

            </button>

        </form>

        <div class="cadastro-login-link">

            <p>

                Já tem uma conta?
                <a href="login.php">Faça login</a>

            </p>

        </div>

    </div>

</main>

<?php include 'footer.php'; ?>

</body>
</html>

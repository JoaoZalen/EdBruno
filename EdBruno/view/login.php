<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ED Ensino</title>

    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/login.css">

</head>

<body>

<?php include 'header.php'; ?>

<main class="container-login">

    <section class="login-box">

        <div class="left-login">

            <h1>
                Bem-vindo ao
                <span>ED Ensino</span>
            </h1>

            <p>
                Acesse sua conta para continuar estudando
                Estruturas de Dados.
            </p>

        </div>

        <div class="right-login">

            <form action="../processamento/processamento.php" method="POST">

                <input type="hidden" name="acao" value="login">

                <h2>Login</h2>

                <?php
                if(isset($_SESSION['Error'])){
                    echo "<div class='mensagem-erro'>" . $_SESSION['Error'] . "</div>";
                    unset($_SESSION['Error']);
                }
                ?>

                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" name="inputEmail" placeholder="Digite seu e-mail" required>
                </div>

                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" name="inputSenha" placeholder="Digite sua senha" required>
                </div>

                <button type="submit" class="btn-entrar">
                    Entrar
                </button>

                <a href="cadastro.php" class="cadastro-link">
                    Não possui conta? Cadastre-se
                </a>

            </form>

        </div>

    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
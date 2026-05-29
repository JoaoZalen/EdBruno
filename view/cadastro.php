<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ED Ensino</title>

    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/cadastro.css">

</head>

<body>

<?php include 'header.php'; ?>

<main class="container-cadastro">

    <section class="cadastro-box">

        <div class="left-cadastro">

            <h1>
                Crie sua conta no
                <span>ED Ensino</span>
            </h1>

            <p>
                Cadastre-se para acessar os conteúdos de Estruturas de Dados,
                TAD e listas encadeadas.
            </p>

        </div>

        <div class="right-cadastro">

            <form action="../processamento/processamento.php" method="POST">

                <input type="hidden" name="acao" value="cadastro">

                <h2>Cadastro</h2>

                <?php
                if(isset($_SESSION['Error'])){
                    echo "<div class='mensagem-erro'>" . $_SESSION['Error'] . "</div>";
                    unset($_SESSION['Error']);
                }
                ?>

                <div class="input-group">
                    <label>Nome completo</label>
                    <input type="text" name="nome" placeholder="Digite seu nome" required>
                </div>

                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="Digite seu e-mail" required>
                </div>

                <div class="input-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" placeholder="Digite seu CPF" required maxlength="14">
                </div>

                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="Digite sua senha" required minlength="6">
                </div>

                <div class="input-group">
                    <label>Confirmar senha</label>
                    <input type="password" name="confirmar_senha" placeholder="Repita sua senha" required minlength="6">
                </div>

                <button type="submit" class="btn-cadastrar">
                    Cadastrar
                </button>

                <a href="login.php" class="login-link">
                    Já possui conta? Entrar
                </a>

            </form>

        </div>

    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
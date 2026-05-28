<?php
session_start(); //Inicia a sessão para verificar o login
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Ed Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/login.css?v=2">
</head>
<body>

<?php include 'header.php'; ?>

<main>

<?php
//verifica se existe um erro na sessão e exibe o erro em um cardzinho vermelho, depois de exibir o erro, 
// ele é removido da sessão para não aparecer mais
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
    <h2>Login</h2>

    <?php
        if(isset($_GET['erro']))
        {
            echo '<p>Email ou senha incorretos.</p>';
        }
    ?>

    <form method="POST" action="../processamento/processamento.php">
        <input type="hidden" name="acao" value="login">

        <label>Email</label>
        <input type="email" name="inputEmail" required>

        <label>Senha</label>
        <input type="password" name="inputSenha" required>

        <input type="submit" value="Entrar">
    </form>
        <a href="cadastro.php">Não tem uma conta? Cadastre-se</a>
</main>

<?php include 'footer.php'; ?>

</body>
</html>

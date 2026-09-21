<?php include 'session.php'; require_once("../model/banco.php"); $bd = new banco(); $user = $bd->getUserById((int)$_SESSION['usuario_id']); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>
<main class="painel-container">
    <h1 class="painel-titulo">Perfil</h1>
    <p class="painel-subtitulo">Atualize seus dados, acompanhe moedas e personalize seu avatar.</p>
    <?php if(isset($_SESSION['Error'])) { ?><div class="painel-alerta"><?php echo $_SESSION['Error']; unset($_SESSION['Error']); ?></div><?php } ?>
    <?php if(isset($_SESSION['Sucesso'])) { ?><div class="painel-alerta"><?php echo $_SESSION['Sucesso']; unset($_SESSION['Sucesso']); ?></div><?php } ?>
    <div class="painel-grid">
        <section class="painel-card">
            <div class="avatar-preview"><img src="../imgs/avatar/base_avatar.svg" alt="Avatar base"></div>
            <h2><?php echo $user['nome']; ?></h2>
            <p><?php echo $user['email']; ?></p>
            <p>Moedas: <span class="moeda"><?php echo (int)$user['moedas']; ?></span></p>
            <p><a class="painel-btn" href="inventario.php">Editar avatar</a></p>
        </section>
        <section class="painel-card">
            <h2>Editar dados</h2>
            <form class="painel-form" method="post" action="../processamento/processamento.php">
                <input type="hidden" name="acao" value="perfil">
                <label>Nome</label>
                <input name="nome" value="<?php echo $user['nome']; ?>" required>
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                <label>CPF</label>
                <input name="cpf" value="<?php echo $user['cpf']; ?>" required>
                <button class="painel-btn" type="submit">Salvar dados</button>
            </form>
        </section>
        <section class="painel-card">
            <h2>Trocar senha</h2>
            <form class="painel-form" method="post" action="../processamento/processamento.php">
                <input type="hidden" name="acao" value="trocar_senha">
                <label>Nova senha</label>
                <input type="password" name="senha" minlength="6" required>
                <label>Confirmar senha</label>
                <input type="password" name="confirmar_senha" minlength="6" required>
                <button class="painel-btn" type="submit">Alterar senha</button>
            </form>
        </section>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

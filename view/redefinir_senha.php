<?php session_start(); $token = $_GET['token'] ?? ''; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>
<main class="painel-container">
    <section class="painel-card" style="max-width: 620px; margin: auto;">
        <h1 class="painel-titulo">Redefinir Senha</h1>
        <?php if(isset($_SESSION['Error'])) { ?><div class="painel-alerta"><?php echo $_SESSION['Error']; unset($_SESSION['Error']); ?></div><?php } ?>
        <form class="painel-form" method="post" action="../processamento/processamento.php">
            <input type="hidden" name="acao" value="redefinir_senha">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label>Nova senha</label>
            <input type="password" name="senha" minlength="6" required>
            <label>Confirmar senha</label>
            <input type="password" name="confirmar_senha" minlength="6" required>
            <button class="painel-btn" type="submit">Salvar senha</button>
        </form>
    </section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

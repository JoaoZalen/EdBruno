<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>
<main class="painel-container">
    <section class="painel-card" style="max-width: 620px; margin: auto;">
        <h1 class="painel-titulo">Recuperar Senha</h1>
        <p class="painel-subtitulo">Informe seu e-mail. No modo local, o link seguro aparece aqui para demonstração.</p>
        <?php if(isset($_SESSION['Sucesso'])) { ?><div class="painel-alerta"><?php echo $_SESSION['Sucesso']; unset($_SESSION['Sucesso']); ?></div><?php } ?>
        <?php if(isset($_SESSION['LinkRecuperacao'])) { ?><div class="painel-alerta">Link local: <a href="<?php echo $_SESSION['LinkRecuperacao']; ?>">redefinir senha</a></div><?php unset($_SESSION['LinkRecuperacao']); } ?>
        <form class="painel-form" method="post" action="../processamento/processamento.php">
            <input type="hidden" name="acao" value="recuperar_senha">
            <label>E-mail</label>
            <input type="email" name="email" required>
            <button class="painel-btn" type="submit">Gerar recuperação</button>
        </form>
    </section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

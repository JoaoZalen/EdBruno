<?php include 'session.php'; $resultado = $_SESSION['resultado_quiz'] ?? null; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>
<main class="painel-container">
    <h1 class="painel-titulo">Resultado</h1>
    <?php if (!$resultado) { ?>
        <section class="painel-card"><p>Nenhuma partida recente.</p><a class="painel-btn" href="quiz.php">Jogar</a></section>
    <?php } else { ?>
        <section class="painel-card">
            <div class="item-img">🏆</div>
            <h2><?php echo $resultado['acertos']; ?> acertos de <?php echo count($resultado['perguntas']); ?></h2>
            <p>Moedas recebidas: <span class="moeda"><?php echo $resultado['moedas']; ?></span></p>
            <a class="painel-btn" href="loja.php">Gastar moedas</a>
            <a class="painel-btn" href="quiz.php">Jogar novamente</a>
        </section>
        <div class="painel-grid">
            <?php foreach ($resultado['respostas'] as $resp) { ?>
                <section class="painel-card">
                    <h3><?php echo $resp['correta'] ? '✅ Acertou' : '❌ Errou'; ?></h3>
                    <p><?php echo $resp['enunciado']; ?></p>
                    <div class="painel-alerta"><?php echo $resp['explicacao']; ?></div>
                </section>
            <?php } ?>
        </div>
    <?php } ?>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

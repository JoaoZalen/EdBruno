<?php include 'session.php'; require_once("../model/banco.php"); require_once("habilidades_texto.php"); $bdQuiz = new banco(); $itensEquipados = $bdQuiz->listarItensEquipados((int)$_SESSION['usuario_id']); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>

<main class="painel-container">
    <h1 class="painel-titulo">Quiz Gamificado</h1>
    <p class="painel-subtitulo">Responda perguntas, acumule moedas e use seus ganhos na loja do avatar.</p>
    <?php if(isset($_SESSION['MensagemQuiz'])) { ?><div class="painel-alerta"><?php echo $_SESSION['MensagemQuiz']; unset($_SESSION['MensagemQuiz']); ?></div><?php } ?>

    <?php if (empty($_SESSION['quiz'])) { ?>
        <section class="painel-card">
            <div class="item-img">🧠</div>
            <h2>Partida rápida</h2>
            <p>8 perguntas misturando teoria e código C#. Acertos fáceis valem 5 moedas, médios 10 e difíceis 20. Gabaritar rende bônus de 50 moedas.</p>
            <form method="post" action="../processamento/processamento.php">
                <input type="hidden" name="acao" value="iniciar_quiz">
                <button class="painel-btn" type="submit">Começar desafio</button>
            </form>
        </section>
    <?php } else {
        $quiz = $_SESSION['quiz'];
        $total = count($quiz['perguntas']);
        $atual = (int)$quiz['atual'];
        if ($atual >= $total) { ?>
            <section class="painel-card">
                <div class="item-img">🏁</div>
                <h2>Desafio concluído</h2>
                <p>Você acertou <?php echo $quiz['acertos']; ?> de <?php echo $total; ?> perguntas.</p>
                <p>Moedas acumuladas nesta partida: <span class="moeda"><?php echo $quiz['moedas']; ?></span></p>
                <form method="post" action="../processamento/processamento.php">
                    <input type="hidden" name="acao" value="finalizar_quiz">
                    <button class="painel-btn" type="submit">Receber moedas</button>
                </form>
            </section>
        <?php } else {
            $pergunta = $quiz['perguntas'][$atual];
            $largura = (($atual) / $total) * 100;
        ?>
            <section class="painel-card">
                <p class="moeda"><?php echo $pergunta['tema']; ?> • <?php echo $pergunta['dificuldade']; ?> • +<?php echo $pergunta['moedas']; ?> moedas</p>
                <div class="quiz-progresso"><span style="width: <?php echo $largura; ?>%"></span></div>
                <h2><?php echo $pergunta['enunciado']; ?></h2>
                <?php if(!empty($quiz['tempo_extra'])) { ?><div class="painel-alerta">Tempo extra ativo nesta partida.</div><?php } ?>
                <?php if(!empty($quiz['bonus_moedas_ativo'])) { ?><div class="painel-alerta">Bônus de moedas ativo: +10 moedas em cada acerto.</div><?php } ?>
                <?php if(!empty($quiz['bonus_perfeito_ativo'])) { ?><div class="painel-alerta">Bônus perfeito ativo: +25 moedas extras se gabaritar.</div><?php } ?>
                <?php if(!empty($quiz['animacao_extra'])) { ?><div class="painel-alerta">Efeito visual extra ativo nesta partida.</div><?php } ?>
                <form method="post" action="../processamento/processamento.php">
                    <input type="hidden" name="acao" value="responder_quiz">
                    <?php foreach ($pergunta['alternativas'] as $letra => $texto) { ?>
                        <?php if(!empty($quiz['eliminada']) && $quiz['eliminada'] === $letra) { continue; } ?>
                        <label class="alternativa">
                            <input type="radio" name="resposta" value="<?php echo $letra; ?>" required>
                            <strong><?php echo $letra; ?>)</strong> <?php echo $texto; ?>
                        </label>
                    <?php } ?>
                    <button class="painel-btn" type="submit">Responder</button>
                </form>
            </section>
            <?php unset($_SESSION['quiz']['eliminada']); ?>
            <section class="painel-card">
                <h2>Itens equipados</h2>
                <?php if(!$itensEquipados) { ?>
                    <p>Nenhum item equipado ainda. Vá ao inventário para equipar habilidades.</p>
                    <a class="painel-btn" href="inventario.php">Abrir inventário</a>
                <?php } else { ?>
                    <div class="habilidades-grid">
                        <?php foreach($itensEquipados as $item) { ?>
                            <form class="habilidade-card" method="post" action="../processamento/processamento.php">
                                <input type="hidden" name="acao" value="usar_habilidade">
                                <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                                <img src="../<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>">
                                <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                <span><?php echo htmlspecialchars($item['habilidade']); ?></span>
                                <p class="habilidade-explica"><?php echo htmlspecialchars(explicarHabilidade($item['habilidade'])); ?></p>
                                <button class="painel-btn" type="submit" <?php echo !empty($quiz['habilidades_usadas'][$item['id']]) ? 'disabled' : ''; ?>>
                                    <?php echo !empty($quiz['habilidades_usadas'][$item['id']]) ? 'Usado' : 'Usar'; ?>
                                </button>
                            </form>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
        <?php } ?>
    <?php } ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>

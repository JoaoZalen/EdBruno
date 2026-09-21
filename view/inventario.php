<?php include 'session.php'; require_once("../model/banco.php"); require_once("habilidades_texto.php"); $bd = new banco(); $itens = $bd->listarInventario((int)$_SESSION['usuario_id']); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventário - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>
<main class="painel-container">
    <h1 class="painel-titulo">Inventário</h1>
    <p class="painel-subtitulo">Equipe itens do avatar. Um item equipado por categoria.</p>
    <?php if(isset($_SESSION['MensagemInventario'])) { ?><div class="painel-alerta"><?php echo $_SESSION['MensagemInventario']; unset($_SESSION['MensagemInventario']); ?></div><?php } ?>
    <section class="painel-card">
        <div class="avatar-preview"><img src="../imgs/avatar/base_avatar.svg" alt="Avatar base"></div>
        <p>Seu avatar ganha personalidade conforme você equipa rosto, chapéu/cabelo, roupa e acessório.</p>
    </section>
    <div class="painel-grid">
        <?php if (!$itens) { ?>
            <section class="painel-card"><h2>Inventário vazio</h2><p>Jogue quiz para ganhar moedas e compre itens na loja.</p><a class="painel-btn" href="loja.php">Abrir loja</a></section>
        <?php } ?>
        <?php foreach ($itens as $item) { ?>
            <section class="painel-card">
                <div class="item-img"><img src="../<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>"></div>
                <h2><?php echo $item['nome']; ?></h2>
                <p><?php echo $item['categoria']; ?> • <?php echo $item['raridade']; ?></p>
                <p>Habilidade: <?php echo htmlspecialchars($item['habilidade']); ?></p>
                <p class="habilidade-explica"><?php echo htmlspecialchars(explicarHabilidade($item['habilidade'])); ?></p>
                <p><?php echo $item['equipado'] ? '✅ Equipado' : 'Disponível'; ?></p>
                <form method="post" action="../processamento/processamento.php">
                    <input type="hidden" name="acao" value="equipar_item">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <button class="painel-btn" type="submit">Equipar</button>
                </form>
            </section>
        <?php } ?>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

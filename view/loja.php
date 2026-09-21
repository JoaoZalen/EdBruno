<?php include 'session.php'; require_once("../model/banco.php"); require_once("habilidades_texto.php"); $bd = new banco(); $user = $bd->getUserById((int)$_SESSION['usuario_id']); $itens = $bd->listarItens(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/painel.css?v=2">
</head>
<body>
<?php include 'header.php'; ?>
<main class="painel-container loja-container">
    <section class="loja-balcao">
        <div class="vendedor-card">
            <img src="../imgs/avatar/vendedor.svg" alt="Vendedor da loja">
        </div>
        <div class="loja-dialogo">
            <span class="loja-etiqueta">Loja do Avatar</span>
            <h1>Escolha seu equipamento</h1>
            <p>Separei itens que mudam o visual do avatar e ainda liberam vantagens para as partidas do Quiz. Dá uma olhada no preço, na raridade e na habilidade antes de comprar.</p>
            <div class="loja-saldo">Saldo disponível: <strong><?php echo (int)$user['moedas']; ?> moedas</strong></div>
        </div>
    </section>

    <?php if(isset($_SESSION['MensagemLoja'])) { ?><div class="painel-alerta"><?php echo $_SESSION['MensagemLoja']; unset($_SESSION['MensagemLoja']); ?></div><?php } ?>

    <div class="loja-prateleira">
        <?php foreach ($itens as $item) { ?>
            <section class="painel-card loja-item">
                <div class="loja-item-topo">
                    <div class="item-img"><img src="../<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>"></div>
                    <div>
                        <span class="raridade raridade-<?php echo strtolower($item['raridade']); ?>"><?php echo htmlspecialchars($item['raridade']); ?></span>
                        <h2><?php echo htmlspecialchars($item['nome']); ?></h2>
                    </div>
                </div>
                <p><?php echo htmlspecialchars($item['descricao']); ?></p>
                <div class="loja-tags">
                    <span><?php echo htmlspecialchars($item['categoria']); ?></span>
                    <span><?php echo htmlspecialchars($item['habilidade']); ?></span>
                </div>
                <p class="habilidade-explica"><?php echo htmlspecialchars(explicarHabilidade($item['habilidade'])); ?></p>
                <p class="moeda preco"><?php echo (int)$item['preco']; ?> moedas</p>
                <form method="post" action="../processamento/processamento.php">
                    <input type="hidden" name="acao" value="comprar_item">
                    <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                    <button class="painel-btn" type="submit">Comprar</button>
                </form>
            </section>
        <?php } ?>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

<?php
include 'session.php';
require_once("../model/banco.php");
require_once("habilidades_texto.php");
require_once("avatar.php");

$bd         = new banco();
$usuarioId  = (int)$_SESSION['usuario_id'];
$user       = $bd->getUserById($usuarioId);
$itens      = $bd->listarItens();
$inventario = $bd->listarInventario($usuarioId);
$equipados  = $bd->listarItensEquipados($usuarioId);
$moedas     = (int)$user['moedas'];

$recem = $_SESSION['AvatarRecem'] ?? null;
unset($_SESSION['AvatarRecem']);

// id do item => 'tenho' | 'equipado'
$posse = [];
foreach ($inventario as $inv) {
    $posse[(int)$inv['id']] = (int)$inv['equipado'] === 1 ? 'equipado' : 'tenho';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja — ED Ensino</title>
    <link rel="stylesheet" href="../css/base.css?v=2">
    <link rel="stylesheet" href="../css/header.css?v=5">
    <link rel="stylesheet" href="../css/footer.css?v=3">
    <link rel="stylesheet" href="../css/painel.css?v=4">
</head>
<body>
<?php include 'header.php'; ?>

<main class="painel-container loja-container">

    <section class="loja-balcao">
        <div class="vendedor-card">
            <?php
            // o vendedor usa o mesmo sistema de avatar, já "vestido" com itens da loja
            $roupaVendedor = [];
            foreach (['chapeu_logica', 'oculos_foco', 'roupa_tempo', 'amuleto_moedas'] as $peca) {
                $roupaVendedor[] = ['imagem' => "imgs/avatar/$peca.svg", 'raridade' => 'comum'];
            }
            echo avatarSvg($roupaVendedor, ['titulo' => 'Vendedor da loja']);
            ?>
        </div>
        <div class="loja-dialogo">
            <span class="loja-etiqueta">Loja do Avatar</span>
            <h1>Escolha seu equipamento</h1>
            <p>Passe o mouse em qualquer item para <strong>provar no seu personagem</strong> antes de comprar. Cada peça muda o visual e libera uma vantagem no Quiz.</p>
            <div class="loja-saldo">Saldo disponível: <strong><?php echo $moedas; ?> moedas</strong></div>
        </div>
    </section>

    <?php if (isset($_SESSION['MensagemLoja'])) { ?>
        <div class="painel-alerta"><?php echo htmlspecialchars($_SESSION['MensagemLoja']); unset($_SESSION['MensagemLoja']); ?></div>
    <?php } ?>
    <?php if (isset($_SESSION['MensagemInventario'])) { ?>
        <div class="painel-alerta alerta-sucesso"><?php echo htmlspecialchars($_SESSION['MensagemInventario']); unset($_SESSION['MensagemInventario']); ?></div>
    <?php } ?>

    <div class="vest-grade">

        <!-- ========== PROVADOR ========== -->
        <section class="vest-palco" data-provador>
            <span class="loja-provador-titulo">Provador</span>
            <div class="vest-avatar">
                <?php echo avatarSvg($equipados, ['provador' => $itens, 'recem' => $recem, 'titulo' => 'Seu avatar']); ?>
                <span class="vest-pedestal"></span>
                <span class="vest-aviso" data-provador-aviso></span>
            </div>
            <?php echo avatarSlotsHtml($equipados); ?>
            <a class="vest-mais" href="inventario.php">Abrir o vestiário &rarr;</a>
        </section>

        <!-- ========== PRATELEIRA ========== -->
        <section class="vest-lista loja-prateleira">
            <?php foreach ($itens as $item) {
                $id     = (int)$item['id'];
                $estado = $posse[$id] ?? 'loja';
                $preco  = (int)$item['preco'];
                $falta  = $preco - $moedas;
                $rar    = htmlspecialchars($item['raridade']); ?>

                <article class="item-card raridade-<?php echo $rar; ?> <?php echo $estado === 'equipado' ? 'equipado' : ''; ?>"
                         data-provar="<?php echo chaveDoItem($item); ?>"
                         data-slot="<?php echo slotDoItem($item); ?>"
                         data-nome="<?php echo htmlspecialchars($item['nome']); ?>">

                    <div class="item-card-icone">
                        <img src="../<?php echo htmlspecialchars($item['imagem']); ?>" alt="">
                        <?php if ($estado === 'equipado') { ?><span class="item-card-check" title="Equipado">✓</span><?php } ?>
                    </div>

                    <div class="item-card-corpo">
                        <span class="raridade raridade-<?php echo $rar; ?>"><?php echo $rar; ?></span>
                        <span class="item-card-slot"><?php echo AVATAR_SLOTS[slotDoItem($item)] ?? ''; ?></span>
                        <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                        <p class="item-card-desc"><?php echo htmlspecialchars($item['descricao']); ?></p>
                        <p class="item-card-habilidade"><strong><?php echo htmlspecialchars($item['habilidade']); ?>:</strong>
                            <?php echo htmlspecialchars(explicarHabilidade($item['habilidade'])); ?></p>

                        <div class="item-card-acoes">
                            <?php if ($estado === 'loja') { ?>
                                <span class="moeda preco"><?php echo $preco; ?></span>
                                <form method="post" action="../processamento/processamento.php">
                                    <input type="hidden" name="acao" value="comprar_item">
                                    <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                    <button class="painel-btn" type="submit" <?php echo $falta > 0 ? 'disabled' : ''; ?>>
                                        <?php echo $falta > 0 ? 'Faltam ' . $falta : 'Comprar'; ?>
                                    </button>
                                </form>
                            <?php } elseif ($estado === 'tenho') { ?>
                                <span class="item-card-posse">Já é seu</span>
                                <form method="post" action="../processamento/processamento.php">
                                    <input type="hidden" name="acao" value="equipar_item">
                                    <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="voltar" value="loja">
                                    <button class="painel-btn painel-btn--verde" type="submit">Equipar</button>
                                </form>
                            <?php } else { ?>
                                <span class="item-card-posse item-card-posse--ok">Equipado</span>
                            <?php } ?>
                            <button type="button" class="item-card-provar" data-botao-provar>Provar</button>
                        </div>
                    </div>
                </article>
            <?php } ?>
        </section>

    </div>
</main>

<?php include 'footer.php'; ?>
<script src="../js/avatar.js?v=1"></script>
</body>
</html>

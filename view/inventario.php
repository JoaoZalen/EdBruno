<?php
include 'session.php';
require_once("../model/banco.php");
require_once("habilidades_texto.php");
require_once("avatar.php");

$bd        = new banco();
$usuarioId = (int)$_SESSION['usuario_id'];
$user      = $bd->getUserById($usuarioId);
$itens     = $bd->listarInventario($usuarioId);
$equipados = array_values(array_filter($itens, function ($i) { return (int)$i['equipado'] === 1; }));

// item recém-equipado: o avatar anima a chegada dele (só uma vez)
$recem = $_SESSION['AvatarRecem'] ?? null;
unset($_SESSION['AvatarRecem']);

// agrupa os itens do inventário pela parte do corpo
$porSlot = [];
foreach ($itens as $item) {
    $porSlot[slotDoItem($item)][] = $item;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vestiário — ED Ensino</title>
    <link rel="stylesheet" href="../css/base.css?v=2">
    <link rel="stylesheet" href="../css/header.css?v=5">
    <link rel="stylesheet" href="../css/footer.css?v=3">
    <link rel="stylesheet" href="../css/painel.css?v=4">
</head>
<body>
<?php include 'header.php'; ?>

<main class="painel-container">

    <span class="eyebrow">Vestiário</span>
    <h1 class="painel-titulo">Monte seu personagem</h1>
    <p class="painel-subtitulo">
        Um item por parte do corpo. Passe o mouse em um item para <strong>provar</strong>
        antes de equipar — cada item também libera uma habilidade no Quiz.
    </p>

    <?php if (isset($_SESSION['MensagemInventario'])) { ?>
        <div class="painel-alerta alerta-sucesso"><?php echo htmlspecialchars($_SESSION['MensagemInventario']); unset($_SESSION['MensagemInventario']); ?></div>
    <?php } ?>

    <div class="vest-grade">

        <!-- ========== PERSONAGEM ========== -->
        <section class="vest-palco" data-provador>
            <div class="vest-avatar">
                <?php echo avatarSvg($equipados, ['provador' => $itens, 'recem' => $recem, 'titulo' => 'Avatar de ' . $user['nome']]); ?>
                <span class="vest-pedestal"></span>
                <span class="vest-aviso" data-provador-aviso></span>
            </div>

            <div class="vest-identidade">
                <h2><?php echo htmlspecialchars($user['nome']); ?></h2>
                <p>
                    <span class="moeda"><?php echo (int)$user['moedas']; ?> moedas</span>
                    · <?php echo count($equipados); ?> de <?php echo count(AVATAR_SLOTS); ?> partes equipadas
                </p>
            </div>

            <?php echo avatarSlotsHtml($equipados, 'inventario'); ?>
        </section>

        <!-- ========== ITENS ========== -->
        <section class="vest-itens">

            <?php if (!$itens) { ?>
                <div class="painel-card vest-vazio">
                    <div class="item-img">🎒</div>
                    <h2>Seu inventário está vazio</h2>
                    <p>Jogue o Quiz para ganhar moedas e compre itens na loja. Lá você também pode provar tudo antes de comprar.</p>
                    <a class="painel-btn" href="loja.php">Abrir a loja</a>
                    <a class="painel-btn painel-btn--contorno" href="quiz.php">Jogar o Quiz</a>
                </div>
            <?php } ?>

            <?php if ($itens) { ?>
                <h2 class="vest-grupo">Seus itens · <?php echo count($itens); ?></h2>
            <?php } ?>

            <!-- uma grade só, na ordem das partes do corpo (chapéu → pés) -->
            <div class="vest-lista">
            <?php foreach (AVATAR_SLOTS as $slot => $nomeSlot) {
                if (empty($porSlot[$slot])) continue; ?>
                    <?php foreach ($porSlot[$slot] as $item) {
                        $equipado = (int)$item['equipado'] === 1;
                        $chave    = chaveDoItem($item); ?>

                        <article class="item-card raridade-<?php echo htmlspecialchars($item['raridade']); ?> <?php echo $equipado ? 'equipado' : ''; ?>"
                                 data-provar="<?php echo $equipado ? 'padrao' : $chave; ?>"
                                 data-slot="<?php echo $slot; ?>"
                                 data-nome="<?php echo $equipado ? 'sem ' . htmlspecialchars($item['nome']) : htmlspecialchars($item['nome']); ?>">

                            <div class="item-card-icone">
                                <img src="../<?php echo htmlspecialchars($item['imagem']); ?>" alt="">
                                <?php if ($equipado) { ?><span class="item-card-check" title="Equipado">✓</span><?php } ?>
                            </div>

                            <div class="item-card-corpo">
                                <span class="raridade raridade-<?php echo htmlspecialchars($item['raridade']); ?>"><?php echo htmlspecialchars($item['raridade']); ?></span>
                                <span class="item-card-slot"><?php echo $nomeSlot; ?></span>
                                <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                                <p class="item-card-habilidade"><strong><?php echo htmlspecialchars($item['habilidade']); ?>:</strong>
                                    <?php echo htmlspecialchars(explicarHabilidade($item['habilidade'])); ?></p>

                                <div class="item-card-acoes">
                                    <form method="post" action="../processamento/processamento.php">
                                        <input type="hidden" name="acao" value="<?php echo $equipado ? 'desequipar_item' : 'equipar_item'; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                                        <input type="hidden" name="voltar" value="inventario">
                                        <?php if ($equipado) { ?>
                                            <button class="painel-btn painel-btn--contorno" type="submit">Remover</button>
                                        <?php } else { ?>
                                            <button class="painel-btn painel-btn--verde" type="submit">Equipar</button>
                                        <?php } ?>
                                    </form>
                                    <button type="button" class="item-card-provar" data-botao-provar>Provar</button>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
            <?php } ?>
            </div>

            <?php if ($itens) { ?>
                <a class="vest-mais" href="loja.php">Quer mais itens? Visite a loja &rarr;</a>
            <?php } ?>
        </section>

    </div>
</main>

<?php include 'footer.php'; ?>
<script src="../js/avatar.js?v=1"></script>
</body>
</html>

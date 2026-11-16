<?php
include 'session.php';
require_once("../model/banco.php");
require_once("habilidades_texto.php");
require_once("avatar.php");

$bd        = new banco();
$usuarioId = (int)$_SESSION['usuario_id'];
$user      = $bd->getUserById($usuarioId);
$equipados = $bd->listarItensEquipados($usuarioId);
$inventario = $bd->listarInventario($usuarioId);

$recem = $_SESSION['AvatarRecem'] ?? null;
unset($_SESSION['AvatarRecem']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil — ED Ensino</title>
    <link rel="stylesheet" href="../css/base.css?v=2">
    <link rel="stylesheet" href="../css/header.css?v=5">
    <link rel="stylesheet" href="../css/footer.css?v=3">
    <link rel="stylesheet" href="../css/painel.css?v=4">
</head>
<body>
<?php include 'header.php'; ?>

<main class="painel-container">

    <?php if (isset($_SESSION['Error'])) { ?><div class="painel-alerta alerta-erro"><?php echo $_SESSION['Error']; unset($_SESSION['Error']); ?></div><?php } ?>
    <?php if (isset($_SESSION['Sucesso'])) { ?><div class="painel-alerta alerta-sucesso"><?php echo $_SESSION['Sucesso']; unset($_SESSION['Sucesso']); ?></div><?php } ?>
    <?php if (isset($_SESSION['MensagemInventario'])) { ?><div class="painel-alerta alerta-sucesso"><?php echo htmlspecialchars($_SESSION['MensagemInventario']); unset($_SESSION['MensagemInventario']); ?></div><?php } ?>

    <!-- ========== CARTÃO DO PERSONAGEM ========== -->
    <section class="perfil-heroi">
        <div class="perfil-avatar">
            <?php echo avatarSvg($equipados, ['recem' => $recem, 'titulo' => 'Avatar de ' . $user['nome']]); ?>
            <span class="vest-pedestal"></span>
        </div>

        <div class="perfil-info">
            <span class="eyebrow">Perfil do estudante</span>
            <h1><?php echo htmlspecialchars($user['nome']); ?></h1>
            <p class="perfil-email"><?php echo htmlspecialchars($user['email']); ?></p>

            <div class="perfil-numeros">
                <div><strong class="moeda"><?php echo (int)$user['moedas']; ?></strong><span>moedas</span></div>
                <div><strong><?php echo count($equipados); ?>/<?php echo count(AVATAR_SLOTS); ?></strong><span>equipados</span></div>
                <div><strong><?php echo count($inventario); ?></strong><span>itens no inventário</span></div>
            </div>

            <?php if ($equipados) { ?>
                <div class="perfil-poderes">
                    <span>Habilidades ativas no Quiz:</span>
                    <?php foreach ($equipados as $item) { ?>
                        <span class="perfil-poder" title="<?php echo htmlspecialchars(explicarHabilidade($item['habilidade'])); ?>">
                            <?php echo htmlspecialchars($item['habilidade']); ?>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="perfil-acoes">
                <a class="painel-btn" href="inventario.php">Abrir vestiário</a>
                <a class="painel-btn painel-btn--contorno" href="loja.php">Ir à loja</a>
            </div>
        </div>

        <div class="perfil-slots">
            <?php echo avatarSlotsHtml($equipados, 'perfil'); ?>
        </div>
    </section>

    <!-- ========== DADOS DA CONTA ========== -->
    <div class="painel-grid perfil-forms">
        <section class="painel-card">
            <h2>Editar dados</h2>
            <form class="painel-form" method="post" action="../processamento/processamento.php">
                <input type="hidden" name="acao" value="perfil">
                <label>Nome</label>
                <input name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <label>CPF</label>
                <input name="cpf" value="<?php echo htmlspecialchars($user['cpf']); ?>" required>
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

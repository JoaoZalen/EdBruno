<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

require_once(__DIR__ . "/avatar.php");

$moedasHeader    = null;
$equipadosHeader = [];
if (isset($_SESSION['usuario_id'])) {
    require_once("../model/banco.php");
    $bdHeader = new banco();
    $userHeader = $bdHeader->getUserById((int)$_SESSION['usuario_id']);
    if ($userHeader) {
        $moedasHeader = (int)$userHeader['moedas'];
        // itens equipados: o rosto do avatar no header usa os mesmos itens
        $equipadosHeader = $bdHeader->listarItensEquipados((int)$_SESSION['usuario_id']);
    }
}

/* Página atual: usada para destacar o link certo no menu */
$paginaAtual = basename($_SERVER['PHP_SELF']);

$conteudos = [
    'tad.php'             => 'TAD',
    'lista_simples.php'   => 'Lista Simples',
    'lista_dupla.php'     => 'Lista Dupla',
    'pilha.php'           => 'Pilha',
    'fila_fifo.php'       => 'Fila FIFO',
    'fila_prioridade.php' => 'Fila de Prioridade',
    'arvore_binaria.php'  => 'Árvore Binária',
];

function linkAtivo($arquivo, $paginaAtual) {
    return $arquivo === $paginaAtual ? 'ativo' : '';
}

$emConteudo = array_key_exists($paginaAtual, $conteudos);

/* Só mostra os dados do usuário se o objeto da sessão estiver completo
   (evita erro em páginas que abrem a sessão sem carregar a classe Usuario) */
$usuarioLogado = isset($_SESSION['usuario']) && $_SESSION['usuario'] instanceof Usuario;
?>

<!-- animações do avatar (vale para todas as páginas que incluem o header) -->
<link rel="stylesheet" href="../css/avatar.css?v=1">

<header class="navbar">
    <div class="navbar-interna">

        <a href="home.php" class="logo-link" aria-label="ED Ensino — início">
            <span class="logo-marca">ED</span>
            <span class="logo-texto">Ensino</span>
        </a>

        <!-- Menu mobile: o checkbox abre/fecha o menu só com CSS -->
        <input type="checkbox" id="menu-toggle" class="menu-toggle">
        <label for="menu-toggle" class="menu-botao" aria-label="Abrir menu">
            <span></span><span></span><span></span>
        </label>

        <nav class="nav-links" aria-label="Menu principal">

            <a href="home.php" class="<?php echo linkAtivo('home.php', $paginaAtual); ?>">Início</a>

            <div class="nav-grupo <?php echo $emConteudo ? 'ativo' : ''; ?>">
                <button type="button" class="nav-grupo-titulo">Conteúdos</button>
                <div class="nav-submenu">
                    <?php foreach ($conteudos as $arquivo => $nome) { ?>
                        <a href="<?php echo $arquivo; ?>" class="<?php echo linkAtivo($arquivo, $paginaAtual); ?>">
                            <?php echo $nome; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <a href="simulador.php" class="<?php echo linkAtivo('simulador.php', $paginaAtual); ?>">Simulador</a>

            <a href="quiz.php" class="<?php echo linkAtivo('quiz.php', $paginaAtual); ?>">Quiz</a>

            <a href="loja.php" class="<?php echo linkAtivo('loja.php', $paginaAtual); ?>">Loja</a>

            <a href="perfil.php" class="<?php echo linkAtivo('perfil.php', $paginaAtual); ?>">Perfil</a>

        </nav>

        <div class="user-area">

            <?php if($usuarioLogado){ ?>

                <?php if($moedasHeader !== null){ ?>
                    <span class="moedas-header" title="Suas moedas"><?php echo $moedasHeader; ?></span>
                <?php } ?>

                <!-- Rosto do avatar; ao passar o mouse abre o cartão do personagem -->
                <div class="usuario-grupo">
                    <a href="perfil.php" class="usuario-chip">
                        <span class="header-avatar">
                            <?php echo avatarSvg($equipadosHeader, ['recorte' => 'cabeca', 'titulo' => 'Seu avatar']); ?>
                        </span>
                        <span class="usuario"><?php echo htmlspecialchars($_SESSION['usuario']->get_Nome()); ?></span>
                    </a>

                    <div class="usuario-cartao">
                        <div class="usuario-cartao-avatar">
                            <?php echo avatarSvg($equipadosHeader, ['titulo' => 'Seu avatar']); ?>
                        </div>
                        <strong><?php echo htmlspecialchars($_SESSION['usuario']->get_Nome()); ?></strong>
                        <span class="usuario-cartao-sub"><?php echo count($equipadosHeader); ?> de <?php echo count(AVATAR_SLOTS); ?> partes equipadas</span>
                        <a href="perfil.php">Meu perfil</a>
                        <a href="inventario.php">Vestiário</a>
                        <a href="loja.php">Loja do avatar</a>
                    </div>
                </div>

                <a href="logout.php" class="btn-sair">Sair</a>

            <?php }else{ ?>

                <a href="login.php" class="btn-login">Entrar</a>

            <?php } ?>

        </div>

    </div>
</header>

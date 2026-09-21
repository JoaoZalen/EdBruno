<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

$moedasHeader = null;
if (isset($_SESSION['usuario_id'])) {
    require_once("../model/banco.php");
    $bdHeader = new banco();
    $userHeader = $bdHeader->getUserById((int)$_SESSION['usuario_id']);
    if ($userHeader) {
        $moedasHeader = (int)$userHeader['moedas'];
    }
}

?>

<header class="navbar">

    <div class="logo-area">

        <a href="home.php" class="logo-link">
            <h1 class="logo">
                ED<span>Ensino</span>
            </h1>
        </a>

    </div>

    <nav class="nav-links">

        <a href="home.php">Home</a>

        <a href="tad.php">TAD</a>

        <a href="lista_simples.php">Lista Simples</a>

        <a href="lista_dupla.php">Lista Dupla</a>

        <a href="pilha.php">Pilha</a>

        <a href="fila_fifo.php">Fila FiFo</a>

        <a href="fila_prioridade.php">Fila de Prioridade</a>

        <a href="quiz.php">Quiz</a>

        <a href="loja.php">Loja</a>

        <a href="perfil.php">Perfil</a>

    </nav>

    <div class="user-area">

        <?php if(isset($_SESSION['usuario'])){ ?>

            <img
                src="../uploads/<?php echo $_SESSION['usuario']->get_Foto(); ?>"
                alt="Foto do usuário"
                class="header-foto"
            >

            <span class="usuario">
                <?php echo $_SESSION['usuario']->get_Nome(); ?>
                <?php if($moedasHeader !== null){ ?>
                    <br><?php echo $moedasHeader; ?> moedas
                <?php } ?>
            </span>

            <a href="logout.php" class="btn-sair">Sair</a>

        <?php }else{ ?>

            <a href="login.php" class="btn-login">Login</a>

        <?php } ?>

    </div>

</header>

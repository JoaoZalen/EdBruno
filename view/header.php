<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
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

    </nav>

    <div class="user-area">

        <?php if(isset($_SESSION['usuario'])){ ?>

            <span class="usuario">
                <?php
                    if(isset($_SESSION['usuario_nome'])){
                        echo $_SESSION['usuario_nome'];
                    }
                ?>
            </span>

            <a href="logout.php" class="btn-sair">Sair</a>

        <?php }else{ ?>

            <a href="login.php" class="btn-login">Login</a>

        <?php } ?>

    </div>

</header>
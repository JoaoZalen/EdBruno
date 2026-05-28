

<header class="header-principal">

    <section class="header-barra">

        <a href="home.php" class="header-logo">
            Ed Ensino
            <span>Estruturas de Dados</span>
        </a>

        <nav class="header-menu">

            <ul class="header-nav">

                <li><a href="home.php">Home</a></li>

                <li><a href="tad.php">TAD</a></li>

                <li><a href="lista_simples.php">Lista Simples</a></li>

                <li><a href="lista_dupla.php">Lista Dupla</a></li>

                <?php
                if(isset($_SESSION['usuario']))
                {
                    $usuario = $_SESSION['usuario'];
                ?>

                    <li class="header-usuario">

                        <img 
                            src="../uploads/<?php echo $usuario->get_Foto(); ?>"
                            alt="Foto Perfil"
                            class="header-foto"
                        >

                    </li>

                <?php
                }
                ?>

                <li>
                    <a href="logout.php" class="nav-sair">
                        Sair
                    </a>
                </li>

            </ul>

        </nav>

    </section>

</header>
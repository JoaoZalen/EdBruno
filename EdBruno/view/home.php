<?php
include 'session.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Ed Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/home.css?v=2">
</head>
<body>


<?php include 'header.php'; ?>


<!-- ========== HERO ========== -->
<section class="home-hero">
    <h1>
    Olá 
    <?php
        if(isset($_SESSION['usuario']))
        {
        $user = $_SESSION['usuario'];
        }
    echo strtoupper($user->get_Nome().',') ;
    ?>
     Bem-vindo ao Ed Ensino</h1>
    <p>
        Portal de ensino de Estruturas de Dados.
        Explore os conteúdos abaixo, com teoria, imagens e exemplos de código.
    </p>
</section>


<!-- ========== CARDS DE CONTEÚDO ========== -->
<main class="home-conteudo">

    <p class="home-secao-titulo">Escolha um conteúdo</p>

    <div class="home-cards">

        <!-- Card: TAD -->
        <a href="tad.php" class="home-card">
            <div class="home-card-icone">&#128196;</div>
            <h2>TAD — Tipo Abstrato de Dado</h2>
            <p>
                Aprenda o conceito de abstração de dados, separação entre
                interface e implementação, e como modelar estruturas
                de forma independente da linguagem.
            </p>
            <span class="home-card-link">Ver conteúdo &rarr;</span>
        </a>

        <!-- Card: Lista Simples -->
        <a href="lista_simples.php" class="home-card">
            <div class="home-card-icone">&#128279;</div>
            <h2>Lista Simplesmente Encadeada</h2>
            <p>
                Estude a estrutura de nós encadeados em uma única direção.
                Veja operações de inserção, remoção e busca com exemplos
                visuais e código em C#.
            </p>
            <span class="home-card-link">Ver conteúdo &rarr;</span>
        </a>

        <!-- Card: Lista Dupla -->
        <a href="lista_dupla.php" class="home-card">
            <div class="home-card-icone">&#8644;</div>
            <h2>Lista Duplamente Encadeada</h2>
            <p>
                Entenda como nós com ponteiros para os dois sentidos
                funcionam. Navegue para frente e para trás na lista
                com exemplos práticos e ilustrações.
            </p>
            <span class="home-card-link">Ver conteúdo &rarr;</span>
        </a>

    </div>

</main>


<?php include 'footer.php'; ?>

</body>
</html>

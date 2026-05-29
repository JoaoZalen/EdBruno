<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Home - ED Ensino</title>

    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/home.css">

</head>

<body>

<?php include 'header.php'; ?>

<main class="home-container">

    <section class="hero">

        <div class="hero-left">

            <h1>
                Aprenda
                <span>Estruturas de Dados</span>
                de forma moderna
            </h1>

            <p>
                Explore conteúdos sobre TAD, listas simplesmente encadeadas
                e listas duplamente encadeadas com exemplos em C#,
                explicações visuais e teoria completa.
            </p>

            <div class="hero-buttons">

                <a href="tad.php" class="btn-primary">
                    Começar
                </a>

                <a href="#conteudos" class="btn-secondary">
                    Ver conteúdos
                </a>

            </div>

        </div>

        <div class="hero-right">

            <div class="code-card">

<pre>
class Node
{
    public int valor;
    public Node prox;
}
</pre>

            </div>

        </div>

    </section>

    <section class="conteudos" id="conteudos">

        <h2>
            Conteúdos Disponíveis
        </h2>

        <div class="cards">

            <a href="tad.php" class="card">

                <div class="card-icon">📘</div>

                <h3>TAD</h3>

                <p>
                    Entenda o conceito de Tipo Abstrato de Dados,
                    sua importância e aplicações.
                </p>

            </a>

            <a href="lista_simples.php" class="card">

                <div class="card-icon">🔗</div>

                <h3>Lista Simples</h3>

                <p>
                    Aprenda como funcionam listas simplesmente
                    encadeadas e seus ponteiros.
                </p>

            </a>

            <a href="lista_dupla.php" class="card">

                <div class="card-icon">↔️</div>

                <h3>Lista Dupla</h3>

                <p>
                    Explore listas duplamente encadeadas e
                    navegação bidirecional.
                </p>

            </a>

        </div>

    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
<?php
include 'session.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista Simplesmente Encadeada</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/lista_simples.css?v=2">

</head>
<?php include 'header.php'; ?>
<div class="pagina-banner">
    <h1>Lista Simplesmente Encadeada</h1>
    <p>Foco comparativo (ideal para quem já conhece vetores)</p>
</div>

<main class="pagina-conteudo">

<section class="secao">
        <h2 class="secao-titulo">O que é uma Lista Simplesmente Encadeada?</h2>
        <p>
            Uma <strong> Lista Simplesmente Encadeada</strong> (ou Singly Linked List) é uma estrutura de dados linear e
             dinâmica usada para armazenar uma coleção de elementos.
        </p>

        <p>
        Diferente dos vetores (arrays), onde os elementos estão guardados em posições seguidas na memória, na lista encadeada os elementos podem estar espalhados. A mágica para que eles continuem organizados
         está na forma como eles se conectam.
        </p>

        <div class="destaque">
            <strong>2. Componentes de uma Lista</strong> Para gerenciar essa estrutura, precisamos 
            de três referências principais:<em>Cabeça (Head)</em>, o <em>Cauda (Tail)</em> e o Nó (Node).
        </div>

        <figure class="secao-imagem">
            <img
                src="../imgs/listasimples/figura1.png.png"
                alt="Diagrama de uma Lista simplismente Encadeada"
            >
            <figcaption>Figura 1 — Representação visual de uma Lista simplismente Encadeada</figcaption>
        </figure>
    </section>
    <hr class="divisor">
 
 <section class="secao">
     <h2 class="secao-titulo">Estrutura do Nó</h2>

     <p>
     O nó de uma lista duplamente encadeada agora carrega três elementos:

O Conteúdo/Valor: O dado armazenado.

O Ponteiro Próximo (Next): O endereço do próximo nó da lista.

O Ponteiro Anterior (Prev): O endereço do nó que vem logo antes dele.
     </p>
     <figure class="secao-imagem">
            <img
                src="../imgs/listasimples/figura2.png.png"
                alt="Estrutura de um nó da lista simplismente encadeada"
            >
            <figcaption>Figura 2 — Campos de um nó: Anterior | Dado | Próximo</figcaption>
        </figure>

<?php include 'footer.php'; ?>
</body>
</html>

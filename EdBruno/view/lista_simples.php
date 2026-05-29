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
    <link rel="stylesheet" href="../css/lista_simples.css?v=10">
</head>

<body>

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
            O nó de uma lista simplesmente encadeada carrega dois elementos principais:
        </p>

        <ul>
            <li><strong>Conteúdo/Valor:</strong> o dado armazenado dentro do nó.</li>
            <li><strong>Ponteiro Próximo (Next):</strong> o endereço do próximo nó da lista.</li>
        </ul>

        <p>
            Diferente da lista duplamente encadeada, a lista simplesmente encadeada não possui ponteiro para o nó anterior.
            Por isso, sua navegação acontece apenas em um sentido.
        </p>

        <figure class="secao-imagem">
            <img
                src="../imgs/listasimples/figura2.png.png"
                alt="Estrutura de um nó da lista simplismente encadeada"
            >
            <figcaption>Figura 2 — Campos de um nó: Dado | Próximo</figcaption>
        </figure>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Funcionamento da Lista</h2>

        <p>
            A lista simplesmente encadeada funciona por meio da ligação entre os nós. Cada nó conhece apenas o próximo
            elemento da sequência. O primeiro nó é acessado pela referência chamada <strong>Head</strong>, ou cabeça da lista.
        </p>

        <p>
            Para percorrer a lista, o algoritmo começa no primeiro nó e segue de nó em nó usando o ponteiro próximo,
            até encontrar o final da estrutura.
        </p>

        <div class="destaque">
            <strong>Importante:</strong> o último nó da lista aponta para <em>null</em>, indicando que não existe próximo elemento.
        </div>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Comparação com Vetores</h2>

        <p>
            Uma diferença importante entre vetores e listas encadeadas está na forma como os dados são armazenados na memória.
        </p>

        <ul>
            <li>Nos vetores, os elementos ficam em posições contínuas da memória.</li>
            <li>Nas listas encadeadas, os nós podem ficar espalhados na memória.</li>
            <li>Nos vetores, o acesso por índice é direto.</li>
            <li>Nas listas, é necessário percorrer os nós até encontrar o elemento desejado.</li>
        </ul>

        <p>
            Por isso, vetores são melhores quando precisamos acessar posições específicas rapidamente, enquanto listas
            são úteis quando precisamos inserir e remover elementos com mais flexibilidade.
        </p>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Vantagens</h2>

        <p>
            As listas simplesmente encadeadas apresentam diversas vantagens quando comparadas aos vetores tradicionais.
        </p>

        <ul>
            <li>Inserção de elementos sem necessidade de mover toda a estrutura.</li>
            <li>Remoção rápida de elementos quando já se conhece a posição anterior.</li>
            <li>Uso dinâmico de memória.</li>
            <li>Facilidade para crescimento da estrutura.</li>
            <li>Boa utilização em estruturas mais complexas, como pilhas e filas.</li>
        </ul>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Desvantagens</h2>

        <p>
            Apesar de suas vantagens, as listas simplesmente encadeadas também possuem algumas limitações.
        </p>

        <ul>
            <li>Não possuem acesso direto aos elementos por índice.</li>
            <li>Necessitam percorrer a lista para localizar um item.</li>
            <li>Consomem memória extra devido ao ponteiro próximo.</li>
            <li>Possuem implementação mais complexa que vetores.</li>
            <li>A navegação ocorre apenas em um sentido.</li>
        </ul>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Operações Básicas</h2>

        <p>
            As operações mais comuns realizadas em uma lista simplesmente encadeada são:
        </p>

        <ul>
            <li><strong>Inserção no início:</strong> adiciona um novo nó antes do primeiro elemento.</li>
            <li><strong>Inserção no final:</strong> adiciona um novo nó após o último elemento.</li>
            <li><strong>Remoção:</strong> retira um nó da lista ajustando os ponteiros.</li>
            <li><strong>Busca:</strong> percorre a lista procurando um valor específico.</li>
            <li><strong>Exibição:</strong> percorre todos os nós mostrando seus valores.</li>
        </ul>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Exemplo em C#</h2>

        <p>
            Abaixo temos um exemplo simples da estrutura de um nó em C#.
        </p>

        <span class="codigo-label">Classe Nó</span>

        <div class="bloco-codigo">
<pre><code>public class No
{
    public int Valor;
    public No Proximo;

    public No(int valor)
    {
        Valor = valor;
        Proximo = null;
    }
}</code></pre>
        </div>

        <p>
            Nesse exemplo, cada nó possui um valor inteiro e uma referência para o próximo nó da lista.
        </p>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Exemplo de Inserção no Início em C#</h2>

        <p>
            A inserção no início acontece quando um novo nó passa a apontar para o antigo primeiro nó da lista.
            Depois disso, a cabeça da lista passa a ser esse novo nó.
        </p>

        <span class="codigo-label">Inserção no início</span>

        <div class="bloco-codigo">
<pre><code>public class ListaSimples
{
    private No inicio;

    public void InserirInicio(int valor)
    {
        No novoNo = new No(valor);
        novoNo.Proximo = inicio;
        inicio = novoNo;
    }
}</code></pre>
        </div>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Aplicações</h2>

        <p>
            As listas simplesmente encadeadas são utilizadas em diversos sistemas computacionais.
        </p>

        <ul>
            <li>Implementação de pilhas.</li>
            <li>Implementação de filas.</li>
            <li>Gerenciamento dinâmico de memória.</li>
            <li>Representação de sequências de dados.</li>
            <li>Base para estruturas mais complexas, como grafos e tabelas hash com encadeamento.</li>
        </ul>
    </section>

    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Conclusão</h2>

        <p>
            A Lista Simplesmente Encadeada é uma estrutura dinâmica importante no estudo de Estruturas de Dados.
            Ela permite armazenar elementos de forma flexível, utilizando nós conectados por ponteiros.
        </p>

        <p>
            Apesar de não possuir acesso direto por índice, ela é muito útil quando o programa precisa inserir ou remover
            elementos sem depender de posições fixas na memória.
        </p>
    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
<?php
include 'session.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/lista_dupla.css?v=2">
    <title>Lista Duplamente Encadeada</title>
</head>
<body>
<?php include 'header.php'; ?>

<!-- ========== BANNER DA PÁGINA ========== -->
<div class="pagina-banner">
    <h1>Lista Duplamente Encadeada</h1>
    <p></p>
</div>
 
 
<!-- ========== CONTEÚDO PRINCIPAL ========== -->
<main class="pagina-conteudo">
 
 
    <!-- ======================================
         SEÇÃO 1 — O que é?
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">O que é uma Lista Duplamente Encadeada?</h2>
 
        <p>
            Uma <strong>Lista Duplamente Encadeada</strong> é uma estrutura de dados linear composta
            por nós, onde cada nó armazena um valor e dois ponteiros: um apontando para o
            nó anterior e outro para o próximo nó da lista.
        </p>
 
        <p>
            Diferente da lista simplesmente encadeada, aqui é possível percorrer a lista
            nos dois sentidos — do início ao fim e do fim ao início.
        </p>
 
        <div class="destaque">
            <strong>Ponto-chave:</strong> cada nó possui três partes —
            ponteiro para o <em>anterior</em>, o <em>dado</em> armazenado
            e o ponteiro para o <em>próximo</em>.
        </div>
 
        <figure class="secao-imagem">
            <img
                src="../imgs/listaDupla/figura1.png"
                alt="Diagrama de uma Lista Duplamente Encadeada"
            >
            <figcaption>Figura 1 — Representação visual de uma Lista Duplamente Encadeada</figcaption>
        </figure>
    </section>
 
    <hr class="divisor">
 
    <section class="secao">
        <h2 class="secao-titulo">Estrutura do Nó</h2>
 
        <p>
            Cada nó da lista possui três campos. Em C#, podemos representá-lo
            como uma classe com as seguintes propriedades:
        </p>
 
        <figure class="secao-imagem">
            <img
                src="../imgs/listaDupla/figura2.png"
                alt="Estrutura de um nó da lista duplamente encadeada"
            >
            <figcaption>Figura 2 — Campos de um nó: Anterior | Dado | Próximo</figcaption>
        </figure>
 
        <span class="codigo-label">C# — Classe No</span>
        <div class="bloco-codigo">
            <pre><code>public class No
{
    public int Dado;       // valor armazenado no nó
    public No Anterior;    // ponteiro para o nó anterior
    public No Proximo;     // ponteiro para o próximo nó
 
    public No(int dado)
    {
        Dado     = dado;
        Anterior = null;
        Proximo  = null;
    }
}</code></pre>
        </div>
    </section>
 
    <hr class="divisor">
 
    <section class="secao">
        <h2 class="secao-titulo">Inserção no Início</h2>
 
        <p>
            Para inserir um nó no início da lista, criamos um novo nó e o colocamos
            antes do nó que era a cabeça. Os ponteiros são ajustados assim:
        </p>
 
        <ul>
            <li>O campo <code>Proximo</code> do novo nó aponta para a cabeça atual.</li>
            <li>O campo <code>Anterior</code> da cabeça atual aponta para o novo nó.</li>
            <li>A cabeça da lista passa a ser o novo nó.</li>
        </ul>
 
        <span class="codigo-label">C# — Método InserirInicio</span>
        <div class="bloco-codigo">
            <pre><code>public class ListaDupla
{
    private No cabeca;
 
    public ListaDupla()
    {
        cabeca = null;
    }
 
    public void InserirInicio(int dado)
    {
        No novo = new No(dado);
 
        if (cabeca == null)
        {
            cabeca = novo;
            return;
        }
 
        novo.Proximo    = cabeca;   // novo aponta para a antiga cabeça
        cabeca.Anterior = novo;     // antiga cabeça aponta de volta para o novo
        cabeca          = novo;     // novo vira a cabeça
    }
}</code></pre>
        </div>
    </section>
 
    <hr class="divisor">

    <section class="secao">
        <h2 class="secao-titulo">Inserção no Fim</h2>
 
        <p>
            Para inserir no fim, percorremos a lista até o último nó e encadeamos
            o novo nó após ele.
        </p>
 
        <span class="codigo-label">C# — Método InserirFim</span>
        <div class="bloco-codigo">
            <pre><code>    public void InserirFim(int dado)
    {
        No novo = new No(dado);
 
        if (cabeca == null)
        {
            cabeca = novo;
            return;
        }
 
        No atual = cabeca;
 
        while (atual.Proximo != null)   // percorre até o último nó
        {
            atual = atual.Proximo;
        }
 
        atual.Proximo  = novo;    // último nó aponta para o novo
        novo.Anterior  = atual;   // novo aponta de volta para o último
    }</code></pre>
        </div>
    </section>
 
    <hr class="divisor">
 
 
    <!-- ======================================
         SEÇÃO 5 — Remoção
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Remoção de um Nó</h2>
 
        <p>
            Para remover um nó pelo valor, localizamos o nó e ajustamos os ponteiros
            dos nós vizinhos para "pular" o nó removido.
        </p>
 
        <div class="destaque">
            <strong>Atenção:</strong> ao remover, é preciso atualizar tanto o
            <code>Proximo</code> do nó anterior quanto o <code>Anterior</code>
            do nó seguinte, para não quebrar a cadeia.
        </div>
 
        <span class="codigo-label">C# — Método Remover</span>
        <div class="bloco-codigo">
            <pre><code>    public void Remover(int dado)
    {
        No atual = cabeca;
 
        while (atual != null && atual.Dado != dado)
        {
            atual = atual.Proximo;
        }
 
        if (atual == null) return;  // nó não encontrado
 
        if (atual.Anterior != null)
            atual.Anterior.Proximo = atual.Proximo;  // pula o nó removido
        else
            cabeca = atual.Proximo;                  // era a cabeça
 
        if (atual.Proximo != null)
            atual.Proximo.Anterior = atual.Anterior; // ajusta ponteiro de volta
    }</code></pre>
        </div>
    </section>
 
    <hr class="divisor">
 
 
    <!-- ======================================
         SEÇÃO 6 — Exibir a lista
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Exibindo a Lista</h2>
 
        <p>
            Para percorrer e exibir todos os elementos, basta começar pela cabeça
            e seguir os ponteiros <code>Proximo</code> até chegar ao fim.
        </p>
 
        <span class="codigo-label">C# — Método Exibir</span>
        <div class="bloco-codigo">
            <pre><code>    public void Exibir()
    {
        No atual = cabeca;
 
        Console.Write("NULL &lt;-&gt; ");
 
        while (atual != null)
        {
            Console.Write(atual.Dado + " &lt;-&gt; ");
            atual = atual.Proximo;
        }
 
        Console.WriteLine("NULL");
    }</code></pre>
        </div>
 
        <p style="margin-top: 12px;">
            Saída esperada para uma lista com os valores 10, 20 e 30:
        </p>
 
        <div class="bloco-codigo">
            <pre><code>NULL &lt;-&gt; 10 &lt;-&gt; 20 &lt;-&gt; 30 &lt;-&gt; NULL</code></pre>
        </div>
    </section>
 
 
</main>

<?php include 'footer.php'; ?>
</body>
</html>

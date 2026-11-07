<?php
include 'session.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/base.css?v=2">
    <link rel="stylesheet" href="../css/header.css?v=5">
    <link rel="stylesheet" href="../css/footer.css?v=3">
    <link rel="stylesheet" href="../css/conteudo.css?v=2">
    <title>Árvore Binária de Busca — ED Ensino</title>
</head>
<body>
<?php include 'header.php'; ?>

<!-- ========== BANNER DA PÁGINA ========== -->
<div class="pagina-banner">
    <h1>Árvore Binária de Busca</h1>
    <p>
        Até aqui todas as estruturas eram <strong>lineares</strong>: um elemento
        depois do outro. A árvore organiza os dados em <strong>hierarquia</strong>,
        e a árvore binária de busca usa essa hierarquia para encontrar um valor
        descartando metade dos elementos a cada comparação.
    </p>
    <span class="banner-tag">Menores à esquerda, maiores à direita</span>
    <div class="banner-acoes">
        <a href="simulador.php?ed=arvore_binaria" class="btn btn-sucesso">Simular Árvore Binária</a>
        <a href="home.php#trilha" class="btn btn-contorno">Ver trilha</a>
    </div>
</div>


<!-- ========== CONTEÚDO PRINCIPAL ========== -->
<main class="pagina-conteudo">

    <!-- ======================================
         SEÇÃO 1 — O que é uma árvore
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">O que é uma árvore?</h2>

        <p>
            Uma <strong>árvore</strong> é uma estrutura formada por nós ligados de
            forma hierárquica. Existe um nó no topo, chamado <strong>raiz</strong>,
            e cada nó pode apontar para outros nós abaixo dele, chamados de
            <strong>filhos</strong>. Diferente de uma lista, um nó pode ter mais de
            um "próximo".
        </p>

        <p>
            O nome vem da forma: é como uma árvore genealógica ou o organograma de
            uma empresa, só que desenhada de cabeça para baixo — a raiz fica em cima
            e as "folhas" ficam embaixo.
        </p>

        <div class="destaque">
            <strong>Ponto-chave:</strong> em uma árvore não existem ciclos. Partindo
            da raiz, existe exatamente um caminho até cada nó.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 2 — Vocabulário
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Vocabulário da árvore</h2>

        <p>Esses termos aparecem em toda explicação e em provas, vale decorar:</p>

        <div class="cartoes">
            <div class="cartao">
                <h3>Raiz</h3>
                <p>O primeiro nó da árvore. É o único que não tem pai.</p>
            </div>
            <div class="cartao">
                <h3>Pai e filho</h3>
                <p>Se o nó A aponta para o nó B, A é pai de B e B é filho de A.</p>
            </div>
            <div class="cartao">
                <h3>Folha</h3>
                <p>Nó que não tem nenhum filho. Fica nas "pontas" da árvore.</p>
            </div>
            <div class="cartao">
                <h3>Subárvore</h3>
                <p>Um nó junto com todos os seus descendentes forma uma árvore menor.</p>
            </div>
            <div class="cartao">
                <h3>Nível (profundidade)</h3>
                <p>Distância de um nó até a raiz. A raiz está no nível 0.</p>
            </div>
            <div class="cartao">
                <h3>Altura</h3>
                <p>Quantidade de níveis da árvore. Define quantos passos uma busca pode levar.</p>
            </div>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 3 — Árvore binária e a regra da busca
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Árvore binária de busca (BST)</h2>

        <p>
            Uma <strong>árvore binária</strong> é uma árvore em que cada nó tem
            <strong>no máximo dois filhos</strong>: o filho da esquerda e o filho da
            direita.
        </p>

        <p>
            A <strong>árvore binária de busca</strong> (em inglês, <em>Binary Search
            Tree</em>) adiciona uma regra que vale para <strong>todos</strong> os nós:
        </p>

        <ul>
            <li>todo valor na subárvore <strong>esquerda</strong> é <strong>menor</strong> que o valor do nó;</li>
            <li>todo valor na subárvore <strong>direita</strong> é <strong>maior</strong> que o valor do nó;</li>
            <li>não existem valores repetidos.</li>
        </ul>

        <figure class="secao-imagem">
            <img
                src="../imgs/arvore/figura1.svg"
                alt="Árvore binária de busca com raiz 50; à esquerda 30 com filhos 20 e 40; à direita 70 com filhos 60 e 80"
            >
            <figcaption>Figura 1 — Uma árvore binária de busca com 7 nós e 3 níveis</figcaption>
        </figure>

        <div class="destaque">
            <strong>Por que essa regra é poderosa:</strong> ao comparar um valor com a
            raiz, você já sabe de que lado ele está. A metade do outro lado pode ser
            ignorada inteira — é a mesma ideia da busca binária em um vetor ordenado.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 4 — Estrutura do nó
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Estrutura do nó</h2>

        <p>
            O nó da árvore binária é parecido com o da lista dupla: guarda um valor e
            duas referências. A diferença está no significado delas — em vez de
            "anterior" e "próximo", são os filhos <em>esquerdo</em> e <em>direito</em>.
        </p>

        <span class="codigo-label">C# — Classe No e classe da árvore</span>
        <div class="bloco-codigo">
            <pre><code>public class No
{
    public int Valor;
    public No Esquerda;   // filho com valor menor
    public No Direita;    // filho com valor maior

    public No(int valor)
    {
        Valor    = valor;
        Esquerda = null;  // todo nó nasce como folha
        Direita  = null;
    }
}

public class ArvoreBinariaBusca
{
    private No raiz;      // único ponto de entrada da árvore

    public ArvoreBinariaBusca()
    {
        raiz = null;      // árvore vazia
    }
}</code></pre>
        </div>

        <p class="explica-codigo">
            Assim como a pilha só conhece o <code>topo</code> e a lista só conhece a
            <code>cabeca</code>, a árvore só guarda a <code>raiz</code>. Todo acesso
            começa por ela e desce pelos ponteiros <code>Esquerda</code> e
            <code>Direita</code>.
        </p>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 5 — Inserção
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Inserção</h2>

        <p>
            Para inserir, começamos na raiz e vamos descendo: se o valor é menor,
            vamos para a esquerda; se é maior, para a direita. Quando encontramos um
            espaço vazio (<code>null</code>) no lado certo, o novo nó entra ali.
            <strong>Todo nó novo entra como folha.</strong>
        </p>

        <span class="codigo-label">C# — Método Inserir</span>
        <div class="bloco-codigo">
            <pre><code>    public void Inserir(int valor)
    {
        No novo = new No(valor);

        if (raiz == null) { raiz = novo; return; }   // árvore vazia

        No atual = raiz;
        while (true)
        {
            if (valor &lt; atual.Valor)
            {
                if (atual.Esquerda == null) { atual.Esquerda = novo; return; }
                atual = atual.Esquerda;
            }
            else if (valor &gt; atual.Valor)
            {
                if (atual.Direita == null) { atual.Direita = novo; return; }
                atual = atual.Direita;
            }
            else return;   // valor repetido não entra
        }
    }</code></pre>
        </div>

        <p>Exemplo: inserindo <code>45</code> na árvore da Figura 1.</p>

        <ol class="passos">
            <li><strong>Compara com 50:</strong> 45 &lt; 50, então desce para a esquerda (30).</li>
            <li><strong>Compara com 30:</strong> 45 &gt; 30, então desce para a direita (40).</li>
            <li><strong>Compara com 40:</strong> 45 &gt; 40 e a direita de 40 está vazia.</li>
            <li><strong>Encaixa:</strong> <code>40.Direita = novo</code>. Foram só 3 comparações em uma árvore de 7 nós.</li>
        </ol>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 6 — Busca
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Busca</h2>

        <p>
            A busca segue exatamente o mesmo caminho da inserção, mas em vez de
            inserir ao encontrar <code>null</code>, ela responde "não encontrado".
        </p>

        <span class="codigo-label">C# — Método Buscar</span>
        <div class="bloco-codigo">
            <pre><code>    public bool Buscar(int valor)
    {
        No atual = raiz;
        while (atual != null)
        {
            if (valor == atual.Valor) return true;
            if (valor &lt; atual.Valor) atual = atual.Esquerda;
            else                      atual = atual.Direita;
        }
        return false;   // chegou em null: não existe
    }</code></pre>
        </div>

        <div class="destaque">
            <strong>Compare com a lista:</strong> para achar um valor em uma lista de
            1.000 elementos podem ser necessárias 1.000 comparações. Em uma árvore
            binária de busca balanceada com 1.000 nós, bastam cerca de 10.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 7 — Remoção
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Remoção: os três casos</h2>

        <p>
            Remover é a operação mais delicada, porque a árvore precisa continuar
            obedecendo a regra depois que o nó sai. Primeiro localizamos o nó (como
            na busca) guardando também o <strong>pai</strong>. Depois, existem três
            situações:
        </p>

        <figure class="secao-imagem">
            <img
                src="../imgs/arvore/figura2.svg"
                alt="Três casos de remoção: remover uma folha, remover um nó com um filho e remover um nó com dois filhos usando o sucessor"
            >
            <figcaption>Figura 2 — Os três casos de remoção em uma árvore binária de busca</figcaption>
        </figure>

        <ol class="passos">
            <li>
                <strong>O nó é uma folha:</strong> basta o pai apontar para
                <code>null</code> no lugar dele.
            </li>
            <li>
                <strong>O nó tem um filho:</strong> o filho "sobe" e ocupa o lugar do
                nó removido. A regra continua valendo, porque toda a subárvore do
                filho já estava do lado certo do pai.
            </li>
            <li>
                <strong>O nó tem dois filhos:</strong> procuramos o
                <strong>sucessor</strong> — o menor valor da subárvore direita (um
                passo para a direita e depois tudo para a esquerda). Copiamos o valor
                do sucessor para o nó e removemos o sucessor, que sempre cai no caso
                1 ou 2.
            </li>
        </ol>

        <span class="codigo-label">C# — Método Remover</span>
        <div class="bloco-codigo">
            <pre><code>    public void Remover(int valor)
    {
        No pai = null, atual = raiz;

        // 1) localiza o nó, lembrando quem é o pai
        while (atual != null &amp;&amp; atual.Valor != valor)
        {
            pai   = atual;
            atual = valor &lt; atual.Valor ? atual.Esquerda : atual.Direita;
        }
        if (atual == null) return;                 // não encontrou

        // 2) dois filhos: troca pelo sucessor
        if (atual.Esquerda != null &amp;&amp; atual.Direita != null)
        {
            No paiSuc = atual, suc = atual.Direita;
            while (suc.Esquerda != null) { paiSuc = suc; suc = suc.Esquerda; }

            atual.Valor = suc.Valor;               // copia o valor
            pai = paiSuc;  atual = suc;            // agora remove o sucessor
        }

        // 3) zero ou um filho: o filho (ou null) ocupa o lugar
        No filho = atual.Esquerda ?? atual.Direita;

        if (pai == null)               raiz          = filho;
        else if (pai.Esquerda == atual) pai.Esquerda = filho;
        else                            pai.Direita  = filho;
    }</code></pre>
        </div>

        <p class="explica-codigo">
            Repare que o caso dos dois filhos não remove o nó original: ele só troca
            o valor e transforma o problema em um caso mais simples. O sucessor nunca
            tem filho à esquerda — se tivesse, ele não seria o menor da subárvore.
        </p>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 8 — Percursos
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Percursos: visitando todos os nós</h2>

        <p>
            Em uma lista só existe um jeito natural de visitar tudo: do começo ao fim.
            Na árvore existem vários, e cada um produz uma ordem diferente. Os três
            primeiros são <strong>recursivos</strong> e mudam apenas
            <em>quando</em> o nó atual é impresso:
        </p>

        <table class="tabela-complexidade">
            <thead>
                <tr>
                    <th>Percurso</th>
                    <th>Ordem</th>
                    <th>Resultado na Figura 1</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Em ordem</td>
                    <td>esq · nó · dir</td>
                    <td><code>20 30 40 50 60 70 80</code> — sai ordenado!</td>
                </tr>
                <tr>
                    <td>Pré-ordem</td>
                    <td>nó · esq · dir</td>
                    <td><code>50 30 20 40 70 60 80</code> — boa para copiar a árvore</td>
                </tr>
                <tr>
                    <td>Pós-ordem</td>
                    <td>esq · dir · nó</td>
                    <td><code>20 40 30 60 80 70 50</code> — filhos antes do pai (ex.: liberar memória)</td>
                </tr>
                <tr>
                    <td>Por nível</td>
                    <td>usa uma fila</td>
                    <td><code>50 30 70 20 40 60 80</code> — de cima para baixo</td>
                </tr>
            </tbody>
        </table>

        <span class="codigo-label">C# — Percursos recursivos</span>
        <div class="bloco-codigo">
            <pre><code>    void EmOrdem(No no)
    {
        if (no == null) return;
        EmOrdem(no.Esquerda);
        Console.Write(no.Valor + " ");   // visita no MEIO
        EmOrdem(no.Direita);
    }

    void PreOrdem(No no)
    {
        if (no == null) return;
        Console.Write(no.Valor + " ");   // visita ANTES
        PreOrdem(no.Esquerda);
        PreOrdem(no.Direita);
    }

    void PosOrdem(No no)
    {
        if (no == null) return;
        PosOrdem(no.Esquerda);
        PosOrdem(no.Direita);
        Console.Write(no.Valor + " ");   // visita DEPOIS
    }</code></pre>
        </div>

        <span class="codigo-label">C# — Percurso por nível (usa a Fila FIFO)</span>
        <div class="bloco-codigo">
            <pre><code>    void PorNivel()
    {
        if (raiz == null) return;
        Queue&lt;No&gt; fila = new Queue&lt;No&gt;();
        fila.Enqueue(raiz);

        while (fila.Count &gt; 0)
        {
            No no = fila.Dequeue();
            Console.Write(no.Valor + " ");
            if (no.Esquerda != null) fila.Enqueue(no.Esquerda);
            if (no.Direita  != null) fila.Enqueue(no.Direita);
        }
    }</code></pre>
        </div>

        <div class="destaque">
            <strong>Conexão entre conteúdos:</strong> os percursos recursivos usam a
            <em>pilha de chamadas</em> do programa, e o percurso por nível usa uma
            <em>fila</em>. As estruturas que você estudou antes aparecem aqui como
            ferramentas.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 9 — Complexidade
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Complexidade e o problema do desbalanceamento</h2>

        <p>
            O custo de inserir, buscar e remover depende da <strong>altura</strong>
            (<em>h</em>) da árvore, porque no pior caso descemos da raiz até uma
            folha.
        </p>

        <table class="tabela-complexidade">
            <thead>
                <tr>
                    <th>Operação</th>
                    <th>Árvore balanceada</th>
                    <th>Pior caso (degenerada)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Buscar</td><td>O(log n)</td><td>O(n)</td></tr>
                <tr><td>Inserir</td><td>O(log n)</td><td>O(n)</td></tr>
                <tr><td>Remover</td><td>O(log n)</td><td>O(n)</td></tr>
                <tr><td>Percorrer tudo</td><td>O(n)</td><td>O(n)</td></tr>
                <tr><td>Mínimo / Máximo</td><td>O(log n)</td><td>O(n)</td></tr>
            </tbody>
        </table>

        <div class="destaque">
            <strong>Atenção:</strong> se os valores forem inseridos já em ordem
            (<code>10, 20, 30, 40...</code>), cada nó vira filho direito do anterior e
            a árvore se transforma em uma lista encadeada — altura <em>n</em>, buscas
            em O(n). Teste isso no simulador! Árvores <strong>AVL</strong> e
            <strong>Rubro-Negras</strong> resolvem o problema fazendo rotações para
            manter a árvore balanceada.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 10 — Vantagens e desvantagens
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Vantagens e desvantagens</h2>

        <div class="colunas">
            <div class="coluna coluna-boa">
                <h3>Vantagens</h3>
                <ul>
                    <li>Busca, inserção e remoção rápidas quando balanceada.</li>
                    <li>O percurso em ordem entrega os dados já ordenados.</li>
                    <li>Mínimo e máximo ficam nas pontas: fáceis de achar.</li>
                    <li>Tamanho dinâmico, como as listas encadeadas.</li>
                </ul>
            </div>

            <div class="coluna coluna-ruim">
                <h3>Desvantagens</h3>
                <ul>
                    <li>Pode degenerar em lista se a ordem de inserção for ruim.</li>
                    <li>Remoção com dois filhos é mais complexa de implementar.</li>
                    <li>Cada nó gasta memória com dois ponteiros.</li>
                    <li>Não permite acesso direto por índice.</li>
                </ul>
            </div>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 11 — Aplicações
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Aplicações reais</h2>

        <div class="cartoes">
            <div class="cartao">
                <h3>Índices de banco de dados</h3>
                <p>Bancos como o MySQL usam árvores (variações chamadas B-Tree) para achar registros sem ler a tabela inteira.</p>
            </div>
            <div class="cartao">
                <h3>Coleções ordenadas</h3>
                <p>No C#, <code>SortedSet</code> e <code>SortedDictionary</code> são implementados com árvores balanceadas.</p>
            </div>
            <div class="cartao">
                <h3>Sistemas de arquivos</h3>
                <p>Pastas dentro de pastas formam uma árvore: a raiz é o disco e as folhas são os arquivos.</p>
            </div>
            <div class="cartao">
                <h3>Compiladores</h3>
                <p>O código é transformado em uma árvore sintática para ser analisado e executado na ordem certa.</p>
            </div>
        </div>
    </section>

    <aside class="chamada-simulador">
        <div>
            <h3>Agora veja na prática</h3>
            <p>Insira, remova e rode os percursos na Árvore Binária acompanhando cada comparação.</p>
        </div>
        <a href="simulador.php?ed=arvore_binaria" class="btn btn-sucesso">Abrir simulador</a>
    </aside>

</main>

<?php include 'footer.php'; ?>

</body>
</html>

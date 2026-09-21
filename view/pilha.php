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
    <link rel="stylesheet" href="../css/pilha.css?v=1">
    <title>Pilha Encadeada</title>
</head>
<body>
<?php include 'header.php'; ?>

<!-- ========== BANNER DA PÁGINA ========== -->
<div class="pagina-banner">
    <h1>Pilha Encadeada</h1>
    <p>
        A Pilha é uma estrutura de dados linear que segue o princípio
        <strong>LIFO</strong> (<em>Last In, First Out</em>): o último elemento
        inserido é sempre o primeiro a ser removido. Na versão encadeada,
        cada elemento vive em um nó ligado ao nó seguinte, e toda a manipulação
        acontece em um único ponto — o <em>topo</em> da pilha.
    </p>
    <span class="banner-tag">LIFO — último a entrar, primeiro a sair</span>
</div>


<!-- ========== CONTEÚDO PRINCIPAL ========== -->
<main class="pagina-conteudo">


    <!-- ======================================
         SEÇÃO 1 — O que é?
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">O que é uma Pilha Encadeada?</h2>

        <p>
            Uma <strong>Pilha Encadeada</strong> é uma pilha implementada com nós
            alocados dinamicamente, e não com um vetor de tamanho fixo. Cada nó
            guarda um dado e uma referência para o próximo nó, formando uma
            corrente que começa no topo e termina na base.
        </p>

        <p>
            A diferença em relação a uma lista encadeada comum não está na forma,
            e sim nas <strong>regras de acesso</strong>: em uma pilha só é permitido
            inserir e remover em uma única extremidade. Não existe "inserir no meio"
            nem "remover pelo valor" — essa restrição é justamente o que torna a
            estrutura previsível e barata.
        </p>

        <div class="destaque">
            <strong>Ponto-chave:</strong> a pilha encadeada não precisa saber
            quantos elementos vai armazenar. Ela cresce um nó por vez, enquanto
            houver memória disponível, sem precisar redimensionar nada.
        </div>

        <figure class="secao-imagem">
            <img
                src="../imgs/pilha/figura1.png"
                alt="Diagrama de uma pilha encadeada com a referência Topo apontando para o nó 30, encadeado até o nó 10 e NULL"
            >
            <figcaption>Figura 1 — O topo e os ponteiros entre os nós de uma Pilha Encadeada</figcaption>
        </figure>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 2 — Princípio LIFO
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">O princípio LIFO</h2>

        <p>
            <strong>LIFO</strong> significa <em>Last In, First Out</em>: o último
            elemento inserido é o primeiro a ser removido. Pense em uma pilha de
            pratos — você empilha um prato sobre o outro e, para pegar algum,
            começa pelo que está por cima. Tirar o prato de baixo exigiria
            remover todos os que estão acima dele.
        </p>

        <p>
            Na estrutura de dados acontece o mesmo. Se forem inseridos os valores
            <code>10</code>, <code>20</code> e <code>30</code>, nessa ordem, a
            primeira remoção devolve <code>30</code>, a segunda devolve
            <code>20</code> e a última devolve <code>10</code> — exatamente a
            ordem inversa da inserção.
        </p>

        <div class="destaque">
            <strong>Consequência prática:</strong> a pilha inverte a ordem dos
            elementos. Por isso ela é a estrutura natural para desfazer ações e
            para voltar ao estado anterior de um processo.
        </div>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 3 — Estrutura do nó
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Estrutura do nó</h2>

        <p>
            O nó é a menor peça da estrutura e possui apenas dois campos: o
            <em>dado</em> armazenado e a <em>referência</em> para o próximo nó.
            Diferente da lista duplamente encadeada, aqui não existe ponteiro para
            o anterior — a pilha nunca precisa andar para trás.
        </p>

        <figure class="secao-imagem">
            <img
                src="../imgs/pilha/figura3.png"
                alt="Estrutura de um nó da pilha encadeada, com os campos Dado e Próximo"
            >
            <figcaption>Figura 2 — Campos de um nó: Dado | Próximo</figcaption>
        </figure>

        <span class="codigo-label">C# — Classe No</span>
        <div class="bloco-codigo">
            <pre><code>public class No
{
    public int Dado;      // valor armazenado no nó
    public No Proximo;    // referência para o nó de baixo na pilha

    public No(int dado)
    {
        Dado    = dado;
        Proximo = null;   // ao nascer, o nó ainda não aponta para ninguém
    }
}</code></pre>
        </div>

        <p class="explica-codigo">
            O construtor recebe o valor e deixa <code>Proximo</code> como
            <code>null</code>. Quem define esse encadeamento é a operação
            <code>Push</code>, no momento em que o nó entra na pilha: só ali se
            sabe qual nó ficará abaixo dele. Note que o nó não guarda nenhuma
            posição ou índice — a ordem existe apenas por causa das referências.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 4 — Referência para o topo
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">A referência para o topo</h2>

        <p>
            A pilha em si guarda um único ponteiro: o <strong>topo</strong>. Ele
            aponta para o nó mais recente e é o único ponto de entrada da
            estrutura. Se o topo é <code>null</code>, a pilha está vazia.
        </p>

        <ul>
            <li>Todo <code>Push</code> faz o topo apontar para o nó recém-criado.</li>
            <li>Todo <code>Pop</code> faz o topo apontar para o nó que estava abaixo.</li>
            <li>A base da pilha é o nó cujo <code>Proximo</code> é <code>null</code>.</li>
        </ul>

        <span class="codigo-label">C# — Classe PilhaEncadeada</span>
        <div class="bloco-codigo">
            <pre><code>public class PilhaEncadeada
{
    private No topo;         // único ponto de acesso da pilha
    private int quantidade;  // controla o tamanho sem percorrer os nós

    public PilhaEncadeada()
    {
        topo       = null;   // pilha nasce vazia
        quantidade = 0;
    }

    public int Quantidade
    {
        get { return quantidade; }
    }
}</code></pre>
        </div>

        <p class="explica-codigo">
            Manter o campo <code>quantidade</code> atualizado a cada inserção e
            remoção evita percorrer a corrente de nós só para contar elementos:
            saber o tamanho passa a custar um acesso direto em vez de uma
            varredura completa. O campo <code>topo</code> é privado justamente
            para impedir que código externo alcance o meio da pilha e quebre a
            regra LIFO.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 5 — Push
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Operação Push — empilhar</h2>

        <p>
            O <strong>Push</strong> insere um novo elemento no topo da pilha.
            A ordem dos comandos importa: primeiro o novo nó passa a apontar para
            o topo atual, e só depois o topo é atualizado.
        </p>

        <span class="codigo-label">C# — Método Push</span>
        <div class="bloco-codigo">
            <pre><code>    public void Push(int dado)
    {
        No novo = new No(dado);

        novo.Proximo = topo;   // o novo nó passa a apontar para o antigo topo
        topo         = novo;   // agora o novo nó é o topo

        quantidade++;
    }</code></pre>
        </div>

        <p class="explica-codigo">
            Quando a pilha está vazia, <code>topo</code> vale <code>null</code> e
            a mesma linha <code>novo.Proximo = topo</code> já deixa o nó como base
            da pilha — por isso o método não precisa de um caso especial. Se as
            duas linhas fossem invertidas, o topo seria sobrescrito antes de ser
            guardado e todos os nós antigos ficariam inalcançáveis, perdendo a
            pilha inteira.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 6 — Pop
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Operação Pop — desempilhar</h2>

        <p>
            O <strong>Pop</strong> remove e devolve o elemento do topo. Antes de
            mexer nos ponteiros é preciso guardar o valor que será retornado, pois
            o nó sai da estrutura.
        </p>

        <span class="codigo-label">C# — Método Pop</span>
        <div class="bloco-codigo">
            <pre><code>    public int Pop()
    {
        if (topo == null)
        {
            throw new InvalidOperationException("A pilha está vazia.");
        }

        int valor = topo.Dado;   // guarda o dado antes de remover o nó

        topo = topo.Proximo;     // o nó de baixo vira o novo topo

        quantidade--;

        return valor;
    }</code></pre>
        </div>

        <p class="explica-codigo">
            A verificação inicial protege contra o <em>stack underflow</em>:
            tentar remover de uma pilha vazia é um erro de uso, e lançar a exceção
            deixa isso explícito em vez de devolver um valor inventado como zero.
            Ao reatribuir <code>topo</code>, o nó antigo deixa de ser referenciado
            e o coletor de lixo do .NET libera sua memória automaticamente — não
            existe <code>free</code> manual aqui.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 7 — Peek / Top
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Operação Peek (Top) — consultar o topo</h2>

        <p>
            O <strong>Peek</strong>, também chamado de <strong>Top</strong>,
            devolve o valor do topo <em>sem</em> removê-lo. É a operação usada
            quando se quer apenas olhar o que está por cima.
        </p>

        <span class="codigo-label">C# — Método Peek</span>
        <div class="bloco-codigo">
            <pre><code>    public int Peek()
    {
        if (topo == null)
        {
            throw new InvalidOperationException("A pilha está vazia.");
        }

        return topo.Dado;   // apenas lê; o topo continua no lugar
    }</code></pre>
        </div>

        <p class="explica-codigo">
            A diferença para o <code>Pop</code> é que nenhum ponteiro é alterado e
            <code>quantidade</code> permanece igual: a pilha sai da operação
            exatamente no mesmo estado em que entrou. Isso permite decidir o que
            fazer antes de remover — por exemplo, em um validador de parênteses,
            conferir se o símbolo do topo casa com o de fechamento e só então
            desempilhar.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 8 — IsEmpty
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Operação IsEmpty — verificar se está vazia</h2>

        <p>
            O <strong>IsEmpty</strong> informa se a pilha está vazia. Como o topo
            é a única porta de entrada, basta testar se ele é <code>null</code>.
        </p>

        <span class="codigo-label">C# — Método IsEmpty</span>
        <div class="bloco-codigo">
            <pre><code>    public bool IsEmpty()
    {
        return topo == null;   // sem topo, não há nenhum nó na pilha
    }</code></pre>
        </div>

        <p class="explica-codigo">
            Esse teste custa sempre o mesmo, independentemente do tamanho da
            pilha, porque não percorre nó nenhum. Na prática, ele é chamado antes
            de <code>Pop</code> e <code>Peek</code> para evitar a exceção:
            <code>while (!pilha.IsEmpty()) { ... pilha.Pop(); }</code> é o padrão
            mais comum de consumo de uma pilha.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 9 — Exemplo completo
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Exemplo completo em C#</h2>

        <p>
            Abaixo está a implementação completa usando nós encadeados, sem
            utilizar a classe pronta <code>Stack</code> do .NET. Todo o
            encadeamento é feito manualmente.
        </p>

        <span class="codigo-label">C# — Programa completo</span>
        <div class="bloco-codigo">
            <pre><code>using System;

public class No
{
    public int Dado;
    public No Proximo;

    public No(int dado)
    {
        Dado    = dado;
        Proximo = null;
    }
}

public class PilhaEncadeada
{
    private No topo;
    private int quantidade;

    public PilhaEncadeada()
    {
        topo       = null;
        quantidade = 0;
    }

    public int Quantidade
    {
        get { return quantidade; }
    }

    public bool IsEmpty()
    {
        return topo == null;
    }

    public void Push(int dado)
    {
        No novo = new No(dado);

        novo.Proximo = topo;
        topo         = novo;

        quantidade++;
    }

    public int Pop()
    {
        if (topo == null)
        {
            throw new InvalidOperationException("A pilha está vazia.");
        }

        int valor = topo.Dado;

        topo = topo.Proximo;

        quantidade--;

        return valor;
    }

    public int Peek()
    {
        if (topo == null)
        {
            throw new InvalidOperationException("A pilha está vazia.");
        }

        return topo.Dado;
    }

    public void Exibir()
    {
        No atual = topo;

        Console.Write("Topo -&gt; ");

        while (atual != null)
        {
            Console.Write(atual.Dado + " -&gt; ");
            atual = atual.Proximo;
        }

        Console.WriteLine("NULL");
    }
}

public class Program
{
    public static void Main()
    {
        PilhaEncadeada pilha = new PilhaEncadeada();

        Console.WriteLine("Vazia? " + pilha.IsEmpty());   // True

        pilha.Push(10);
        pilha.Push(20);
        pilha.Push(30);

        pilha.Exibir();                                   // Topo -&gt; 30 -&gt; 20 -&gt; 10 -&gt; NULL

        Console.WriteLine("Peek: " + pilha.Peek());       // 30
        Console.WriteLine("Pop:  " + pilha.Pop());        // 30
        Console.WriteLine("Pop:  " + pilha.Pop());        // 20

        pilha.Exibir();                                   // Topo -&gt; 10 -&gt; NULL

        Console.WriteLine("Quantidade: " + pilha.Quantidade);  // 1
        Console.WriteLine("Vazia? " + pilha.IsEmpty());        // False
    }
}</code></pre>
        </div>

        <p class="explica-codigo">
            O método <code>Exibir</code> é o único que percorre a estrutura: ele
            usa uma variável auxiliar <code>atual</code> para caminhar do topo até
            a base, sem mexer no campo <code>topo</code> — se ele próprio fosse
            usado como contador do laço, a pilha seria esvaziada só para imprimir.
            A saída sai do mais recente para o mais antigo, que é exatamente a
            ordem em que os elementos serão removidos.
        </p>

        <span class="codigo-label">Saída esperada no console</span>
        <div class="bloco-codigo">
            <pre><code>Vazia? True
Topo -&gt; 30 -&gt; 20 -&gt; 10 -&gt; NULL
Peek: 30
Pop:  30
Pop:  20
Topo -&gt; 10 -&gt; NULL
Quantidade: 1
Vazia? False</code></pre>
        </div>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 10 — Passo a passo
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Demonstração passo a passo</h2>

        <p>
            Acompanhe o estado da pilha ao executar
            <code>Push(10)</code>, <code>Push(20)</code>, <code>Push(30)</code> e,
            em seguida, um <code>Pop()</code>:
        </p>

        <figure class="secao-imagem">
            <img
                src="../imgs/pilha/figura2.png"
                alt="Sequência de cinco passos mostrando a pilha vazia, três operações Push e um Pop"
            >
            <figcaption>Figura 3 — Sequência de Push e Pop, passo a passo</figcaption>
        </figure>

        <ol class="passos">
            <li>
                <strong>Pilha vazia:</strong> <code>topo = null</code> e
                <code>quantidade = 0</code>. <code>IsEmpty()</code> devolve
                <code>true</code>.
            </li>
            <li>
                <strong>Push(10):</strong> o nó 10 é criado, seu
                <code>Proximo</code> recebe <code>null</code> (o topo antigo) e ele
                vira o topo. A pilha é <code>10</code>.
            </li>
            <li>
                <strong>Push(20):</strong> o nó 20 aponta para o nó 10 e assume o
                topo. A pilha é <code>20 → 10</code>.
            </li>
            <li>
                <strong>Push(30):</strong> o nó 30 aponta para o nó 20 e assume o
                topo. A pilha é <code>30 → 20 → 10</code> e
                <code>quantidade = 3</code>.
            </li>
            <li>
                <strong>Pop():</strong> o valor <code>30</code> é guardado, o topo
                passa a ser o nó 20 e o nó 30 é descartado. Sobra
                <code>20 → 10</code>. Um <code>Peek()</code> agora devolveria
                <code>20</code>.
            </li>
        </ol>

        <div class="destaque">
            <strong>Observe:</strong> os nós 10 e 20 nunca mudaram de lugar nem
            foram copiados. Só a referência <code>topo</code> se moveu — é isso que
            torna as operações da pilha encadeada tão baratas.
        </div>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 11 — Vantagens e desvantagens
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Vantagens e desvantagens</h2>

        <div class="colunas">

            <div class="coluna coluna-boa">
                <h3>Vantagens</h3>
                <ul>
                    <li>Tamanho dinâmico: cresce e diminui conforme o uso, sem limite fixo.</li>
                    <li><code>Push</code>, <code>Pop</code>, <code>Peek</code> e <code>IsEmpty</code> em tempo constante.</li>
                    <li>Não desperdiça memória reservando espaço que talvez nunca seja usado.</li>
                    <li>Não precisa redimensionar nem copiar elementos quando enche.</li>
                    <li>Interface simples: poucas operações, difícil usar errado.</li>
                </ul>
            </div>

            <div class="coluna coluna-ruim">
                <h3>Desvantagens</h3>
                <ul>
                    <li>Cada nó gasta memória extra com a referência <code>Proximo</code>.</li>
                    <li>Sem acesso por índice: chegar ao <em>n</em>-ésimo elemento exige desempilhar.</li>
                    <li>Nós espalhados na memória aproveitam menos a cache do processador.</li>
                    <li>Alocar um objeto a cada <code>Push</code> custa mais que escrever em um vetor.</li>
                    <li><code>Pop</code> e <code>Peek</code> em pilha vazia geram erro se não houver verificação.</li>
                </ul>
            </div>

        </div>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 12 — Complexidade
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Complexidade das operações</h2>

        <p>
            Considerando <em>n</em> como a quantidade de elementos na pilha:
        </p>

        <table class="tabela-complexidade">
            <thead>
                <tr>
                    <th>Operação</th>
                    <th>Complexidade</th>
                    <th>Por quê</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Push</td>
                    <td>O(1)</td>
                    <td>Cria um nó e troca duas referências, sem percorrer a pilha.</td>
                </tr>
                <tr>
                    <td>Pop</td>
                    <td>O(1)</td>
                    <td>Move o topo para o nó seguinte; nenhum outro nó é tocado.</td>
                </tr>
                <tr>
                    <td>Peek (Top)</td>
                    <td>O(1)</td>
                    <td>Apenas lê o dado do nó apontado pelo topo.</td>
                </tr>
                <tr>
                    <td>IsEmpty</td>
                    <td>O(1)</td>
                    <td>Uma única comparação do topo com <code>null</code>.</td>
                </tr>
                <tr>
                    <td>Exibir / percorrer</td>
                    <td>O(n)</td>
                    <td>Visita todos os nós, do topo até a base.</td>
                </tr>
                <tr>
                    <td>Buscar um valor</td>
                    <td>O(n)</td>
                    <td>Não há acesso direto: é preciso caminhar pela corrente de nós.</td>
                </tr>
                <tr>
                    <td>Espaço ocupado</td>
                    <td>O(n)</td>
                    <td>Um nó por elemento, mais a referência do topo.</td>
                </tr>
            </tbody>
        </table>

        <div class="destaque">
            <strong>Resumo:</strong> tudo o que define uma pilha é O(1). As únicas
            operações O(n) são as que violam o espírito da estrutura — buscar ou
            percorrer. Se o programa precisa muito disso, provavelmente a estrutura
            certa não é uma pilha.
        </div>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 13 — Aplicações reais
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Aplicações reais</h2>

        <div class="cartoes">

            <div class="cartao">
                <h3>Desfazer ações (Ctrl+Z)</h3>
                <p>
                    Cada alteração feita em um editor é empilhada. Ao desfazer,
                    o programa desempilha a última ação — exatamente a ordem
                    inversa em que foram realizadas.
                </p>
            </div>

            <div class="cartao">
                <h3>Histórico do navegador</h3>
                <p>
                    As páginas visitadas são empilhadas; o botão "voltar"
                    desempilha a última e exibe a anterior.
                </p>
            </div>

            <div class="cartao">
                <h3>Chamadas de funções</h3>
                <p>
                    A <em>call stack</em> empilha um registro por chamada com
                    parâmetros e endereço de retorno. Ao terminar, a função é
                    desempilhada e a execução volta para quem a chamou — inclusive
                    em recursão.
                </p>
            </div>

            <div class="cartao">
                <h3>Validação de símbolos</h3>
                <p>
                    Compiladores empilham cada símbolo de abertura
                    <code>( [ {</code> e, ao encontrar um de fechamento,
                    desempilham para conferir se o par casa.
                </p>
            </div>

        </div>

        <span class="codigo-label">C# — Validação de parênteses usando a pilha</span>
        <div class="bloco-codigo">
            <pre><code>public static bool Balanceado(string expressao)
{
    PilhaEncadeada pilha = new PilhaEncadeada();

    foreach (char c in expressao)
    {
        if (c == '(')
        {
            pilha.Push(1);            // empilha cada abertura
        }
        else if (c == ')')
        {
            if (pilha.IsEmpty())
            {
                return false;         // fechou sem ter aberto
            }

            pilha.Pop();              // casou com a abertura mais recente
        }
    }

    return pilha.IsEmpty();           // sobrou abertura? então não está balanceado
}</code></pre>
        </div>

        <p class="explica-codigo">
            O algoritmo aproveita duas propriedades da pilha ao mesmo tempo: o
            <code>Pop</code> sempre devolve a abertura <em>mais recente</em>, que é
            justamente a que deve casar com o fechamento encontrado, e o
            <code>IsEmpty</code> serve para dois diagnósticos diferentes — no meio
            do laço indica fechamento sobrando, e no <code>return</code> final
            indica abertura sobrando. Para <code>"(()"</code> o resultado é
            <code>false</code>, porque resta um nó na pilha ao fim da varredura.
        </p>
    </section>

    <hr class="divisor">


    <!-- ======================================
         SEÇÃO 14 — Conclusão
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Conclusão</h2>

        <p>
            A Pilha Encadeada mostra como uma restrição bem escolhida deixa uma
            estrutura mais forte: ao permitir inserção e remoção apenas no topo,
            todas as operações essenciais passam a custar tempo constante e o
            código fica curto e difícil de usar errado.
        </p>

        <p>
            Com apenas uma referência — o <code>topo</code> — e nós contendo
            <code>Dado</code> e <code>Proximo</code>, implementamos
            <code>Push</code>, <code>Pop</code>, <code>Peek</code> e
            <code>IsEmpty</code> sem depender de tamanho fixo nem de
            redimensionamento.
        </p>

        <p>
            Entender a pilha é entender o próprio funcionamento dos programas:
            chamadas de função, recursão, histórico e desfazer são todos o mesmo
            princípio LIFO em ação. Sempre que o problema pedir "voltar ao estado
            anterior" ou "tratar primeiro o mais recente", a pilha é a resposta
            natural.
        </p>
    </section>


</main>

<?php include 'footer.php'; ?>
</body>
</html>

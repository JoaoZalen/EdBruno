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
    <title>Árvore Patricia (Radix Tree) — ED Ensino</title>
</head>
<body>
<?php include 'header.php'; ?>

<!-- ========== BANNER DA PÁGINA ========== -->
<div class="pagina-banner">
    <h1>Árvore Patricia (Radix Tree)</h1>
    <p>
        Uma evolução otimizada da Árvore Trie. Em vez de gastar um nó para cada
        letra isolada, a <strong>Árvore Patricia</strong> compacta caminhos sem
        ramificações, armazenando <strong>trechos inteiros de texto (substrings)</strong>
        em um único nó.
    </p>
    <span class="banner-tag">Compressão de prefixos e economia de memória</span>
    <div class="banner-acoes">
        <a href="simulador.php?ed=arvore_patricia" class="btn btn-sucesso">Simular Árvore Patricia</a>
        <a href="home.php#trilha" class="btn btn-contorno">Ver trilha</a>
    </div>
</div>

<!-- ========== CONTEÚDO PRINCIPAL ========== -->
<main class="pagina-conteudo">

    <!-- ======================================
         SEÇÃO 1 — O que é a Árvore Patricia
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">O que é uma Árvore Patricia?</h2>

        <p>
            <strong>PATRICIA</strong> é uma sigla em inglês para <em>Practical Algorithm To
            Retrieve Information Coded In Alphanumeric</em>. Na prática, ela também é
            muito conhecida como <strong>Radix Tree</strong> (Árvore de Raiz) ou
            <strong>Trie Compactada</strong>.
        </p>

        <p>
            Na Árvore Trie tradicional, se você inserir a palavra <code>"COMPUTADOR"</code>,
            serão criados 10 nós encadeados um abaixo do outro, mesmo que nenhuma outra
            palavra comece com <code>"COMPU..."</code>. A Árvore Patricia resolve esse
            desperdício de memória <strong>fundindo nós que possuem apenas um filho</strong>.
        </p>

        <div class="destaque">
            <strong>Regra de Ouro:</strong> na Árvore Patricia, todo nó interno (exceto a raiz)
            possui <strong>pelo menos dois filhos</strong> ou marca o fim de uma palavra.
            Não existem "corredores" de nós solitários.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 2 — Trie Comum vs. Árvore Patricia
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Comparação visual: Trie vs. Patricia</h2>

        <p>
            Imagine armazenar as palavras <strong>CASA</strong>, <strong>CASACO</strong> e
            <strong>CARRO</strong>:
        </p>

        <div class="colunas">
            <div class="coluna coluna-ruim">
                <h3>Na Trie Tradicional (7 nós)</h3>
                <ul>
                    <li><code>C</code> &rarr; <code>A</code> (prefixo comum)</li>
                    <li>Ramo 1: <code>S</code> &rarr; <code>A</code> (fim) &rarr; <code>C</code> &rarr; <code>O</code> (fim)</li>
                    <li>Ramo 2: <code>R</code> &rarr; <code>R</code> &rarr; <code>O</code> (fim)</li>
                    <li><em>Muitos nós intermediários com apenas 1 filho.</em></li>
                </ul>
            </div>

            <div class="coluna coluna-boa">
                <h3>Na Árvore Patricia (5 nós)</h3>
                <ul>
                    <li><code>"CA"</code> (prefixo comum compactado em 1 nó)</li>
                    <li>Ramo 1: <code>"SA"</code> (fim) &rarr; <code>"CO"</code> (fim)</li>
                    <li>Ramo 2: <code>"RRO"</code> (fim)</li>
                    <li><em>Menos ponteiros alocados e menos saltos na memória.</em></li>
                </ul>
            </div>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 3 — Estrutura do Nó
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Estrutura do nó</h2>

        <p>
            Diferente da Trie onde cada aresta/nó representava apenas um <code>char</code>,
            na Árvore Patricia cada nó guarda um <strong>rótulo (string)</strong> contendo
            o trecho compactado e um dicionário de filhos indexado pela
            <strong>primeira letra</strong> de cada trecho.
        </p>

        <span class="codigo-label">C# — Classe NoPatricia e ArvorePatricia</span>
        <div class="bloco-codigo">
            <pre><code>using System;
using System.Collections.Generic;

public class NoPatricia
{
    public string Trecho;                            // Substring armazenada neste nó
    public Dictionary&lt;char, NoPatricia&gt; Filhos;      // Chave = 1ª letra do trecho filho
    public bool FimDePalavra;                        // Indica se uma palavra termina aqui

    public NoPatricia(string trecho = "", bool fim = false)
    {
        Trecho       = trecho;
        Filhos       = new Dictionary&lt;char, NoPatricia&gt;();
        FimDePalavra = fim;
    }
}

public class ArvorePatricia
{
    private NoPatricia raiz;

    public ArvorePatricia()
    {
        raiz = new NoPatricia();   // Raiz inicia vazia
    }
}</code></pre>
        </div>

        <p class="explica-codigo">
            Como dois filhos de um mesmo nó nunca começam com a mesma letra (senão
            estariam fundidos no mesmo prefixo), basta usar o primeiro caractere
            (<code>trecho[0]</code>) como chave no <code>Dictionary</code>.
        </p>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 4 — Inserção e Divisão de Nós (Split)
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Inserção e Divisão de Prefixos (Split)</h2>

        <p>
            A operação mais importante da Árvore Patricia acontece quando inserimos uma
            palavra que compartilha apenas <strong>parte</strong> do texto de um nó já existente.
            Nesse momento ocorre uma <strong>divisão (split)</strong>:
        </p>

        <ol class="passos">
            <li><strong>Sem correspondência:</strong> se nenhum filho começa com a primeira letra da palavra, cria-se um novo nó com a palavra inteira restante.</li>
            <li><strong>Correspondência total do trecho:</strong> se o trecho do nó cabe inteiro dentro da palavra, consome-se esse prefixo e continua descendo para os filhos.</li>
            <li><strong>Correspondência parcial (Split):</strong> exemplo — o nó guarda <code>"CASA"</code> e queremos inserir <code>"CARRO"</code>. O prefixo comum é <code>"CA"</code>. O nó atual passa a guardar apenas <code>"CA"</code> e ganha dois filhos: <code>"SA"</code> e <code>"RRO"</code>.</li>
        </ol>

        <span class="codigo-label">C# — Método Inserir com divisão de prefixos</span>
        <div class="bloco-codigo">
            <pre><code>    public void Inserir(string palavra)
    {
        palavra = palavra.ToLower();
        NoPatricia atual = raiz;
        int i = 0;

        while (i &lt; palavra.Length)
        {
            char primeiraLetra = palavra[i];

            // Caso 1: Não existe caminho com essa letra -> cria nó com o resto da palavra
            if (!atual.Filhos.ContainsKey(primeiraLetra))
            {
                atual.Filhos[primeiraLetra] = new NoPatricia(palavra.Substring(i), true);
                return;
            }

            NoPatricia filho = atual.Filhos[primeiraLetra];
            string trecho = filho.Trecho;

            // Descobre quantos caracteres coincidem entre a palavra restante e o trecho do nó
            int j = 0;
            while (j &lt; trecho.Length &amp;&amp; (i + j) &lt; palavra.Length &amp;&amp; trecho[j] == palavra[i + j])
            {
                j++;
            }

            // Caso 2: O trecho inteiro do nó coincidiu -> avança para o próximo nível
            if (j == trecho.Length)
            {
                atual = filho;
                i += j;
                if (i == palavra.Length)
                {
                    atual.FimDePalavra = true;
                }
            }
            else
            {
                // Caso 3: Coincidência parcial -> divide (split) o nó existente
                string prefixoComum = trecho.Substring(0, j);
                string restoTrecho  = trecho.Substring(j);
                string restoPalavra = palavra.Substring(i + j);

                // Nó intermediário fica com o prefixo comum
                NoPatricia noDividido = new NoPatricia(prefixoComum, restoPalavra.Length == 0);
                atual.Filhos[primeiraLetra] = noDividido;

                // O antigo filho passa a guardar apenas o sufixo restante
                filho.Trecho = restoTrecho;
                noDividido.Filhos[restoTrecho[0]] = filho;

                // Se ainda sobrou texto da nova palavra, cria o segundo ramo
                if (restoPalavra.Length &gt; 0)
                {
                    noDividido.Filhos[restoPalavra[0]] = new NoPatricia(restoPalavra, true);
                }
                return;
            }
        }
    }</code></pre>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 5 — Busca
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Busca</h2>

        <p>
            Na busca, caminhamos comparando blocos de caracteres (<code>StartsWith</code>)
            em vez de uma letra por vez, o que reduz drasticamente o número de nós visitados:
        </p>

        <span class="codigo-label">C# — Método Buscar</span>
        <div class="bloco-codigo">
            <pre><code>    public bool Buscar(string palavra)
    {
        palavra = palavra.ToLower();
        NoPatricia atual = raiz;
        int i = 0;

        while (i &lt; palavra.Length)
        {
            char c = palavra[i];
            if (!atual.Filhos.ContainsKey(c))
                return false;

            NoPatricia filho = atual.Filhos[c];
            string restante = palavra.Substring(i);

            // Verifica se o restante da palavra começa exatamente com o trecho do nó
            if (!restante.StartsWith(filho.Trecho))
                return false;

            i += filho.Trecho.Length;
            atual = filho;
        }

        return atual.FimDePalavra;
    }</code></pre>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 6 — Complexidade
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Complexidade</h2>

        <p>
            Considerando <strong>$m$</strong> o comprimento da palavra e <strong>$n$</strong>
            o total de palavras armazenadas:
        </p>

        <table class="tabela-complexidade">
            <thead>
                <tr>
                    <th>Operação</th>
                    <th>Complexidade de Tempo</th>
                    <th>Espaço (Nós criados)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Inserir</td><td>O(m)</td><td>No máximo $2n$ nós no total</td></tr>
                <tr><td>Buscar palavra</td><td>O(m)</td><td>Menos saltos de ponteiros que a Trie</td></tr>
                <tr><td>Buscar prefixo</td><td>O(m)</td><td>Percorre apenas os blocos necessários</td></tr>
                <tr><td>Remover</td><td>O(m)</td><td>Funde nós novamente se ficar com 1 filho</td></tr>
            </tbody>
        </table>

        <div class="destaque">
            <strong>Limite garantido de nós:</strong> enquanto uma Trie comum pode criar
            centenas de nós para poucas palavras longas, uma Árvore Patricia com $n$
            palavras terá <strong>no máximo $2n - 1$ nós</strong>.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 7 — Aplicações Reais
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Aplicações reais</h2>

        <div class="cartoes">
            <div class="cartao">
                <h3>Roteadores e Tabelas IP</h3>
                <p>O kernel do Linux e roteadores de internet usam Radix Trees para armazenar rotas CIDR e encontrar o destino de pacotes em nanossegundos.</p>
            </div>
            <div class="cartao">
                <h3>Roteamento de APIs Web</h3>
                <p>Frameworks modernos (como Go Fiber, Fastify e Gin) usam Árvores Patricia para mapear URLs como <code>/usuarios/:id/perfil</code>.</p>
            </div>
            <div class="cartao">
                <h3>Sistemas de Arquivos e Bancos</h3>
                <p>Indexação em memória (como o índice ART — <em>Adaptive Radix Tree</em>) usado em bancos de dados modernos de alta velocidade.</p>
            </div>
            <div class="cartao">
                <h3>Dicionários Compactos</h3>
                <p>Armazenamento de grandes vocabulários em dispositivos com pouca memória RAM.</p>
            </div>
        </div>
    </section>

    <aside class="chamada-simulador">
        <div>
            <h3>Agora veja na prática</h3>
            <p>Insira palavras na Árvore Patricia e veja os nós se dividirem e compactarem automaticamente.</p>
        </div>
        <a href="simulador.php?ed=arvore_patricia" class="btn btn-sucesso">Abrir simulador</a>
    </aside>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
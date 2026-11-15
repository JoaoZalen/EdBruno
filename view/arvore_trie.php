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
    <title>Árvore Trie (Árvore de Prefixos) — ED Ensino</title>
</head>
<body>
<?php include 'header.php'; ?>

<!-- ========== BANNER DA PÁGINA ========== -->
<div class="pagina-banner">
    <h1>Árvore Trie</h1>
    <p>
        Enquanto a árvore binária compara valores inteiros a cada nó, a
        <strong>Trie</strong> (do inglês <em>re<strong>trie</strong>val</em>)
        guarda palavras quebrando-as <strong>caractere por caractere</strong>.
        Palavras com o mesmo começo compartilham o mesmo caminho na árvore.
    </p>
    <span class="banner-tag">Busca e autocompletar por prefixos</span>
    <div class="banner-acoes">
        <a href="simulador.php?ed=arvore_trie" class="btn btn-sucesso">Simular Árvore Trie</a>
        <a href="home.php#trilha" class="btn btn-contorno">Ver trilha</a>
    </div>
</div>


<!-- ========== CONTEÚDO PRINCIPAL ========== -->
<main class="pagina-conteudo">

    <!-- ======================================
         SEÇÃO 1 — O que é uma Árvore Trie
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">O que é uma Árvore Trie?</h2>

        <p>
            A <strong>Trie</strong> — também chamada de <strong>árvore de prefixos</strong>
            ou <strong>árvore digital</strong> — é uma árvore especializada em armazenar
            conjuntos de strings (palavras, códigos, nomes). Em vez de guardar uma palavra
            inteira dentro de um único nó, cada passo descendo na árvore representa
            <strong>uma letra</strong>.
        </p>

        <p>
            O grande diferencial é que palavras que começam iguais dividem os mesmos nós
            iniciais. Por exemplo, <code>"CASA"</code>, <code>"CASACO"</code> e
            <code>"CARRO"</code> compartilham o prefixo <code>"CA"</code> antes de se
            ramificarem.
        </p>

        <div class="destaque">
            <strong>Ponto-chave:</strong> o tempo para encontrar uma palavra em uma Trie
            depende apenas do <strong>tamanho da palavra</strong> ($m$), e não de quantas
            milhares de palavras estão cadastradas na árvore.
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 2 — Vocabulário
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Vocabulário da Trie</h2>

        <p>Antes de partir para o código, vale fixar os conceitos específicos dessa estrutura:</p>

        <div class="cartoes">
            <div class="cartao">
                <h3>Raiz vazia</h3>
                <p>O nó raiz não guarda nenhuma letra — ele funciona apenas como o ponto de partida para todas as palavras.</p>
            </div>
            <div class="cartao">
                <h3>Prefixo</h3>
                <p>Qualquer sequência inicial de letras. Todo caminho descendo a partir da raiz forma um prefixo válido.</p>
            </div>
            <div class="cartao">
                <h3>Fim de palavra</h3>
                <p>Uma marcação booleana (<code>FimDePalavra</code>) que indica se o caminho até aquele nó forma uma palavra completa.</p>
            </div>
            <div class="cartao">
                <h3>Ramificação (Alfabeto)</h3>
                <p>Diferente da binária (máximo 2 filhos), cada nó da Trie pode ter até 26 filhos (uma saída para cada letra de A a Z).</p>
            </div>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 3 — Como ela organiza as palavras
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Como funciona na prática</h2>

        <p>
            Imagine inserir as palavras <strong>CASA</strong>, <strong>CASO</strong> e
            <strong>CÃO</strong> em uma Trie vazia:
        </p>

        <ul>
            <li>A raiz aponta para o nó <strong>C</strong>;</li>
            <li>O nó <strong>C</strong> aponta para o nó <strong>A</strong> (prefixo comum <code>"CA"</code>);</li>
            <li>A partir de <strong>A</strong>, o caminho se divide: vai para <strong>S</strong> (levando a <strong>A</strong> e <strong>O</strong>) e vai para <strong>O</strong> (formando <code>"CAO"</code>);</li>
            <li>Os nós finais recebem uma marcação especial indicando que ali termina uma palavra cadastrada.</li>
        </ul>

        <figure class="secao-imagem">
            <img
                src="../imgs/trie/figura1.svg"
                alt="Árvore Trie com raiz vazia conectada a C, depois A, ramificando para S (com filhos A e O marcados como fim de palavra) e O (marcado como fim de palavra)"
            >
            <figcaption>Figura 1 — Árvore Trie armazenando as palavras CASA, CASO e CAO (nós destacados indicam fim de palavra)</figcaption>
        </figure>

        <div class="destaque">
            <strong>Por que a marcação de fim é obrigatória:</strong> se cadastrarmos apenas
            <code>"CASACO"</code>, os nós <code>C -&gt; A -&gt; S -&gt; A</code> existem na
            árvore. Sem a bandeira <code>FimDePalavra</code> no segundo <code>A</code>, a
            Trie acharia que <code>"CASA"</code> também foi inserida!
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 4 — Estrutura do nó
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Estrutura do nó</h2>

        <p>
            Para representar os 26 possíveis filhos (letras de <code>'a'</code> a
            <code>'z'</code>), a forma mais direta e rápida em C# é usar um
            <strong>vetor de 26 posições</strong> (ou um <code>Dictionary&lt;char, NoTrie&gt;</code>
            quando houver acentos e símbolos).
        </p>

        <span class="codigo-label">C# — Classe NoTrie e classe ArvoreTrie</span>
        <div class="bloco-codigo">
            <pre><code>public class NoTrie
{
    public NoTrie[] Filhos;      // 26 posições: índice 0 = 'a', 1 = 'b' ... 25 = 'z'
    public bool FimDePalavra;    // true se uma palavra termina exatamente aqui

    public NoTrie()
    {
        Filhos       = new NoTrie[26];
        FimDePalavra = false;
    }
}

public class ArvoreTrie
{
    private NoTrie raiz;

    public ArvoreTrie()
    {
        raiz = new NoTrie();     // raiz nasce vazia, sem representar nenhuma letra
    }
}</code></pre>
        </div>

        <p class="explica-codigo">
            Repare no truque matemático: em C#, subtrair <code>'a'</code> de qualquer letra
            minúscula converte o caractere no índice exato do vetor (<code>'a' - 'a' = 0</code>,
            <code>'b' - 'a' = 1</code>, <code>'z' - 'a' = 25</code>).
        </p>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 5 — Inserção
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Inserção</h2>

        <p>
            Para inserir uma palavra, começamos na raiz e caminhamos letra por letra. Se
            o caminho para a letra atual ainda não existe (<code>null</code>), criamos um
            novo nó; se já existe, apenas avançamos por ele. Ao chegar na última letra,
            marcamos <code>FimDePalavra = true</code>.
        </p>

        <span class="codigo-label">C# — Método Inserir</span>
        <div class="bloco-codigo">
            <pre><code>    public void Inserir(string palavra)
    {
        NoTrie atual = raiz;
        palavra = palavra.ToLower();

        foreach (char c in palavra)
        {
            int indice = c - 'a';

            if (atual.Filhos[indice] == null)
            {
                atual.Filhos[indice] = new NoTrie();   // cria o ramo se não existir
            }

            atual = atual.Filhos[indice];              // desce para o próximo nó
        }

        atual.FimDePalavra = true;                     // marca o término da palavra
    }</code></pre>
        </div>

        <p>Exemplo: inserindo <code>"CASA"</code> em uma Trie vazia.</p>

        <ol class="passos">
            <li><strong>Letra 'c' (índice 2):</strong> <code>raiz.Filhos[2]</code> é <code>null</code>, então cria o nó e desce para ele.</li>
            <li><strong>Letra 'a' (índice 0):</strong> cria o filho no índice 0 e desce.</li>
            <li><strong>Letra 's' (índice 18):</strong> cria o filho no índice 18 e desce.</li>
            <li><strong>Letra 'a' (índice 0):</strong> cria o filho no índice 0, desce e define <code>FimDePalavra = true</code>.</li>
        </ol>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 6 — Busca e Prefixos
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Busca exata e busca por prefixo</h2>

        <p>
            A Trie oferece duas consultas muito úteis: verificar se uma <strong>palavra
            inteira</strong> existe e verificar se existe alguma palavra que
            <strong>começa com determinado prefixo</strong> (base do autocompletar).
        </p>

        <span class="codigo-label">C# — Métodos Buscar e ComecaCom</span>
        <div class="bloco-codigo">
            <pre><code>    // Retorna true somente se a palavra inteira foi cadastrada
    public bool Buscar(string palavra)
    {
        NoTrie no = PercorrerPrefixo(palavra);
        return no != null &amp;&amp; no.FimDePalavra;
    }

    // Retorna true se existir qualquer palavra iniciada por esse prefixo
    public bool ComecaCom(string prefixo)
    {
        return PercorrerPrefixo(prefixo) != null;
    }

    // Método auxiliar que caminha pelos caracteres
    private NoTrie PercorrerPrefixo(string texto)
    {
        NoTrie atual = raiz;
        texto = texto.ToLower();

        foreach (char c in texto)
        {
            int indice = c - 'a';
            if (atual.Filhos[indice] == null)
                return null;                           // caminho interrompido

            atual = atual.Filhos[indice];
        }
        return atual;
    }</code></pre>
        </div>

        <div class="destaque">
            <strong>Diferença na prática:</strong> se apenas <code>"COMPUTADOR"</code> está
            na Trie, <code>ComecaCom("COMP")</code> retorna <code>true</code>, enquanto
            <code>Buscar("COMP")</code> retorna <code>false</code> (pois <code>FimDePalavra</code>
            no nó <code>'P'</code> é falso).
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 7 — Autocompletar (Listando sugestões)
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Autocompletar: listando palavras por prefixo</h2>

        <p>
            Para sugerir palavras enquanto o usuário digita, primeiro caminhamos até o
            final do prefixo digitado. A partir desse nó, fazemos uma busca recursiva
            (DFS) acumulando as letras sempre que encontrarmos <code>FimDePalavra == true</code>.
        </p>

        <span class="codigo-label">C# — Sugerir palavras a partir de um prefixo</span>
        <div class="bloco-codigo">
            <pre><code>    public List&lt;string&gt; SugerirPalavras(string prefixo)
    {
        List&lt;string&gt; resultados = new List&lt;string&gt;();
        NoTrie noPrefixo = PercorrerPrefixo(prefixo);

        if (noPrefixo == null) return resultados;

        ColetarPalavras(noPrefixo, prefixo.ToLower(), resultados);
        return resultados;
    }

    private void ColetarPalavras(NoTrie no, string palavraAtual, List&lt;string&gt; lista)
    {
        if (no.FimDePalavra)
            lista.Add(palavraAtual);

        for (int i = 0; i &lt; 26; i++)
        {
            if (no.Filhos[i] != null)
            {
                char proximaLetra = (char)('a' + i);
                ColetarPalavras(no.Filhos[i], palavraAtual + proximaLetra, lista);
            }
        }
    }</code></pre>
        </div>

        <p class="explica-codigo">
            Como o laço <code>for</code> percorre os índices de <code>0</code> a
            <code>25</code> (de <code>'a'</code> até <code>'z'</code>), as sugestões já
            saem naturalmente em <strong>ordem alfabética</strong>!
        </p>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 8 — Remoção
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Remoção</h2>

        <p>
            Remover uma palavra exige cuidado para não apagar letras que pertencem a
            outras palavras que compartilham o mesmo prefixo. Existem dois cenários:
        </p>

        <ol class="passos">
            <li>
                <strong>A palavra é prefixo de outra maior</strong> (ex.: remover
                <code>"CASA"</code> quando <code>"CASACO"</code> existe): apenas
                desmarcamos <code>FimDePalavra = false</code>, mantendo os nós intactos.
            </li>
            <li>
                <strong>A palavra tem nós exclusivos</strong> (ex.: remover
                <code>"CASACO"</code> quando <code>"CASA"</code> existe): desmarcamos o
                fim e apagamos de baixo para cima apenas os nós <code>'O'</code> e
                <code>'C'</code>, parando ao encontrar o <code>'A'</code> de
                <code>"CASA"</code>.
            </li>
        </ol>

        <span class="codigo-label">C# — Método Remover (recursivo)</span>
        <div class="bloco-codigo">
            <pre><code>    public void Remover(string palavra)
    {
        RemoverRec(raiz, palavra.ToLower(), 0);
    }

    private bool RemoverRec(NoTrie atual, string palavra, int profundidade)
    {
        if (atual == null) return false;

        // Chegou ao último caractere da palavra
        if (profundidade == palavra.Length)
        {
            if (!atual.FimDePalavra) return false;   // palavra não existia
            atual.FimDePalavra = false;              // desmarca o fim
            return EstaVazio(atual);                 // pode apagar nó se não tiver filhos
        }

        int indice = palavra[profundidade] - 'a';
        if (RemoverRec(atual.Filhos[indice], palavra, profundidade + 1))
        {
            atual.Filhos[indice] = null;             // libera referência do filho
            return !atual.FimDePalavra &amp;&amp; EstaVazio(atual);
        }
        return false;
    }

    private bool EstaVazio(NoTrie no)
    {
        for (int i = 0; i &lt; 26; i++)
            if (no.Filhos[i] != null) return false;
        return true;
    }</code></pre>
        </div>
    </section>

    <hr class="divisor">

    <!-- ======================================
         SEÇÃO 9 — Complexidade
         ====================================== -->
    <section class="secao">
        <h2 class="secao-titulo">Complexidade</h2>

        <p>
            Nas tabelas abaixo, <strong>$m$</strong> representa o comprimento da palavra
            buscada ou inserida, e <strong>$n$</strong> é o total de palavras armazenadas:
        </p>

        <table class="tabela-complexidade">
            <thead>
                <tr>
                    <th>Operação</th>
                    <th>Complexidade de Tempo</th>
                    <th>Observação</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Inserir</td><td>O(m)</td><td>Percorre ou cria 1 nó por letra da palavra</td></tr>
                <tr><td>Buscar palavra</td><td>O(m)</td><td>Independente do total $n$ de palavras na árvore</td></tr>
                <tr><td>Buscar prefixo</td><td>O(m)</td><td>Para no último caractere do prefixo</td></tr>
                <tr><td>Remover</td><td>O(m)</td><td>Desce e sobe no máximo $m$ níveis</td></tr>
            </tbody>
        </table>

        <div class="destaque">
            <strong>Comparação com Tabela Hash:</strong> uma Hash também busca palavras em
            O(m), mas não consegue listar sugestões por prefixo nem entregar resultados em
            ordem alfabética sem percorrer toda a tabela.
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
                    <li>Busca muito rápida em O(m), sem colisões de hash.</li>
                    <li>Ideal para autocompletar e busca por prefixos.</li>
                    <li>Percurso em profundidade já entrega as palavras em ordem alfabética.</li>
                    <li>Economiza espaço entre palavras que compartilham prefixos longos.</li>
                </ul>
            </div>

            <div class="coluna coluna-ruim">
                <h3>Desvantagens</h3>
                <ul>
                    <li>Alto consumo de memória se usar vetores de 26 ponteiros em cada nó.</li>
                    <li>Pouco eficiente quando as palavras não compartilham prefixos.</li>
                    <li>Suportar acentos, números e maiúsculas exige estruturas maiores (como <code>Dictionary</code>).</li>
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
                <h3>Autocompletar de buscas</h3>
                <p>Barras de pesquisa (Google, lojas virtuais e IDEs) usam Tries para sugerir termos instantaneamente a cada tecla.</p>
            </div>
            <div class="cartao">
                <h3>Corretores ortográficos</h3>
                <p>Dicionários digitais verificam se uma palavra existe e sugerem palavras próximas navegando pelos prefixos.</p>
            </div>
            <div class="cartao">
                <h3>Roteamento de redes (IP)</h3>
                <p>Roteadores usam variações binárias de Trie (Radix Tree) para encontrar o prefixo de rede mais longo de um endereço IP.</p>
            </div>
            <div class="cartao">
                <h3>Agenda de contatos</h3>
                <p>Filtrar nomes ou números de telefone conforme os primeiros dígitos são pressionados no teclado.</p>
            </div>
        </div>
    </section>

    <aside class="chamada-simulador">
        <div>
            <h3>Agora veja na prática</h3>
            <p>Insira palavras, teste prefixos e acompanhe a criação dos nós na Árvore Trie.</p>
        </div>
        <a href="simulador.php?ed=arvore_trie" class="btn btn-sucesso">Abrir simulador</a>
    </aside>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
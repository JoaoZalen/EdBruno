<?php
include 'session.php';

/* Trilha de estudo: cada etapa agrupa conteúdos relacionados */
$trilha = [
    [
        'etapa'  => 'Fundamentos',
        'resumo' => 'Antes das estruturas, a ideia que organiza todas elas.',
        'itens'  => [
            ['pagina' => 'tad.php', 'sim' => null, 'titulo' => 'TAD — Tipo Abstrato de Dados',
             'texto' => 'Separar o que a estrutura faz de como ela é feita: abstração, encapsulamento e interface.',
             'tags' => ['Abstração', 'Interface']],
        ],
    ],
    [
        'etapa'  => 'Listas encadeadas',
        'resumo' => 'Nós ligados por ponteiros, a base de quase tudo que vem depois.',
        'itens'  => [
            ['pagina' => 'lista_simples.php', 'sim' => 'lista_simples', 'titulo' => 'Lista Simples',
             'texto' => 'Cada nó aponta para o próximo. Inserção, remoção e busca percorrendo a partir da cabeça.',
             'tags' => ['Nó', 'Cabeça', 'O(n)']],
            ['pagina' => 'lista_dupla.php', 'sim' => 'lista_dupla', 'titulo' => 'Lista Dupla',
             'texto' => 'Ponteiros para os dois lados: navegação reversa e remoção no fim em tempo constante.',
             'tags' => ['Anterior', 'Cauda', 'O(1)']],
        ],
    ],
    [
        'etapa'  => 'Pilhas e filas',
        'resumo' => 'Listas com regras de acesso que resolvem problemas do dia a dia.',
        'itens'  => [
            ['pagina' => 'pilha.php', 'sim' => 'pilha', 'titulo' => 'Pilha',
             'texto' => 'Tudo acontece no topo. Push, Pop e Peek, desfazer ações e chamadas de função.',
             'tags' => ['LIFO', 'Topo']],
            ['pagina' => 'fila_fifo.php', 'sim' => 'fila_fifo', 'titulo' => 'Fila FIFO',
             'texto' => 'Entra pelo fim, sai pelo início. Ponteiros de início e fim e ordem de chegada.',
             'tags' => ['FIFO', 'Início', 'Fim']],
            ['pagina' => 'fila_prioridade.php', 'sim' => 'fila_prioridade', 'titulo' => 'Fila de Prioridade',
             'texto' => 'A prioridade decide quem sai primeiro, sem quebrar a ordem de chegada nos empates.',
             'tags' => ['Prioridade', 'Inserção ordenada']],
        ],
    ],
    [
        'etapa'  => 'Árvores',
        'resumo' => 'Saindo da linha reta: dados em hierarquia para buscar muito mais rápido.',
        'itens'  => [
            ['pagina' => 'arvore_binaria.php', 'sim' => 'arvore_binaria', 'titulo' => 'Árvore Binária de Busca',
             'texto' => 'Menores à esquerda, maiores à direita. Inserção, busca, os três casos de remoção e os quatro percursos.',
             'tags' => ['Raiz', 'Folha', 'O(log n)']],
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início — ED Ensino</title>

    <link rel="stylesheet" href="../css/base.css?v=2">
    <link rel="stylesheet" href="../css/header.css?v=5">
    <link rel="stylesheet" href="../css/footer.css?v=3">
    <link rel="stylesheet" href="../css/home.css?v=3">
</head>
<body>

<?php include 'header.php'; ?>

<main>

    <!-- ========== HERO ========== -->
    <section class="hero">
        <div class="container hero-grade">

            <div class="hero-texto">
                <span class="eyebrow">Estruturas de Dados · C#</span>
                <h1>Aprenda estruturas de dados vendo cada <span>ponteiro</span> se mover.</h1>
                <p>
                    Teoria direta ao ponto, código comentado em C# e um simulador
                    que mostra passo a passo o que acontece na memória.
                </p>

                <div class="hero-botoes">
                    <a href="#trilha" class="btn btn-primario">Começar a trilha</a>
                    <a href="simulador.php" class="btn btn-contorno">Abrir simulador</a>
                </div>

                <ul class="hero-numeros">
                    <li><strong>7</strong> conteúdos</li>
                    <li><strong>6</strong> simulações</li>
                    <li><strong>Quiz</strong> com moedas</li>
                </ul>
            </div>

            <!-- Ilustração feita só com HTML/CSS: uma lista encadeada -->
            <div class="hero-visual" aria-hidden="true">
                <div class="visual-janela">
                    <div class="visual-barra">
                        <span></span><span></span><span></span>
                        <em>lista_simples.cs</em>
                    </div>

                    <div class="visual-lista">
                        <div class="visual-no visual-no--cabeca">
                            <small>cabeça</small>
                            <b>10</b><i></i>
                        </div>
                        <div class="visual-seta"></div>
                        <div class="visual-no visual-no--atual">
                            <small>atual</small>
                            <b>20</b><i></i>
                        </div>
                        <div class="visual-seta"></div>
                        <div class="visual-no visual-no--novo">
                            <small>novo</small>
                            <b>30</b><i>∅</i>
                        </div>
                    </div>

<pre class="visual-codigo"><span class="c">// insere no fim</span>
<span class="k">while</span> (atual.Proximo != <span class="k">null</span>)
    atual = atual.Proximo;
<span class="marca">atual.Proximo = novo;</span></pre>
                </div>
            </div>

        </div>
    </section>

    <!-- ========== TRILHA DE ESTUDO ========== -->
    <section class="secao-home" id="trilha">
        <div class="container">

            <header class="secao-cabecalho">
                <span class="eyebrow">Trilha de estudo</span>
                <h2>Siga a ordem e cada conteúdo prepara o próximo</h2>
            </header>

            <ol class="trilha">
                <?php foreach ($trilha as $i => $etapa) { ?>
                <li class="trilha-etapa">

                    <div class="trilha-marcador">
                        <span class="trilha-numero"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
                        <div>
                            <h3><?php echo $etapa['etapa']; ?></h3>
                            <p><?php echo $etapa['resumo']; ?></p>
                        </div>
                    </div>

                    <div class="trilha-cards">
                        <?php foreach ($etapa['itens'] as $item) { ?>
                        <article class="card-conteudo">
                            <h4><?php echo $item['titulo']; ?></h4>
                            <p><?php echo $item['texto']; ?></p>

                            <div class="card-tags">
                                <?php foreach ($item['tags'] as $tag) { ?>
                                    <span><?php echo $tag; ?></span>
                                <?php } ?>
                            </div>

                            <div class="card-acoes">
                                <a href="<?php echo $item['pagina']; ?>" class="card-link">Estudar &rarr;</a>
                                <?php if ($item['sim']) { ?>
                                    <a href="simulador.php?ed=<?php echo $item['sim']; ?>" class="card-link card-link--sim">Simular</a>
                                <?php } ?>
                            </div>
                        </article>
                        <?php } ?>
                    </div>

                </li>
                <?php } ?>
            </ol>

        </div>
    </section>

    <!-- ========== COMO ESTUDAR ========== -->
    <section class="secao-home secao-home--faixa">
        <div class="container">

            <header class="secao-cabecalho">
                <span class="eyebrow">Método</span>
                <h2>Três passos para fixar cada estrutura</h2>
            </header>

            <div class="metodo">
                <div class="metodo-passo metodo-passo--azul">
                    <span class="metodo-icone">1</span>
                    <h3>Leia a teoria</h3>
                    <p>Conceito, diagramas e o código em C# explicado linha por linha.</p>
                </div>
                <div class="metodo-passo metodo-passo--verde">
                    <span class="metodo-icone">2</span>
                    <h3>Simule</h3>
                    <p>Insira e remova valores e veja a estrutura se reorganizar em tempo real.</p>
                </div>
                <div class="metodo-passo metodo-passo--amarelo">
                    <span class="metodo-icone">3</span>
                    <h3>Teste no quiz</h3>
                    <p>Responda perguntas, ganhe moedas e personalize seu avatar na loja.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- ========== CHAMADA PARA O QUIZ ========== -->
    <section class="secao-home">
        <div class="container">
            <div class="cta">
                <div>
                    <span class="selo selo-amarelo">+ moedas a cada acerto</span>
                    <h2>Pronto para testar o que aprendeu?</h2>
                    <p>8 perguntas misturando teoria e código. Quanto mais difícil, mais moedas.</p>
                </div>
                <a href="quiz.php" class="btn btn-primario">Começar o quiz</a>
            </div>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>

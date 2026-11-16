<?php
/**
 * simulador.php — Simulador interativo de Estruturas de Dados
 * Caminho: view/simulador.php
 */
include 'session.php';

/* ---- Estruturas disponíveis ---- */
$estruturas = [
    'lista_simples' => [
        'aba'    => 'Lista Simples',
        'titulo' => 'Lista Simplesmente Encadeada',
        'regra'  => 'Acesso sequencial',
        'desc'   => 'Cada nó guarda um valor e aponta só para o próximo. Para chegar a qualquer posição é preciso partir da cabeça.',
        'pagina' => 'lista_simples.php',
    ],
    'lista_dupla' => [
        'aba'    => 'Lista Dupla',
        'titulo' => 'Lista Duplamente Encadeada',
        'regra'  => 'Dois sentidos',
        'desc'   => 'Cada nó aponta para o próximo (azul) e para o anterior (verde). Com a cauda, mexer no fim custa O(1).',
        'pagina' => 'lista_dupla.php',
    ],
    'pilha' => [
        'aba'    => 'Pilha',
        'titulo' => 'Pilha Encadeada',
        'regra'  => 'LIFO',
        'desc'   => 'Tudo acontece no topo: empilhar coloca um nó em cima, desempilhar tira o de cima.',
        'pagina' => 'pilha.php',
    ],
    'fila_fifo' => [
        'aba'    => 'Fila FIFO',
        'titulo' => 'Fila Encadeada (FIFO)',
        'regra'  => 'FIFO',
        'desc'   => 'Entra pelo fim, sai pelo início, como uma fila de banco.',
        'pagina' => 'fila_fifo.php',
    ],
    'fila_prioridade' => [
        'aba'    => 'Fila de Prioridade',
        'titulo' => 'Fila de Prioridade',
        'regra'  => 'Maior prioridade primeiro',
        'desc'   => 'Cada elemento tem prioridade de 1 a 5. A inserção encaixa o nó na posição certa; empates respeitam a ordem de chegada.',
        'pagina' => 'fila_prioridade.php',
    ],
    'arvore_binaria' => [
        'aba'    => 'Árvore Binária',
        'titulo' => 'Árvore Binária de Busca',
        'regra'  => 'Menores à esquerda',
        'desc'   => 'Cada nó tem até dois filhos: valores menores ficam à esquerda e maiores à direita. Assim a busca descarta metade da árvore a cada passo.',
        'pagina' => 'arvore_binaria.php',
    ],
    'arvore_patricia' => [
        'aba'    => 'Árvore Patricia',
        'titulo' => 'Árvore Patricia (Radix Tree)',
        'regra'  => 'Prefixos comprimidos',
        'desc'   => 'Versão compactada da Trie: nós com apenas um filho são fundidos em um único bloco de caracteres, economizando memória e saltos na busca.',
        'pagina' => 'arvore_patricia.php',
    ],
];

/* ---- Estrutura escolhida (só aceita as da lista acima) ---- */
$ed = isset($_GET['ed']) ? $_GET['ed'] : 'lista_simples';
if (!array_key_exists($ed, $estruturas)) {
    $ed = 'lista_simples';
}
$atual = $estruturas[$ed];

$ehLista      = ($ed === 'lista_simples' || $ed === 'lista_dupla');
$ehPrioridade = ($ed === 'fila_prioridade');
$ehTexto      = ($ed === 'arvore_trie' || $ed === 'arvore_patricia');
$ehArvore     = ($ed === 'arvore_binaria' || $ed === 'arvore_trie' || $ed === 'arvore_patricia');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador — <?php echo $atual['aba']; ?> | ED Ensino</title>

    <link rel="stylesheet" href="../css/base.css?v=2">
    <link rel="stylesheet" href="../css/header.css?v=5">
    <link rel="stylesheet" href="../css/simulador.css?v=4">
</head>
<body class="pagina-simulador">

<?php include 'header.php'; ?>

<main class="sim-app" id="simulador" data-estrutura="<?php echo $ed; ?>">

    <!-- ========== BARRA DE FERRAMENTAS ========== -->
    <div class="sim-barra">
        <nav class="sim-abas" aria-label="Estruturas">
            <?php foreach ($estruturas as $chave => $e) { ?>
                <a href="simulador.php?ed=<?php echo $chave; ?>"
                   class="sim-aba <?php echo $chave === $ed ? 'ativa' : ''; ?>">
                    <?php echo $e['aba']; ?>
                </a>
            <?php } ?>
        </nav>

        <div class="sim-ferramentas">
            <button type="button" class="sim-ferramenta" id="sim-zoom-menos" title="Diminuir zoom">−</button>
            <button type="button" class="sim-ferramenta" id="sim-zoom-mais" title="Aumentar zoom">+</button>
            <button type="button" class="sim-ferramenta sim-ferramenta--texto" id="sim-ajustar" title="Enquadrar a estrutura">Ajustar</button>
            <button type="button" class="sim-ferramenta sim-ferramenta--texto" id="sim-reorganizar" title="Devolver os nós arrastados ao lugar">Reorganizar</button>
            <span class="sim-separador"></span>
            <label class="sim-interruptor" title="Avança um passo por vez (barra de espaço)">
                <input type="checkbox" id="sim-passo-a-passo">
                <span>Passo a passo</span>
            </label>
            <span class="sim-separador"></span>
            <button type="button" class="sim-ferramenta sim-ferramenta--texto" id="sim-painel-botao" title="Mostrar/ocultar o painel">Painel</button>
            <button type="button" class="sim-ferramenta sim-ferramenta--destaque" id="sim-tela-cheia" title="Tela cheia (F)">Tela cheia</button>
        </div>
    </div>

    <!-- ========== CORPO: PALCO + PAINEL ========== -->
    <div class="sim-corpo">

        <!-- ---- Palco ---- -->
        <section class="sim-palco" id="sim-palco" aria-label="Área da simulação">

            <div class="sim-info" id="sim-info"></div>

            <div class="sim-dica" id="sim-dica">
                <span><b>Arraste</b> os nós</span>
                <span><b>Botão direito</b> para opções</span>
                <span><b>Roda do mouse</b> para zoom</span>
                <span><b>Arraste o fundo</b> para mover</span>
                <button type="button" id="sim-dica-fechar" aria-label="Fechar dica">×</button>
            </div>

            <div class="sim-mundo" id="sim-mundo">
                <svg class="sim-links" id="sim-links" aria-hidden="true">
                    <defs>
                        <marker id="seta-azul" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M0,0 L10,5 L0,10 z" fill="#2563eb"/>
                        </marker>
                        <marker id="seta-verde" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M0,0 L10,5 L0,10 z" fill="#059669"/>
                        </marker>
                        <marker id="seta-amarela" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M0,0 L10,5 L0,10 z" fill="#ca8a04"/>
                        </marker>
                    </defs>
                </svg>
                <div class="sim-nos" id="sim-nos"></div>
            </div>

            <p class="sim-vazio" id="sim-vazio">
                Estrutura vazia.<br>
                <small>Use o painel ou clique com o botão direito aqui para adicionar.</small>
            </p>

            <div class="sim-narracao" id="sim-narracao" aria-live="polite">
                <span class="sim-narracao-texto" id="sim-narracao-texto">Pronto. Escolha uma operação no painel.</span>
                <button type="button" class="sim-proximo" id="sim-proximo" hidden>Próximo passo ▶</button>
            </div>
        </section>

        <!-- ---- Painel lateral ---- -->
        <aside class="sim-painel" id="sim-painel">

            <section class="sim-card sim-card--intro">
                <div class="sim-card-topo">
                    <h1><?php echo $atual['titulo']; ?></h1>
                    <span class="sim-regra"><?php echo $atual['regra']; ?></span>
                </div>
                <p><?php echo $atual['desc']; ?></p>
            </section>

            <section class="sim-card">
                <h2>Operações</h2>

                <div class="sim-campos">
                    <?php if ($ehTexto) { ?>
                    <label>
                        Palavra / Prefixo
                        <input type="text" id="sim-valor" maxlength="12" placeholder="aleatória (A-Z)">
                    </label>
                    <?php } else { ?>
                    <label>
                        Valor
                        <input type="number" id="sim-valor" min="0" max="999" placeholder="aleatório">
                    </label>
                    <?php } ?>

                    <?php if ($ehLista) { ?>
                    <label>
                        Posição
                        <input type="number" id="sim-posicao" min="0" placeholder="ex: 2">
                    </label>
                    <?php } ?>

                    <?php if ($ehPrioridade) { ?>
                    <label>
                        Prioridade
                        <select id="sim-prioridade">
                            <option value="1">1 — baixa</option>
                            <option value="2">2</option>
                            <option value="3" selected>3 — média</option>
                            <option value="4">4</option>
                            <option value="5">5 — alta</option>
                        </select>
                    </label>
                    <?php } ?>
                </div>

                <!-- Os botões de operação são criados pelo simulador.js -->
                <div class="sim-operacoes" id="sim-operacoes"></div>

                <div class="sim-extras">
                    <button type="button" class="sim-btn sim-btn--fantasma" id="sim-aleatorio">Gerar aleatória</button>
                    <button type="button" class="sim-btn sim-btn--fantasma" id="sim-limpar">Limpar</button>
                </div>

                <label class="sim-velocidade">
                    <span>Velocidade</span>
                    <input type="range" id="sim-velocidade" min="1" max="5" value="3">
                </label>
            </section>

            <section class="sim-card">
                <h2 id="sim-codigo-titulo">Código em C#</h2>
                <pre class="sim-codigo" id="sim-codigo"><span class="sim-codigo-dica">// execute uma operação para ver o código</span></pre>
            </section>

            <section class="sim-card">
                <h2>Legenda</h2>
                <ul class="sim-legenda">
                    <li><span class="cor cor-atual"></span> visitando (atual)</li>
                    <li><span class="cor cor-novo"></span> nó novo / dividido</li>
                    <li><span class="cor cor-alvo"></span> sendo removido</li>
                    <li><span class="cor cor-achado"></span> encontrado / fim de palavra</li>
                    <li><span class="linha linha-azul"></span> ponteiro <?php echo $ehArvore ? 'para os filhos' : 'Próximo'; ?></li>
                    <?php if ($ed === 'lista_dupla') { ?>
                    <li><span class="linha linha-verde"></span> ponteiro Anterior</li>
                    <?php } ?>
                </ul>
            </section>

            <section class="sim-card">
                <h2>Histórico</h2>
                <ol class="sim-log" id="sim-log"></ol>
            </section>

            <a class="sim-teoria" href="<?php echo $atual['pagina']; ?>">
                Ler a teoria de <?php echo $atual['aba']; ?> &rarr;
            </a>
        </aside>

    </div>

    <div class="sim-menu" id="sim-menu" role="menu" hidden></div>

</main>

<script src="../js/simulador.js?v=4"></script>
</body>
</html>
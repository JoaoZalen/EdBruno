<?php
// Perguntas independentes sobre Fila Encadeada FIFO desenvolvidas por José Ricardo

$perguntas_fila_fifo = [
    [
        'enunciado' => 'O que significa a sigla FIFO no contexto de estrutura de dados?',
        'alternativas' => [
            'A' => 'First-In, First-Out (O primeiro a entrar é o primeiro a sair).',
            'B' => 'Fast-In, Fast-Out (Entrada e saída rápidas sem ordem).',
            'C' => 'First-In, Last-Out (O primeiro a entrar é o último a sair).',
            'D' => 'Final-In, First-Out (O último a entrar é o primeiro a sair).'
        ],
        'resposta_correta' => 'A',
        'dificuldade' => 'facil',
        'explicacao' => 'FIFO significa First-In, First-Out: os elementos são processados rigorosamente na ordem de chegada.'
    ],
    [
        'enunciado' => 'Em uma Fila Encadeada FIFO, em quais extremidades ocorrem, respectivamente, a inserção e a remoção?',
        'alternativas' => [
            'A' => 'A inserção ocorre no início e a remoção no final.',
            'B' => 'A inserção ocorre no final e a remoção no início.',
            'C' => 'Tanto a inserção quanto a remoção ocorrem no início.',
            'D' => 'Tanto a inserção quanto a remoção ocorrem no final.'
        ],
        'resposta_correta' => 'B',
        'dificuldade' => 'facil',
        'explicacao' => 'Novos elementos entram no final (Enqueue) e os elementos antigos saem pelo início (Dequeue).'
    ],
    [
        'enunciado' => 'Qual é a complexidade de tempo das operações Enqueue e Dequeue em uma Fila Encadeada bem implementada?',
        'alternativas' => [
            'A' => 'O(n) para ambas.',
            'B' => 'O(1) para Enqueue e O(n) para Dequeue.',
            'C' => 'O(1) para ambas.',
            'D' => 'O(log n) para ambas.'
        ],
        'resposta_correta' => 'C',
        'dificuldade' => 'media',
        'explicacao' => 'Ao manter ponteiros para o início e o fim da fila, as operações de inserção e remoção executam em tempo constante O(1).'
    ],
    [
        'enunciado' => 'O que acontece com o ponteiro "fim" (rear) quando a remoção (Dequeue) faz a fila ficar totalmente vazia?',
        'alternativas' => [
            'A' => 'Ele continua apontando para o nó removido.',
            'B' => 'Ele passa a apontar para o início automaticamente.',
            'C' => 'Ele deve ser ajustado explicitamente para null.',
            'D' => 'Ele lança uma exceção de memória.'
        ],
        'resposta_correta' => 'C',
        'dificuldade' => 'media',
        'explicacao' => 'Quando o início passa a ser null após um Dequeue, o ponteiro de fim também deve ser ajustado para null para manter a estrutura consistente.'
    ],
    [
        'enunciado' => 'Considere o trecho de código C#:\n\nFilaEncadeada<int> f = new FilaEncadeada<int>();\nf.Enqueue(5);\nf.Enqueue(10);\nf.Enqueue(15);\nf.Dequeue();\nConsole.WriteLine(f.Peek());\n\nQual valor será exibido no console?',
        'alternativas' => [
            'A' => '5',
            'B' => '10',
            'C' => '15',
            'D' => '0'
        ],
        'resposta_correta' => 'B',
        'dificuldade' => 'media',
        'explicacao' => 'Após enfileirar 5, 10 e 15, a fila fica [5, 10, 15]. O Dequeue remove o 5. O novo início passa a ser 10, que é retornado pelo Peek().'
    ],
    [
        'enunciado' => 'Qual é a principal vantagem de uma Fila Encadeada em relação a uma Fila baseada em Vetor Estático (Array)?',
        'alternativas' => [
            'A' => 'Consome menos memória total por não usar ponteiros.',
            'B' => 'Permite alocação dinâmica, aumentando ou diminuindo de tamanho sem limite pré-definido.',
            'C' => 'Permite acesso aleatório rápido por índice.',
            'D' => 'Garante ordenação automática dos dados inseridos.'
        ],
        'resposta_correta' => 'B',
        'dificuldade' => 'facil',
        'explicacao' => 'A Fila Encadeada aloca memória dinamicamente para cada nó conforme necessário, sem precisar definir um tamanho máximo inicial.'
    ],
    [
        'enunciado' => 'Análise o código C# a seguir:\n\nif (inicio == null) {\n    inicio = novoNo;\n    fim = novoNo;\n}\n\nEste trecho de código é executado em qual operação da Fila?',
        'alternativas' => [
            'A' => 'Dequeue em uma fila cheia.',
            'B' => 'Enqueue em uma fila que estava vazia.',
            'C' => 'Peek em uma fila com múltiplos elementos.',
            'D' => 'IsEmpty ao verificar o estado da fila.'
        ],
        'resposta_correta' => 'B',
        'dificuldade' => 'media',
        'explicacao' => 'Ao realizar o Enqueue em uma fila vazia (inicio == null), tanto o ponteiro inicio quanto o fim devem passar a apontar para o novo nó.'
    ],
    [
        'enunciado' => 'Qual das opções abaixo NÃO é um exemplo típico de aplicação de uma Fila FIFO?',
        'alternativas' => [
            'A' => 'Gerenciamento de impressões de documentos.',
            'B' => 'Recuperação do histórico de páginas navegadas com botão "Voltar" do browser.',
            'C' => 'Escalonamento de processos FCFS em sistemas operacionais.',
            'D' => 'Processamento de mensagens em sistemas de mensageria.'
        ],
        'resposta_correta' => 'B',
        'dificuldade' => 'dificil',
        'explicacao' => 'O botão "Voltar" do navegador utiliza o princípio LIFO (Pilha), pois a última página visitada é a primeira a retornar.'
    ],
    [
        'enunciado' => 'Se uma Fila Encadeada FIFO executa a seguinte sequência de operações: Enqueue(A), Enqueue(B), Dequeue(), Enqueue(C), Dequeue(). Qual elemento permanece na fila?',
        'alternativas' => [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'A fila fica vazia.'
        ],
        'resposta_correta' => 'C',
        'dificuldade' => 'dificil',
        'explicacao' => '1. Enqueue(A): [A]\n2. Enqueue(B): [A, B]\n3. Dequeue(): remove A, sobra [B]\n4. Enqueue(C): [B, C]\n5. Dequeue(): remove B, sobra [C].'
    ],
    [
        'enunciado' => 'O que acontece ao tentar executar a operação Dequeue() ou Peek() em uma fila que está totalmente vazia?',
        'alternativas' => [
            'A' => 'Retorna o valor 0 por padrão.',
            'B' => 'Adiciona um nó nulo no final.',
            'C' => 'Lança uma exceção (como InvalidOperationException) ou erro indicando fila vazia.',
            'D' => 'Reinicia a estrutura da fila.'
        ],
        'resposta_correta' => 'C',
        'dificuldade' => 'facil',
        'explicacao' => 'Tentar remover ou consultar o topo de uma fila sem elementos gera uma exceção/erro de operação inválida.'
    ]
];
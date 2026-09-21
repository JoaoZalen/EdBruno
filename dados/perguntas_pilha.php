<?php
/**
 * Banco de perguntas sobre Pilha Encadeada.
 *
 * Arquivo independente: não depende de sessão, banco de dados ou header.
 * Para usar, basta incluir e ler a variável $perguntas_pilha, ou capturar
 * o valor retornado pelo include:
 *
 *     $perguntas = include 'dados/perguntas_pilha.php';
 *
 * Formato de cada registro:
 *   tema           => assunto da pergunta dentro do conteúdo de Pilha
 *   enunciado      => texto da pergunta
 *   codigo         => trecho de código C# associado (string vazia quando teórica)
 *   alternativas   => vetor com as chaves 'a', 'b', 'c' e 'd'
 *   resposta       => chave da alternativa correta
 *   dificuldade    => 'facil', 'medio' ou 'dificil'
 *   explicacao     => justificativa da resposta correta
 */

$perguntas_pilha = [

    [
        'tema'        => 'Princípio LIFO',
        'enunciado'   => 'O princípio que rege o funcionamento de uma Pilha é o LIFO. O que ele significa?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'O primeiro elemento inserido é o primeiro a ser removido.',
            'b' => 'O último elemento inserido é o primeiro a ser removido.',
            'c' => 'Os elementos são removidos em ordem crescente de valor.',
            'd' => 'Qualquer elemento pode ser removido diretamente pelo índice.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'facil',
        'explicacao'  => 'LIFO é a sigla de Last In, First Out: o último a entrar é o primeiro a sair. A ordem descrita na alternativa "a" é FIFO, característica da Fila, e não da Pilha.',
    ],

    [
        'tema'        => 'Estrutura do nó',
        'enunciado'   => 'Em uma Pilha Encadeada de inteiros, quais campos um nó precisa ter?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'Apenas o dado armazenado.',
            'b' => 'O dado e uma referência para o próximo nó.',
            'c' => 'O dado e referências para o nó anterior e o próximo.',
            'd' => 'O dado, o índice da posição e o tamanho da pilha.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'facil',
        'explicacao'  => 'O nó guarda o dado e a referência para o próximo nó, que é o elemento abaixo dele na pilha. A referência para o anterior existe na lista duplamente encadeada, mas é desnecessária na pilha, que nunca percorre no sentido contrário.',
    ],

    [
        'tema'        => 'Referência para o topo',
        'enunciado'   => 'Como uma Pilha Encadeada identifica que está vazia?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'Quando a referência topo é igual a null.',
            'b' => 'Quando o último nó aponta para ele mesmo.',
            'c' => 'Quando o campo Dado do topo é igual a zero.',
            'd' => 'Quando o vetor interno atinge a capacidade máxima.',
        ],
        'resposta'    => 'a',
        'dificuldade' => 'facil',
        'explicacao'  => 'O topo é o único ponto de acesso da pilha; se ele não aponta para nenhum nó (null), não há elementos. O dado valer zero não diz nada sobre a pilha estar vazia, pois zero é um valor válido.',
    ],

    [
        'tema'        => 'Operação Peek',
        'enunciado'   => 'Qual é a diferença entre as operações Pop e Peek (Top)?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'Pop consulta o topo e Peek consulta a base da pilha.',
            'b' => 'Pop remove e devolve o topo; Peek apenas devolve o topo, sem removê-lo.',
            'c' => 'As duas removem o topo, mas Peek não devolve o valor.',
            'd' => 'Pop funciona em pilha vazia e Peek não.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'facil',
        'explicacao'  => 'Peek é uma consulta: não altera ponteiros nem a quantidade de elementos. Pop altera a estrutura, movendo o topo para o nó seguinte. Ambas falham quando a pilha está vazia.',
    ],

    [
        'tema'        => 'Sequência de operações',
        'enunciado'   => 'Uma pilha recebe Push(5), Push(8), Push(12) e, em seguida, um Pop(). Qual valor está no topo depois dessas operações?',
        'codigo'      => '',
        'alternativas' => [
            'a' => '5',
            'b' => '8',
            'c' => '12',
            'd' => 'A pilha fica vazia.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'facil',
        'explicacao'  => 'Após os três Push a pilha é 12 → 8 → 5, com 12 no topo. O Pop remove o 12 (o último inserido), deixando o 8 como novo topo e o 5 na base.',
    ],

    [
        'tema'        => 'Complexidade',
        'enunciado'   => 'Qual é a complexidade de tempo das operações Push, Pop e Peek em uma Pilha Encadeada?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'O(1) para todas as três.',
            'b' => 'O(n) para todas as três.',
            'c' => 'O(1) para Push e O(n) para Pop e Peek.',
            'd' => 'O(log n) para todas as três.',
        ],
        'resposta'    => 'a',
        'dificuldade' => 'medio',
        'explicacao'  => 'As três operações mexem apenas no nó apontado pelo topo, sem percorrer a estrutura, então o custo não depende da quantidade de elementos. Percorrer ou buscar um valor, aí sim, custa O(n).',
    ],

    [
        'tema'        => 'Operação Push',
        'enunciado'   => 'Analise o método Push abaixo. Qual é o efeito da ordem em que as duas atribuições aparecem?',
        'codigo'      => "public void Push(int dado)\n{\n    No novo = new No(dado);\n\n    novo.Proximo = topo;\n    topo         = novo;\n\n    quantidade++;\n}",
        'alternativas' => [
            'a' => 'A ordem é indiferente: o resultado seria o mesmo se as linhas fossem trocadas.',
            'b' => 'Se as linhas fossem trocadas, o topo antigo seria perdido e todos os nós anteriores ficariam inacessíveis.',
            'c' => 'Se as linhas fossem trocadas, apenas o contador quantidade ficaria errado.',
            'd' => 'A ordem impede que a pilha aceite o primeiro elemento quando ela está vazia.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'medio',
        'explicacao'  => 'É preciso ligar o novo nó ao topo atual antes de mover o topo. Invertendo as linhas, topo passaria a ser o novo nó e novo.Proximo apontaria para ele mesmo, perdendo a referência para o restante da pilha.',
    ],

    [
        'tema'        => 'Operação Pop',
        'enunciado'   => 'No método Pop abaixo, por que o valor é guardado em uma variável antes da linha que altera o topo?',
        'codigo'      => "public int Pop()\n{\n    if (topo == null)\n    {\n        throw new InvalidOperationException(\"A pilha está vazia.\");\n    }\n\n    int valor = topo.Dado;\n\n    topo = topo.Proximo;\n\n    quantidade--;\n\n    return valor;\n}",
        'alternativas' => [
            'a' => 'Para economizar memória durante a remoção.',
            'b' => 'Porque depois de mover o topo o nó removido não é mais alcançável pela pilha, e seu dado não poderia ser lido.',
            'c' => 'Porque o C# não permite retornar um campo de objeto diretamente.',
            'd' => 'Para que o contador quantidade seja atualizado corretamente.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'medio',
        'explicacao'  => 'Assim que topo recebe topo.Proximo, a pilha deixa de referenciar o nó removido. O dado precisa ser copiado antes para poder ser devolvido ao chamador.',
    ],

    [
        'tema'        => 'Saída de código',
        'enunciado'   => 'Qual é a saída impressa pelo trecho abaixo, considerando a implementação encadeada com Push, Pop e Peek?',
        'codigo'      => "PilhaEncadeada pilha = new PilhaEncadeada();\n\npilha.Push(1);\npilha.Push(2);\npilha.Pop();\npilha.Push(3);\n\nConsole.WriteLine(pilha.Peek());",
        'alternativas' => [
            'a' => '1',
            'b' => '2',
            'c' => '3',
            'd' => '0',
        ],
        'resposta'    => 'c',
        'dificuldade' => 'medio',
        'explicacao'  => 'Depois de Push(1) e Push(2) a pilha é 2 → 1. O Pop remove o 2, restando apenas o 1. O Push(3) coloca o 3 no topo, e o Peek devolve 3 sem removê-lo.',
    ],

    [
        'tema'        => 'Pilha vazia',
        'enunciado'   => 'O que acontece ao executar o código abaixo com a implementação que verifica o topo antes de remover?',
        'codigo'      => "PilhaEncadeada pilha = new PilhaEncadeada();\n\npilha.Push(7);\npilha.Pop();\npilha.Pop();",
        'alternativas' => [
            'a' => 'O segundo Pop devolve 7 novamente.',
            'b' => 'O segundo Pop devolve 0, por ser o valor padrão de int.',
            'c' => 'O segundo Pop lança InvalidOperationException, pois a pilha já está vazia.',
            'd' => 'O segundo Pop reinicia a pilha sem efeito algum.',
        ],
        'resposta'    => 'c',
        'dificuldade' => 'medio',
        'explicacao'  => 'O primeiro Pop remove o único elemento e deixa topo com null. No segundo Pop a verificação inicial detecta a pilha vazia e lança a exceção — situação conhecida como stack underflow. Por isso chama-se IsEmpty() antes de desempilhar.',
    ],

    [
        'tema'        => 'Aplicações',
        'enunciado'   => 'Qual das situações abaixo NÃO é naturalmente resolvida por uma Pilha?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'Desfazer a última ação em um editor de texto (Ctrl+Z).',
            'b' => 'Controlar as chamadas de função e o retorno da recursão.',
            'c' => 'Atender pedidos em uma fila de impressão, na ordem em que chegaram.',
            'd' => 'Verificar se os parênteses de uma expressão estão balanceados.',
        ],
        'resposta'    => 'c',
        'dificuldade' => 'facil',
        'explicacao'  => 'Atender na ordem de chegada é comportamento FIFO, próprio da Fila. Desfazer ações, controlar a call stack e validar símbolos dependem de tratar primeiro o elemento mais recente, ou seja, LIFO.',
    ],

    [
        'tema'        => 'Pilha encadeada x pilha em vetor',
        'enunciado'   => 'Qual é a principal vantagem da Pilha Encadeada em relação a uma pilha implementada sobre um vetor de tamanho fixo?',
        'codigo'      => '',
        'alternativas' => [
            'a' => 'Permite acessar qualquer elemento pelo índice em tempo constante.',
            'b' => 'Cresce dinamicamente, sem limite pré-definido nem necessidade de redimensionar e copiar elementos.',
            'c' => 'Consome menos memória por elemento armazenado.',
            'd' => 'Elimina a necessidade de verificar se a pilha está vazia antes de remover.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'medio',
        'explicacao'  => 'A versão encadeada aloca um nó por vez, então nunca "enche". Ela gasta mais memória por elemento (por causa da referência Proximo) e também não oferece acesso por índice, o que descarta as alternativas "a" e "c".',
    ],

    [
        'tema'        => 'Percurso da pilha',
        'enunciado'   => 'No método Exibir abaixo, por que é usada a variável auxiliar atual em vez de percorrer a pilha com o próprio campo topo?',
        'codigo'      => "public void Exibir()\n{\n    No atual = topo;\n\n    while (atual != null)\n    {\n        Console.Write(atual.Dado + \" -> \");\n        atual = atual.Proximo;\n    }\n\n    Console.WriteLine(\"NULL\");\n}",
        'alternativas' => [
            'a' => 'Porque usar topo no laço o deixaria em null ao final, esvaziando a pilha só para imprimi-la.',
            'b' => 'Porque o campo topo é somente leitura dentro da classe.',
            'c' => 'Porque a variável auxiliar torna o percurso O(1) em vez de O(n).',
            'd' => 'Porque o laço while não aceita campos de classe como condição.',
        ],
        'resposta'    => 'a',
        'dificuldade' => 'dificil',
        'explicacao'  => 'O laço avança a variável até null. Se essa variável fosse o próprio topo, a pilha perderia a referência para todos os nós e ficaria vazia após a impressão. O percurso continua sendo O(n) de qualquer forma.',
    ],

    [
        'tema'        => 'Inversão com pilha',
        'enunciado'   => 'Uma pilha A contém, do topo para a base, os valores 1, 2 e 3. Desempilhando todos os elementos de A e empilhando cada um deles em uma pilha B, qual será o conteúdo de B, do topo para a base?',
        'codigo'      => "while (!a.IsEmpty())\n{\n    b.Push(a.Pop());\n}",
        'alternativas' => [
            'a' => '1, 2, 3',
            'b' => '3, 2, 1',
            'c' => '2, 1, 3',
            'd' => 'B fica vazia.',
        ],
        'resposta'    => 'b',
        'dificuldade' => 'dificil',
        'explicacao'  => 'Os Pop em A devolvem 1, 2 e 3 nessa ordem, e cada valor vai para o topo de B. O último empilhado (3) fica no topo de B, resultando em 3, 2, 1 — a transferência entre duas pilhas inverte a ordem original.',
    ],

];

return $perguntas_pilha;

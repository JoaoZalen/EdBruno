<?php
$perguntas_quiz = [
    [
        'tema' => 'TAD',
        'dificuldade' => 'Fácil',
        'moedas' => 5,
        'enunciado' => 'O que um Tipo Abstrato de Dados define?',
        'alternativas' => [
            'A' => 'Apenas as cores da interface.',
            'B' => 'Dados e operações, escondendo detalhes internos.',
            'C' => 'Somente tabelas SQL.',
            'D' => 'Apenas comandos de impressão.'
        ],
        'correta' => 'B',
        'explicacao' => 'O TAD descreve comportamento e operações sem obrigar o usuário a conhecer a implementação.'
    ],
    [
        'tema' => 'Lista Simples',
        'dificuldade' => 'Média',
        'moedas' => 10,
        'enunciado' => 'Em uma lista simplesmente encadeada, cada nó possui normalmente:',
        'alternativas' => [
            'A' => 'Valor e próximo.',
            'B' => 'Valor, anterior e próximo.',
            'C' => 'Apenas prioridade.',
            'D' => 'Somente índice fixo.'
        ],
        'correta' => 'A',
        'explicacao' => 'A lista simples armazena um dado e uma referência para o próximo nó.'
    ],
    [
        'tema' => 'Lista Dupla',
        'dificuldade' => 'Média',
        'moedas' => 10,
        'enunciado' => 'Qual é a vantagem do ponteiro anterior na lista dupla?',
        'alternativas' => [
            'A' => 'Eliminar memória.',
            'B' => 'Permitir navegação nos dois sentidos.',
            'C' => 'Impedir remoções.',
            'D' => 'Ordenar automaticamente.'
        ],
        'correta' => 'B',
        'explicacao' => 'A referência anterior permite voltar na lista, facilitando algumas operações.'
    ],
    [
        'tema' => 'Pilha',
        'dificuldade' => 'Fácil',
        'moedas' => 5,
        'enunciado' => 'Qual regra uma pilha encadeada segue?',
        'alternativas' => [
            'A' => 'FIFO.',
            'B' => 'LIFO.',
            'C' => 'Ordem alfabética.',
            'D' => 'Prioridade menor primeiro.'
        ],
        'correta' => 'B',
        'explicacao' => 'Pilha usa LIFO: o último elemento inserido é o primeiro removido.'
    ],
    [
        'tema' => 'Fila FIFO',
        'dificuldade' => 'Fácil',
        'moedas' => 5,
        'enunciado' => 'Em uma fila FIFO, qual elemento é removido primeiro?',
        'alternativas' => [
            'A' => 'O último inserido.',
            'B' => 'O primeiro inserido.',
            'C' => 'O maior valor.',
            'D' => 'O menor valor.'
        ],
        'correta' => 'B',
        'explicacao' => 'FIFO significa First-In, First-Out.'
    ],
    [
        'tema' => 'Fila FIFO',
        'dificuldade' => 'Média',
        'moedas' => 10,
        'enunciado' => 'No C#, após remover o último elemento da fila encadeada, qual ponteiro também precisa virar null?',
        'alternativas' => [
            'A' => 'fim.',
            'B' => 'valor.',
            'C' => 'prioridade.',
            'D' => 'senha.'
        ],
        'correta' => 'A',
        'explicacao' => 'Quando a fila fica vazia, início e fim devem apontar para null.'
    ],
    [
        'tema' => 'Fila de Prioridade',
        'dificuldade' => 'Difícil',
        'moedas' => 20,
        'enunciado' => 'Na fila de prioridade encadeada, por que inserir depois dos nós de mesma prioridade?',
        'alternativas' => [
            'A' => 'Para apagar empates.',
            'B' => 'Para preservar FIFO entre elementos empatados.',
            'C' => 'Para inverter a fila.',
            'D' => 'Para impedir remoção.'
        ],
        'correta' => 'B',
        'explicacao' => 'Se dois itens têm a mesma prioridade, quem chegou primeiro deve continuar antes.'
    ],
    [
        'tema' => 'Código C#',
        'dificuldade' => 'Difícil',
        'moedas' => 20,
        'enunciado' => 'No trecho "novo.Proximo = inicio; inicio = novo;", qual operação está acontecendo?',
        'alternativas' => [
            'A' => 'Inserção no início de uma estrutura encadeada.',
            'B' => 'Busca binária.',
            'C' => 'Remoção do último nó.',
            'D' => 'Ordenação por bolha.'
        ],
        'correta' => 'A',
        'explicacao' => 'O novo nó aponta para o antigo início e passa a ser o novo início.'
    ],
];
?>

<?php include 'session.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fila Encadeada FIFO - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/fila_fifo.css?v=1">
</head>
<body>
<?php include 'header.php'; ?>

<div class="pagina-banner">
    <h1>Fila Encadeada FIFO</h1>
    <p>Compreenda o funcionamento do princípio First-In, First-Out (FIFO), a gestão de ponteiros de início e fim, as operações fundamentais e a implementação prática em C#.</p>
</div>

<main class="pagina-conteudo">
    <section class="secao">
        <h2 class="secao-titulo">O que é uma Fila Encadeada?</h2>
        <p>Uma <strong>Fila Encadeada</strong> é uma estrutura de dados dinâmica linear composta por nós conectados através de ponteiros/referências. Diferente de uma lista estática baseada em vetores (arrays), a fila encadeada não necessita de um tamanho fixo pré-definido na memória, permitindo que novos elementos sejam alocados dinamicamente à medida que são inseridos.</p>
        <div class="destaque">
            <strong>Regra Fundamental:</strong> Na Fila Encadeada FIFO, as inserções acontecem exclusivamente no final (cauda/rear) e as remoções ocorrem obrigatoriamente no início (cabeça/front).
        </div>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Funcionamento do Princípio FIFO</h2>
        <p>O termo <strong>FIFO</strong> é uma sigla para <em>First-In, First-Out</em> (Primeiro a Entrar, Primeiro a Sair). Isso significa que o primeiro elemento a ser inserido na fila será obrigatoriamente o primeiro elemento a ser removido.</p>
        <p>Pense numa fila de atendimento de banco ou caixa de supermercado: a primeira pessoa que chega é a primeira a ser atendida. As novas pessoas entram sempre no final da fila.</p>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Estrutura do Nó e Ponteiros de Controle</h2>
        <p>Cada elemento da fila encadeada é representado por um <strong>Nó</strong> (Node). O nó possui dois campos principais:</p>
        <ul>
            <li><strong>Dado (Valor):</strong> A informação armazenada no nó (ex: um número, um objeto, uma string).</li>
            <li><strong>Próximo (Next):</strong> Uma referência/ponteiro para o próximo nó da fila. O último nó aponta para <code>null</code>.</li>
        </ul>
        <p>Para gerenciar a fila com eficiência $O(1)$, mantemos duas referências principais na classe da fila:</p>
        <ul>
            <li><strong><code>inicio</code> (Front/Head):</strong> Aponta para o primeiro nó da fila (onde ocorrem as remoções).</li>
            <li><strong><code>fim</code> (Rear/Tail):</strong> Aponta para o último nó da fila (onde ocorrem as inserções).</li>
        </ul>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Representação Visual da Fila</h2>
        <div class="secao-imagem">
            <img src="../imgs/fila_fifo/estrutura_fila.svg" alt="Diagrama da Fila Encadeada identificando Início, Fim e Ponteiros">
            <figcaption>Figura 1: Representação de uma Fila Encadeada mostrando os ponteiros 'inicio' e 'fim'.</figcaption>
        </div>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Operações Principais</h2>
        
        <h3>1. Inserção (Enqueue)</h3>
        <p>Adiciona um novo elemento ao final da fila. Cria-se um novo nó; se a fila estiver vazia, tanto <code>inicio</code> quanto <code>fim</code> passarão a apontar para ele. Caso contrário, o ponteiro <code>Proximo</code> do nó que atualmente é o <code>fim</code> passa a apontar para o novo nó, e o ponteiro <code>fim</code> é atualizado.</p>

        <h3>2. Remoção (Dequeue)</h3>
        <p>Remove o elemento do início da fila e retorna o seu valor. O ponteiro <code>inicio</code> é atualizado para apontar para <code>inicio.Proximo</code>. Caso a fila se torne vazia após essa remoção, o ponteiro <code>fim</code> também deve ser definido como <code>null</code>.</p>

        <h3>3. Consulta do Início (Peek / Front)</h3>
        <p>Retorna o valor armazenado no nó do início (<code>inicio.Valor</code>) sem remover o elemento da fila. Lança uma exceção ou retorna aviso caso a fila esteja vazia.</p>

        <h3>4. Verificação de Vazia (IsEmpty)</h3>
        <p>Verifica se a fila não possui elementos, checando simplesmente se <code>inicio == null</code>.</p>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Sequência de Operações (Enqueue e Dequeue)</h2>
        <div class="secao-imagem">
            <img src="../imgs/fila_fifo/operacoes_enqueue_dequeue.svg" alt="Diagrama da Sequência de Enqueue e Dequeue">
            <figcaption>Figura 2: Passo a passo visual de inserção (Enqueue) e remoção (Dequeue) na fila.</figcaption>
        </div>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Demonstração Passo a Passo com Valores</h2>
        <p>Considere uma fila inicialmente vazia e a seguinte sequência de ações com os valores <strong>10</strong>, <strong>20</strong> e <strong>30</strong>:</p>
        <ol>
            <li><strong>Enqueue(10):</strong> A fila cria o nó [10]. Como é o primeiro, <code>inicio</code> e <code>fim</code> apontam para [10]. <br><em>Fila: [10]</em></li>
            <li><strong>Enqueue(20):</strong> Cria o nó [20]. O nó [10] aponta para [20]. O ponteiro <code>fim</code> passa para [20]. <br><em>Fila: [10] -> [20]</em></li>
            <li><strong>Enqueue(30):</strong> Cria o nó [30]. O nó [20] aponta para [30]. O ponteiro <code>fim</code> passa para [30]. <br><em>Fila: [10] -> [20] -> [30]</em></li>
            <li><strong>Dequeue():</strong> O nó do início [10] é removido e retornado. O ponteiro <code>inicio</code> avança para [20]. <br><em>Fila: [20] -> [30]</em></li>
            <li><strong>Peek():</strong> Retorna o valor 20 sem alterar a fila.</li>
        </ol>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Implementação Completa em C#</h2>
        <p>Abaixo está a implementação própria de uma Fila Encadeada em C# utilizando nós customizados (sem utilizar a classe utilitária <code>System.Collections.Generic.Queue&lt;T&gt;</code>):</p>

        <div class="bloco-codigo">
            <div class="codigo-label">C# - FilaEncadeada.cs</div>
<pre><code>using System;

namespace EstruturaDeDados
{
    // Classe que representa cada elemento (Nó) da fila
    public class No&lt;T&gt;
    {
        public T Valor { get; set; }
        public No&lt;T&gt; Proximo { get; set; }

        public No(T valor)
        {
            Valor = valor;
            Proximo = null;
        }
    }

    // Classe da Fila Encadeada FIFO
    public class FilaEncadeada&lt;T&gt;
    {
        private No&lt;T&gt; inicio;
        private No&lt;T&gt; fim;
        public int Quantidade { get; private; }

        public FilaEncadeada()
        {
            inicio = null;
            fim = null;
            Quantidade = 0;
        }

        // Verifica se a fila está vazia
        public bool IsEmpty()
        {
            return inicio == null;
        }

        // Enqueue: Insere um novo elemento no final da fila
        public void Enqueue(T valor)
        {
            No&lt;T&gt; novoNo = new No&lt;T&gt;(valor);

            if (IsEmpty())
            {
                inicio = novoNo;
                fim = novoNo;
            }
            else
            {
                fim.Proximo = novoNo;
                fim = novoNo;
            }

            Quantidade++;
        }

        // Dequeue: Remove e retorna o elemento do início da fila
        public T Dequeue()
        {
            if (IsEmpty())
            {
                throw new InvalidOperationException("A fila está vazia.");
            }

            T valorInicio = inicio.Valor;
            inicio = inicio.Proximo;

            // Tratamento obrigatório: se a fila esvaziou, ajusta o fim para null
            if (inicio == null)
            {
                fim = null;
            }

            Quantidade--;
            return valorInicio;
        }

        // Peek: Retorna o elemento do início sem remover
        public T Peek()
        {
            if (IsEmpty())
            {
                throw new InvalidOperationException("A fila está vazia.");
            }

            return inicio.Valor;
        }
    }
}</code></pre>
        </div>

        <p><strong>Explicação do Código:</strong> A classe <code>No&lt;T&gt;</code> armazena o dado genérico e o ponteiro para o próximo item. A classe <code>FilaEncadeada&lt;T&gt;</code> mantém as referências <code>inicio</code> e <code>fim</code>. Na inserção (<code>Enqueue</code>), se a fila está vazia, ambos os ponteiros recebem o novo nó; caso contrário, encadeamos o novo nó no final existente. Na remoção (<code>Dequeue</code>), o nó inicial é desconectado e o ponteiro avança. Caso o início passe a ser <code>null</code>, garantimos que o ponteiro de fim também é ajustado para <code>null</code> para evitar inconsistências na memória.</p>
    </section>

    <div class="divisor"></div>

    <section class="secao">
        <h2 class="secao-titulo">Vantagens e Desvantagens</h2>
        <table class="tabela-comparativa" style="width:100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th style="padding: 8px; border: 1px solid #ddd;">Vantagens</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Desvantagens</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">Alocação dinâmica: cresce e diminui conforme a necessidade sem limite fixo.</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">Gasto adicional de memória para armazenar o ponteiro de referência em cada nó.</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">Operações de inserção e remoção extremamente rápidas e de tempo constante $O(1)$.</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">Acesso aleatório não permitido (não é possível acessar o n-ésimo elemento diretamente por índice).</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Complexidade das Operações (Big-O)</h2>
        <ul>
            <li><strong>Enqueue (Inserção):</strong> $O(1)$ - Tempo constante, pois temos o ponteiro direto para o <code>fim</code>.</li>
            <li><strong>Dequeue (Remoção):</strong> $O(1)$ - Tempo constante, pois temos o ponteiro direto para o <code>inicio</code>.</li>
            <li><strong>Peek (Consulta):</strong> $O(1)$ - Tempo constante.</li>
            <li><strong>Busca de Elemento:</strong> $O(n)$ - Necessita percorrer nó por nó do início ao fim.</li>
        </ul>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Aplicações Reais</h2>
        <ul>
            <li><strong>Sistemas de Impressão:</strong> Os documentos enviados para a impressora entram numa fila e são impressos exatamente na ordem de envio.</li>
            <li><strong>Sistemas Operacionais:</strong> Escalonamento de processos (processamento de tarefas no estilo First-Come, First-Served).</li>
            <li><strong>Atendimento ao Cliente:</strong> Sistemas de tickets de chamados e filas virtuais de atendimento.</li>
            <li><strong>Redes de Computadores:</strong> Buffers de pacotes em roteadores e filas de mensagens (ex: RabbitMQ, Kafka).</li>
        </ul>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Conclusão</h2>
        <p>A Fila Encadeada FIFO é uma estrutura indispensável na ciência da computação para organizar tarefas sequenciais onde a ordem de chegada deve ser estritamente respeitada. Com alocação dinâmica e operações de inserção e remoção com complexidade $O(1)$, ela oferece alta performance para problemas de processamento em ordem cronológica.</p>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
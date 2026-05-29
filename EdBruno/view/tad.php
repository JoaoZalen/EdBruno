<?php
include 'session.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAD - Tipo Abstrato de Dados</title>

    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/tad.css?v=10">

</head>

<body>

<?php include 'header.php'; ?>

<main class="tad-container">

    <section class="tad-hero">

        <div class="tad-hero-text">

            <span class="tag">Estruturas de Dados</span>

            <h1>
                TAD — Tipo Abstrato de Dados
            </h1>

            <p>
                Um Tipo Abstrato de Dados é uma forma de organizar informações
                definindo quais operações podem ser feitas, sem se preocupar
                inicialmente com a forma como essas operações são implementadas.
            </p>

        </div>

        <div class="tad-hero-card">

<pre>
TAD Pilha

Operações:
- Inserir
- Remover
- Consultar topo
- Verificar se está vazia
</pre>

        </div>

    </section>

    <section class="tad-section">

        <h2>O que é um TAD?</h2>

        <p>
            TAD significa <strong>Tipo Abstrato de Dados</strong>. Ele representa
            uma estrutura que define um conjunto de dados e as operações que podem
            ser realizadas sobre eles.
        </p>

        <p>
            A palavra “abstrato” significa que o usuário da estrutura não precisa
            conhecer os detalhes internos de funcionamento. Ele precisa apenas saber
            quais comandos pode usar.
        </p>

    </section>

    <section class="tad-section">

        <h2>Por que o TAD é importante?</h2>

        <p>
            O TAD é importante porque ajuda a separar a ideia principal de uma estrutura
            de dados da sua implementação. Isso torna o programa mais organizado,
            fácil de entender e mais simples de modificar no futuro.
        </p>

        <p>
            Por exemplo, uma pilha pode ser implementada usando vetor, lista encadeada
            ou outra estrutura interna. Mesmo assim, para quem utiliza a pilha, as
            operações continuam sendo as mesmas.
        </p>

    </section>

    <section class="tad-section">

        <h2>Características de um TAD</h2>

        <div class="info-grid">

            <div class="info-card">
                <h3>Abstração</h3>
                <p>
                    Mostra apenas o que é necessário para usar a estrutura,
                    escondendo os detalhes internos.
                </p>
            </div>

            <div class="info-card">
                <h3>Encapsulamento</h3>
                <p>
                    Protege os dados internos e permite acesso apenas por meio
                    das operações definidas.
                </p>
            </div>

            <div class="info-card">
                <h3>Reutilização</h3>
                <p>
                    Permite usar a mesma estrutura em diferentes partes de um sistema.
                </p>
            </div>

            <div class="info-card">
                <h3>Organização</h3>
                <p>
                    Facilita a divisão do código em partes menores e mais compreensíveis.
                </p>
            </div>

        </div>

    </section>

    <section class="tad-section">

        <h2>Exemplo simples</h2>

        <p>
            Imagine uma <strong>pilha de pratos</strong>. Você só consegue colocar
            um prato no topo ou retirar o prato que está no topo.
        </p>

        <div class="visual-stack">

            <div class="stack-item">Prato 3</div>
            <div class="stack-item">Prato 2</div>
            <div class="stack-item">Prato 1</div>

        </div>

        <p>
            Esse comportamento é chamado de <strong>LIFO</strong>, ou seja,
            o último elemento que entra é o primeiro que sai.
        </p>

    </section>

    <section class="tad-section">

        <h2>TAD x Implementação</h2>

        <p>
            O TAD define <strong>o que a estrutura faz</strong>. A implementação
            define <strong>como ela faz</strong>.
        </p>

        <div class="comparacao">

            <div>
                <h3>TAD</h3>
                <p>Define as operações disponíveis.</p>
                <p>Exemplo: inserir, remover, buscar e consultar.</p>
            </div>

            <div>
                <h3>Implementação</h3>
                <p>Define o código interno da estrutura.</p>
                <p>Exemplo: usar vetor, lista, classe ou ponteiros.</p>
            </div>

        </div>

    </section>

    <section class="tad-section">

        <h2>Fluxo de funcionamento</h2>

        <p>
            O usuário interage com as operações do TAD, sem precisar acessar diretamente
            a memória ou os detalhes internos da estrutura.
        </p>

        <div class="fluxo-tad">

            <div class="fluxo-item">Usuário</div>
            <div class="fluxo-seta">↓</div>
            <div class="fluxo-item">Operações do TAD</div>
            <div class="fluxo-seta">↓</div>
            <div class="fluxo-item">Implementação Interna</div>
            <div class="fluxo-seta">↓</div>
            <div class="fluxo-item">Dados na Memória</div>

        </div>

    </section>

    <section class="tad-section">

        <h2>Principais exemplos de TADs</h2>

        <p>
            Diversas estruturas estudadas em Estruturas de Dados podem ser entendidas
            como Tipos Abstratos de Dados.
        </p>

        <ul class="lista-topicos">
            <li><strong>Pilha:</strong> segue o princípio LIFO, onde o último elemento inserido é o primeiro a sair.</li>
            <li><strong>Fila:</strong> segue o princípio FIFO, onde o primeiro elemento inserido é o primeiro a sair.</li>
            <li><strong>Lista:</strong> armazena elementos em sequência, permitindo inserção, remoção e busca.</li>
            <li><strong>Árvore:</strong> organiza dados de forma hierárquica.</li>
            <li><strong>Grafo:</strong> representa relações entre elementos por meio de vértices e arestas.</li>
        </ul>

    </section>

    <section class="tad-section">

        <h2>Vantagens dos TADs</h2>

        <ul class="lista-topicos">
            <li>Facilitam a organização do código.</li>
            <li>Permitem separar lógica e implementação.</li>
            <li>Ajudam na reutilização de estruturas.</li>
            <li>Melhoram a manutenção do sistema.</li>
            <li>Tornam o programa mais fácil de entender.</li>
        </ul>

    </section>

    <section class="tad-section">

        <h2>Desvantagens dos TADs</h2>

        <ul class="lista-topicos">
            <li>Podem exigir mais planejamento antes da implementação.</li>
            <li>Algumas estruturas são mais difíceis de programar.</li>
            <li>Dependendo da implementação, podem consumir mais memória.</li>
            <li>Para iniciantes, o conceito de abstração pode ser mais difícil no começo.</li>
        </ul>

    </section>

    <section class="tad-section">

        <h2>Exemplo em C# — Pilha</h2>

        <p>
            Abaixo temos um exemplo simples de uma pilha implementada em C#.
        </p>

<pre class="codigo">
using System;
using System.Collections.Generic;

class Pilha
{
    private List&lt;int&gt; elementos = new List&lt;int&gt;();

    public void Inserir(int valor)
    {
        elementos.Add(valor);
    }

    public void Remover()
    {
        if(elementos.Count > 0)
        {
            elementos.RemoveAt(elementos.Count - 1);
        }
    }

    public int Topo()
    {
        return elementos[elementos.Count - 1];
    }

    public bool EstaVazia()
    {
        return elementos.Count == 0;
    }
}
</pre>

    </section>

    <section class="tad-section">

        <h2>Exemplo em C# — Conta Bancária</h2>

        <p>
            Outro exemplo de TAD pode ser uma conta bancária. O usuário pode depositar,
            sacar e consultar saldo, mas não acessa diretamente a variável interna
            que guarda o saldo.
        </p>

<pre class="codigo">
class ContaBancaria
{
    private double saldo;

    public void Depositar(double valor)
    {
        if(valor > 0)
        {
            saldo += valor;
        }
    }

    public void Sacar(double valor)
    {
        if(valor > 0 && valor <= saldo)
        {
            saldo -= valor;
        }
    }

    public double ConsultarSaldo()
    {
        return saldo;
    }
}
</pre>

        <p>
            Nesse exemplo, o atributo <strong>saldo</strong> está protegido. Ele só pode
            ser alterado pelos métodos definidos na classe, mostrando o conceito de
            encapsulamento.
        </p>

    </section>

    <section class="tad-section">

        <h2>Onde os TADs são utilizados?</h2>

        <p>
            Os Tipos Abstratos de Dados aparecem em várias áreas da computação e são
            base para sistemas mais complexos.
        </p>

        <ul class="lista-topicos">
            <li>Sistemas bancários.</li>
            <li>Jogos digitais.</li>
            <li>Aplicativos móveis.</li>
            <li>Sistemas operacionais.</li>
            <li>Bancos de dados.</li>
            <li>Redes sociais.</li>
            <li>Sites e sistemas web.</li>
        </ul>

    </section>

    <section class="tad-section conclusao">

        <h2>Conclusão</h2>

        <p>
            O TAD é importante porque permite pensar primeiro na lógica da estrutura
            e depois na forma de implementação. Ele facilita a organização do código,
            melhora a reutilização e torna o sistema mais fácil de entender.
        </p>

        <p>
            Esse conceito serve como base para outras estruturas estudadas, como
            listas simplesmente encadeadas, listas duplamente encadeadas, pilhas,
            filas, árvores e grafos.
        </p>

    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
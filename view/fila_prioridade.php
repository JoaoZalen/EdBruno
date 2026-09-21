<?php include 'session.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fila de Prioridade - ED Ensino</title>
    <link rel="stylesheet" href="../css/header.css?v=2">
    <link rel="stylesheet" href="../css/footer.css?v=2">
    <link rel="stylesheet" href="../css/fila_prioridade.css?v=1">
</head>
<body>
<?php include 'header.php'; ?>

<div class="pagina-banner">
    <h1>Fila de Prioridade Encadeada</h1>
    <p>Entenda como uma fila pode atender elementos pela prioridade, preservando a ordem de chegada quando as prioridades são iguais.</p>
</div>

<main class="pagina-conteudo">
    <section class="secao">
        <h2 class="secao-titulo">O que é?</h2>
        <p>Uma <strong>Fila de Prioridade Encadeada</strong> é uma estrutura dinâmica formada por nós. Cada nó armazena um valor, uma prioridade e uma referência para o próximo nó.</p>
        <p>Diferente da fila FIFO comum, a remoção não considera apenas quem chegou primeiro. O elemento com maior prioridade fica mais próximo do início.</p>
        <div class="destaque"><strong>Regra importante:</strong> quando dois elementos possuem a mesma prioridade, a estrutura mantém FIFO entre eles.</div>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Representação</h2>
        <div class="secao-imagem">inicio -> [Emergência | prioridade 3] -> [Idoso | prioridade 2] -> [Consulta | prioridade 1] -> null</div>
        <p>O início guarda o próximo elemento a ser atendido. A inserção percorre os nós até encontrar a posição correta.</p>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Inserção ordenada</h2>
        <p>Ao inserir, o novo nó deve ficar antes dos nós com prioridade menor e depois dos nós com prioridade igual. Assim, a ordem FIFO é preservada nos empates.</p>
        <div class="secao-imagem">Chegada: A(P2), B(P2), C(P3)
Resultado: C(P3) -> A(P2) -> B(P2)</div>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Operações principais</h2>
        <ul>
            <li><strong>Inserir:</strong> posiciona o nó conforme a prioridade.</li>
            <li><strong>Remover:</strong> remove o nó do início.</li>
            <li><strong>Consultar:</strong> lê o início sem remover.</li>
            <li><strong>EstaVazia:</strong> verifica se o início é nulo.</li>
        </ul>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Exemplo em C#</h2>
        <div class="bloco-codigo">
            <div class="codigo-label">C# - FilaPrioridade.cs</div>
<pre><code>public class No
{
    public string Valor;
    public int Prioridade;
    public No Proximo;

    public No(string valor, int prioridade)
    {
        Valor = valor;
        Prioridade = prioridade;
    }
}

public class FilaPrioridade
{
    private No inicio;

    public void Inserir(string valor, int prioridade)
    {
        No novo = new No(valor, prioridade);

        if (inicio == null || prioridade > inicio.Prioridade)
        {
            novo.Proximo = inicio;
            inicio = novo;
            return;
        }

        No atual = inicio;

        while (atual.Proximo != null &&
               atual.Proximo.Prioridade >= prioridade)
        {
            atual = atual.Proximo;
        }

        novo.Proximo = atual.Proximo;
        atual.Proximo = novo;
    }

    public string Remover()
    {
        if (inicio == null)
            throw new InvalidOperationException("Fila vazia.");

        string valor = inicio.Valor;
        inicio = inicio.Proximo;
        return valor;
    }
}</code></pre>
        </div>
    </section>

    <section class="secao">
        <h2 class="secao-titulo">Complexidade</h2>
        <ul>
            <li><strong>Inserção:</strong> O(n), pois pode percorrer a fila.</li>
            <li><strong>Remoção:</strong> O(1), pois remove do início.</li>
            <li><strong>Consulta:</strong> O(1), pois lê o início.</li>
        </ul>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
